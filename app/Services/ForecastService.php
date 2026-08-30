<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;

class ForecastService
{
    /**
     * Calculate monthly spending forecast for a specific user.
     *
     * @return array{
     *     has_enough_data: bool,
     *     days_elapsed: int,
     *     total_days_in_month: int,
     *     current_expense: float,
     *     daily_rate: float,
     *     projected_total: float,
     *     total_budget: float,
     *     projected_percentage: float,
     *     status: string,
     *     status_label: string,
     *     status_color: string,
     *     category_forecasts: array
     * }
     */
    public function getMonthlyForecast(User $user, ?Carbon $date = null): array
    {
        $now = $date ?? Carbon::now();
        $dayOfMonth = $now->day;
        $totalDaysInMonth = $now->daysInMonth;
        $currentPeriod = $now->format('Y-m');

        // Requirement (1): Only display forecast if at least 3 days have elapsed
        if ($dayOfMonth < 3) {
            return [
                'has_enough_data' => false,
                'days_elapsed' => $dayOfMonth,
                'total_days_in_month' => $totalDaysInMonth,
                'current_expense' => 0.0,
                'daily_rate' => 0.0,
                'projected_total' => 0.0,
                'total_budget' => 0.0,
                'projected_percentage' => 0.0,
                'status' => 'insufficient_data',
                'status_label' => 'Belum Cukup Data',
                'status_color' => 'slate',
                'category_forecasts' => [],
            ];
        }

        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        // Requirement (4): Strict user scoping
        $currentExpense = (float) $user->transactions()
            ->where('tipe', 'expense')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $dailyRate = $currentExpense / $dayOfMonth;
        $projectedTotal = round($dailyRate * $totalDaysInMonth, 2);

        $totalBudget = (float) Budget::where('user_id', $user->id)
            ->where('periode', $currentPeriod)
            ->sum('limit_bulanan');

        $projectedPercentage = $totalBudget > 0
            ? round(($projectedTotal / $totalBudget) * 100, 1)
            : 0;

        // Requirement (2): Threshold logic (<80% Aman, 80-100% Waspada, >100% Potensi Over Budget)
        if ($projectedPercentage > 100) {
            $status = 'over_budget';
            $statusLabel = 'Potensi Over Budget';
            $statusColor = 'rose';
        } elseif ($projectedPercentage >= 80) {
            $status = 'waspada';
            $statusLabel = 'Waspada';
            $statusColor = 'amber';
        } else {
            $status = 'aman';
            $statusLabel = 'Aman';
            $statusColor = 'emerald';
        }

        // Per-category forecast calculation
        $budgets = Budget::where('user_id', $user->id)
            ->where('periode', $currentPeriod)
            ->with('category')
            ->get();

        $categoryForecasts = [];
        foreach ($budgets as $budget) {
            $catSpent = (float) $user->transactions()
                ->where('category_id', $budget->category_id)
                ->where('tipe', 'expense')
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('jumlah');

            $catDailyRate = $catSpent / $dayOfMonth;
            $catProjected = round($catDailyRate * $totalDaysInMonth, 2);
            $catLimit = (float) $budget->limit_bulanan;
            $catPercentage = $catLimit > 0 ? round(($catProjected / $catLimit) * 100, 1) : 0;

            if ($catPercentage > 100) {
                $catStatus = 'over_budget';
                $catLabel = 'Potensi Over Budget';
                $catColor = 'rose';
            } elseif ($catPercentage >= 80) {
                $catStatus = 'waspada';
                $catLabel = 'Waspada';
                $catColor = 'amber';
            } else {
                $catStatus = 'aman';
                $catLabel = 'Aman';
                $catColor = 'emerald';
            }

            $categoryForecasts[] = [
                'category_id' => $budget->category_id,
                'category_nama' => $budget->category->nama,
                'category_warna' => $budget->category->warna ?? '#64748B',
                'limit' => $catLimit,
                'spent' => $catSpent,
                'projected' => $catProjected,
                'percentage' => $catPercentage,
                'status' => $catStatus,
                'status_label' => $catLabel,
                'status_color' => $catColor,
            ];
        }

        return [
            'has_enough_data' => true,
            'days_elapsed' => $dayOfMonth,
            'total_days_in_month' => $totalDaysInMonth,
            'current_expense' => $currentExpense,
            'daily_rate' => round($dailyRate, 2),
            'projected_total' => $projectedTotal,
            'total_budget' => $totalBudget,
            'projected_percentage' => $projectedPercentage,
            'status' => $status,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'category_forecasts' => $categoryForecasts,
        ];
    }
}
