<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;

class FinancialHealthService
{
    /**
     * Calculate financial health score (0-100) and analysis for a user.
     *
     * @return array{
     *     overall_score: int,
     *     grade: string,
     *     grade_color: string,
     *     liquid_balance: float,
     *     avg_monthly_expense: float,
     *     emergency_fund_months: float,
     *     savings_rate: float,
     *     budget_adherence_rate: float,
     *     credit_card_balance: float,
     *     metrics: array,
     *     recommendations: array
     * }
     */
    public function getHealthAnalysis(User $user): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();
        $currentPeriod = $now->format('Y-m');

        // Requirement (4): Strict user scoping
        // 1. Savings Rate (30% weight)
        $incomeThisMonth = (float) $user->transactions()
            ->where('tipe', 'income')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $expenseThisMonth = (float) $user->transactions()
            ->where('tipe', 'expense')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $netSavings = $incomeThisMonth - $expenseThisMonth;
        $savingsRate = $incomeThisMonth > 0
            ? max(0, round(($netSavings / $incomeThisMonth) * 100, 1))
            : 0;

        // Target savings rate = 20%
        $savingsScore = min(100, round(($savingsRate / 20) * 100));
        $weightedSavingsScore = round(($savingsScore / 100) * 30);

        // Requirement (3): 'saldo likuid' = bank + e-wallet + kas (excludes kartu_kredit)
        $liquidBalance = (float) $user->accounts()
            ->whereIn('tipe', ['bank', 'e-wallet', 'kas'])
            ->sum('saldo');

        // Average monthly expense over past 3 months
        $threeMonthsAgo = $now->copy()->subMonths(3)->startOfMonth()->toDateString();
        $totalExpense3Months = (float) $user->transactions()
            ->where('tipe', 'expense')
            ->whereBetween('tanggal', [$threeMonthsAgo, $endOfMonth])
            ->sum('jumlah');

        $avgMonthlyExpense = $totalExpense3Months > 0 ? round($totalExpense3Months / 3, 2) : $expenseThisMonth;

        $emergencyFundMonths = $avgMonthlyExpense > 0
            ? round($liquidBalance / $avgMonthlyExpense, 1)
            : ($liquidBalance > 0 ? 6.0 : 0.0);

        // Target emergency fund = 6 months
        $emergencyScore = min(100, round(($emergencyFundMonths / 6) * 100));
        $weightedEmergencyScore = round(($emergencyScore / 100) * 30);

        // 3. Budget Adherence (20% weight)
        $budgets = Budget::where('user_id', $user->id)
            ->where('periode', $currentPeriod)
            ->get();

        if ($budgets->count() > 0) {
            $adherentCount = 0;
            foreach ($budgets as $budget) {
                $spent = (float) $user->transactions()
                    ->where('category_id', $budget->category_id)
                    ->where('tipe', 'expense')
                    ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->sum('jumlah');

                if ($spent <= (float) $budget->limit_bulanan) {
                    $adherentCount++;
                }
            }
            $budgetAdherenceRate = round(($adherentCount / $budgets->count()) * 100, 1);
            $budgetScore = round($budgetAdherenceRate);
        } else {
            $budgetAdherenceRate = 100.0;
            $budgetScore = 80; // Default when no budget configured yet
        }
        $weightedBudgetScore = round(($budgetScore / 100) * 20);

        // 4. Credit Card / Debt Score (20% weight)
        $creditCardBalance = (float) $user->accounts()
            ->where('tipe', 'kartu_kredit')
            ->sum('saldo');

        // Debt is represented when credit card balance is positive or tracked as liability
        if ($creditCardBalance <= 0) {
            $debtScore = 100;
        } else {
            // Debt ratio relative to liquid balance
            $ratio = $liquidBalance > 0 ? ($creditCardBalance / $liquidBalance) : 1;
            $debtScore = max(0, round(100 - ($ratio * 100)));
        }
        $weightedDebtScore = round(($debtScore / 100) * 20);

        $overallScore = (int) min(100, max(0, $weightedSavingsScore + $weightedEmergencyScore + $weightedBudgetScore + $weightedDebtScore));

        if ($overallScore >= 85) {
            $grade = 'Sangat Sehat';
            $gradeColor = 'emerald';
        } elseif ($overallScore >= 70) {
            $grade = 'Sehat';
            $gradeColor = 'blue';
        } elseif ($overallScore >= 50) {
            $grade = 'Cukup / Perlu Perhatian';
            $gradeColor = 'amber';
        } else {
            $grade = 'Beresiko';
            $gradeColor = 'rose';
        }

        // Actionable Recommendations
        $recommendations = [];
        if ($savingsRate < 20) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Tingkatkan Rasio Tabungan',
                'description' => "Rasio tabungan Anda saat ini {$savingsRate}%. Usahakan menyisihkan minimal 20% dari total pendapatan bulanan.",
            ];
        } else {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Rasio Tabungan Bagus',
                'description' => "Rasio tabungan Anda {$savingsRate}% sudah memenuhi target ideal 20%. Pertahankan!",
            ];
        }

        if ($emergencyFundMonths < 3) {
            $recommendations[] = [
                'type' => 'danger',
                'title' => 'Dana Darurat Masih Kurang',
                'description' => "Saldo likuid Anda (kas, bank, e-wallet) baru mencukupi {$emergencyFundMonths} bulan pengeluaran. Targetkan minimal 3-6 bulan pengeluaran.",
            ];
        } else {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Dana Darurat Aman',
                'description' => "Dana darurat Anda mencukupi {$emergencyFundMonths} bulan pengeluaran ideal.",
            ];
        }

        if ($budgets->count() > 0 && $budgetAdherenceRate < 100) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Evaluasi Pos Pengeluaran Over Budget',
                'description' => 'Terdapat kategori pengeluaran yang melebihi limit bulanan. Periksa menu Budgeting.',
            ];
        }

        return [
            'overall_score' => $overallScore,
            'grade' => $grade,
            'grade_color' => $gradeColor,
            'liquid_balance' => $liquidBalance,
            'avg_monthly_expense' => $avgMonthlyExpense,
            'emergency_fund_months' => $emergencyFundMonths,
            'savings_rate' => $savingsRate,
            'budget_adherence_rate' => $budgetAdherenceRate,
            'credit_card_balance' => $creditCardBalance,
            'metrics' => [
                'savings' => ['score' => $savingsScore, 'weighted' => $weightedSavingsScore, 'max' => 30],
                'emergency' => ['score' => $emergencyScore, 'weighted' => $weightedEmergencyScore, 'max' => 30],
                'budget' => ['score' => $budgetScore, 'weighted' => $weightedBudgetScore, 'max' => 20],
                'debt' => ['score' => $debtScore, 'weighted' => $weightedDebtScore, 'max' => 20],
            ],
            'recommendations' => $recommendations,
        ];
    }
}
