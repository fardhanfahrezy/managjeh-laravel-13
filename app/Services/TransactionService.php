<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    /**
     * Create a new transaction and update account balances atomically.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function createTransaction(User $user, array $data): Transaction
    {
        return DB::transaction(function () use ($user, $data) {
            $sourceAccount = Account::where('user_id', $user->id)
                ->where('id', $data['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $destinationAccount = null;
            if ($data['tipe'] === 'transfer') {
                $destinationAccount = Account::where('user_id', $user->id)
                    ->where('id', $data['destination_account_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $jumlah = (float) $data['jumlah'];

            // Validate negative balance rule
            if (in_array($data['tipe'], ['expense', 'transfer'], true)) {
                if (! $sourceAccount->allowsNegativeBalance() && ($sourceAccount->saldo - $jumlah) < 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => "Saldo akun '{$sourceAccount->nama_akun}' tidak mencukupi (Sisa saldo: Rp ".number_format($sourceAccount->saldo, 2, ',', '.').').',
                    ]);
                }
            }

            // Apply balance mutation
            if ($data['tipe'] === 'income') {
                $sourceAccount->saldo += $jumlah;
                $sourceAccount->save();
            } elseif ($data['tipe'] === 'expense') {
                $sourceAccount->saldo -= $jumlah;
                $sourceAccount->save();
            } elseif ($data['tipe'] === 'transfer') {
                $sourceAccount->saldo -= $jumlah;
                $sourceAccount->save();

                $destinationAccount->saldo += $jumlah;
                $destinationAccount->save();
            }

            return Transaction::create([
                'user_id' => $user->id,
                'account_id' => $sourceAccount->id,
                'destination_account_id' => $destinationAccount?->id,
                'category_id' => $data['category_id'] ?? null,
                'jumlah' => $jumlah,
                'tipe' => $data['tipe'],
                'tanggal' => $data['tanggal'],
                'catatan' => $data['catatan'] ?? null,
                'attachment_url' => $data['attachment_url'] ?? null,
            ]);
        });
    }

    /**
     * Update an existing transaction and recalculate account balances.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // 1. Rollback old balance impact
            $oldSourceAccount = Account::where('id', $transaction->account_id)->lockForUpdate()->first();
            $oldDestAccount = $transaction->destination_account_id
                ? Account::where('id', $transaction->destination_account_id)->lockForUpdate()->first()
                : null;

            $oldJumlah = (float) $transaction->jumlah;

            if ($oldSourceAccount) {
                if ($transaction->tipe === 'income') {
                    $oldSourceAccount->saldo -= $oldJumlah;
                    $oldSourceAccount->save();
                } elseif ($transaction->tipe === 'expense') {
                    $oldSourceAccount->saldo += $oldJumlah;
                    $oldSourceAccount->save();
                } elseif ($transaction->tipe === 'transfer') {
                    $oldSourceAccount->saldo += $oldJumlah;
                    $oldSourceAccount->save();

                    if ($oldDestAccount) {
                        $oldDestAccount->saldo -= $oldJumlah;
                        $oldDestAccount->save();
                    }
                }
            }

            // 2. Fetch new accounts with fresh locks
            $newSourceAccount = Account::where('user_id', $transaction->user_id)
                ->where('id', $data['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $newDestAccount = null;
            if ($data['tipe'] === 'transfer') {
                $newDestAccount = Account::where('user_id', $transaction->user_id)
                    ->where('id', $data['destination_account_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $newJumlah = (float) $data['jumlah'];

            // 3. Check negative balance constraints
            if (in_array($data['tipe'], ['expense', 'transfer'], true)) {
                if (! $newSourceAccount->allowsNegativeBalance() && ($newSourceAccount->saldo - $newJumlah) < 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => "Saldo akun '{$newSourceAccount->nama_akun}' tidak mencukupi (Sisa saldo: Rp ".number_format($newSourceAccount->saldo, 2, ',', '.').').',
                    ]);
                }
            }

            // 4. Apply new balance mutations
            if ($data['tipe'] === 'income') {
                $newSourceAccount->saldo += $newJumlah;
                $newSourceAccount->save();
            } elseif ($data['tipe'] === 'expense') {
                $newSourceAccount->saldo -= $newJumlah;
                $newSourceAccount->save();
            } elseif ($data['tipe'] === 'transfer') {
                $newSourceAccount->saldo -= $newJumlah;
                $newSourceAccount->save();

                $newDestAccount->saldo += $newJumlah;
                $newDestAccount->save();
            }

            // 5. Update transaction record
            $transaction->update([
                'account_id' => $newSourceAccount->id,
                'destination_account_id' => $newDestAccount?->id,
                'category_id' => $data['category_id'] ?? null,
                'jumlah' => $newJumlah,
                'tipe' => $data['tipe'],
                'tanggal' => $data['tanggal'],
                'catatan' => $data['catatan'] ?? null,
                'attachment_url' => $data['attachment_url'] ?? $transaction->attachment_url,
            ]);

            return $transaction;
        });
    }

    /**
     * Delete transaction and rollback its balance impact.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $sourceAccount = Account::where('id', $transaction->account_id)->lockForUpdate()->first();
            $destAccount = $transaction->destination_account_id
                ? Account::where('id', $transaction->destination_account_id)->lockForUpdate()->first()
                : null;

            $jumlah = (float) $transaction->jumlah;

            if ($sourceAccount) {
                if ($transaction->tipe === 'income') {
                    $sourceAccount->saldo -= $jumlah;
                    $sourceAccount->save();
                } elseif ($transaction->tipe === 'expense') {
                    $sourceAccount->saldo += $jumlah;
                    $sourceAccount->save();
                } elseif ($transaction->tipe === 'transfer') {
                    $sourceAccount->saldo += $jumlah;
                    $sourceAccount->save();

                    if ($destAccount) {
                        $destAccount->saldo -= $jumlah;
                        $destAccount->save();
                    }
                }
            }

            return $transaction->delete();
        });
    }
}
