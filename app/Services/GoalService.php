<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Goal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoalService
{
    /**
     * Deposit funds from an account to a financial goal.
     *
     * @throws ValidationException
     */
    public function deposit(Goal $goal, Account $account, float $amount, ?string $catatan = null): Transaction
    {
        return DB::transaction(function () use ($goal, $account, $amount, $catatan) {
            $lockedAccount = Account::where('user_id', $account->user_id)
                ->where('id', $account->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedGoal = Goal::where('user_id', $goal->user_id)
                ->where('id', $goal->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedAccount->allowsNegativeBalance() && ($lockedAccount->saldo - $amount) < 0) {
                throw ValidationException::withMessages([
                    'jumlah' => "Saldo akun '{$lockedAccount->nama_akun}' tidak mencukupi untuk menabung (Sisa saldo: Rp ".number_format($lockedAccount->saldo, 2, ',', '.').').',
                ]);
            }

            // Deduct account balance and increase goal progress
            $lockedAccount->saldo -= $amount;
            $lockedAccount->save();

            $lockedGoal->progres += $amount;
            $lockedGoal->save();

            return Transaction::create([
                'user_id' => $goal->user_id,
                'account_id' => $lockedAccount->id,
                'destination_account_id' => null,
                'category_id' => null,
                'goal_id' => $lockedGoal->id,
                'jumlah' => $amount,
                'tipe' => 'saving',
                'tanggal' => Carbon::now()->toDateString(),
                'catatan' => 'Setor Tabungan: '.$lockedGoal->nama_goal.($catatan ? " ({$catatan})" : ''),
            ]);
        });
    }

    /**
     * Withdraw funds from a financial goal back to an account.
     *
     * @throws ValidationException
     */
    public function withdraw(Goal $goal, Account $account, float $amount, ?string $catatan = null): Transaction
    {
        return DB::transaction(function () use ($goal, $account, $amount, $catatan) {
            $lockedAccount = Account::where('user_id', $account->user_id)
                ->where('id', $account->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedGoal = Goal::where('user_id', $goal->user_id)
                ->where('id', $goal->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $lockedGoal->progres < $amount) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Nominal penarikan melebihi progres tabungan yang terkumpul (Terkumpul: Rp '.number_format($lockedGoal->progres, 2, ',', '.').').',
                ]);
            }

            // Increase account balance and decrease goal progress
            $lockedAccount->saldo += $amount;
            $lockedAccount->save();

            $lockedGoal->progres -= $amount;
            $lockedGoal->save();

            return Transaction::create([
                'user_id' => $goal->user_id,
                'account_id' => $lockedAccount->id,
                'destination_account_id' => $lockedAccount->id,
                'category_id' => null,
                'goal_id' => $lockedGoal->id,
                'jumlah' => $amount,
                'tipe' => 'saving',
                'tanggal' => Carbon::now()->toDateString(),
                'catatan' => 'Tarik Tabungan: '.$lockedGoal->nama_goal.($catatan ? " ({$catatan})" : ''),
            ]);
        });
    }
}
