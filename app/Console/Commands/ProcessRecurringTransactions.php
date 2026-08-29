<?php

namespace App\Console\Commands;

use App\Services\RecurringTransactionService;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses seluruh transaksi dan tagihan berulang yang jatuh tempo';

    /**
     * Execute the console command.
     */
    public function handle(RecurringTransactionService $service): int
    {
        $this->info('Memeriksa tagihan berulang yang jatuh tempo...');

        $result = $service->processDueRules();

        $this->info("Selesai diproses: {$result['processed']} transaksi.");

        if ($result['skipped'] > 0) {
            $this->warn("Dilewati (saldo tidak cukup/error): {$result['skipped']} transaksi.");
            foreach ($result['errors'] as $error) {
                $this->line(" - {$error}");
            }
        }

        return Command::SUCCESS;
    }
}
