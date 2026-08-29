<?php

namespace App\Services;

use App\Models\RecurringRule;
use App\Notifications\SystemAlertNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RecurringTransactionService
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Process all due active recurring rules.
     *
     * @return array{processed: int, skipped: int, errors: array<int, string>}
     */
    public function processDueRules(?Carbon $asOfDate = null): array
    {
        $today = $asOfDate ?? Carbon::today();

        $dueRules = RecurringRule::with(['user', 'account', 'category'])
            ->where('is_active', true)
            ->whereDate('tanggal_berikutnya', '<=', $today->toDateString())
            ->get();

        $processed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($dueRules as $rule) {
            try {
                $tipe = $rule->category ? $rule->category->tipe : 'expense';

                $this->transactionService->createTransaction($rule->user, [
                    'account_id' => $rule->account_id,
                    'destination_account_id' => null,
                    'category_id' => $rule->category_id,
                    'jumlah' => $rule->jumlah,
                    'tipe' => $tipe,
                    'tanggal' => $today->toDateString(),
                    'catatan' => '[Otomatis] '.($rule->catatan ?: ($rule->category?->nama ?? 'Tagihan Berulang')),
                ]);

                // Calculate next date without month-overflow
                $rule->tanggal_berikutnya = $rule->getNextDate();
                $rule->save();

                $processed++;
            } catch (ValidationException|Exception $e) {
                $skipped++;
                $errorMsg = "Tagihan '{$rule->catatan}' gagal diproses: ".$e->getMessage();
                $errors[] = $errorMsg;

                Log::warning("Recurring rule ID {$rule->id} skipped: {$errorMsg}");

                // Notify the user in-app
                $rule->user->notify(new SystemAlertNotification([
                    'title' => 'Tagihan Otomatis Dilewati',
                    'message' => "Tagihan '{$rule->catatan}' (Rp ".number_format($rule->jumlah, 0, ',', '.').") pada akun '{$rule->account->nama_akun}' tidak dapat dibukukan karena saldo tidak mencukupi.",
                    'type' => 'warning',
                    'url' => route('recurring-rules.index'),
                ]));
            }
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }
}
