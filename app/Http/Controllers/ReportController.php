<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ForecastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ForecastService $forecastService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $carbonDate = Carbon::createFromFormat('Y-m', $periode) ?: Carbon::now();
        $startOfMonth = $carbonDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $carbonDate->copy()->endOfMonth()->toDateString();

        // 1. Expense by Category for Pie Chart
        $expenseByCategory = $user->transactions()
            ->where('transactions.tipe', 'expense')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.nama as kategori',
                'categories.warna',
                DB::raw('SUM(transactions.jumlah) as total')
            )
            ->groupBy('categories.id', 'categories.nama', 'categories.warna')
            ->orderBy('total', 'desc')
            ->get();

        // 2. Income by Category
        $incomeByCategory = $user->transactions()
            ->where('transactions.tipe', 'income')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.nama as kategori',
                'categories.warna',
                DB::raw('SUM(transactions.jumlah) as total')
            )
            ->groupBy('categories.id', 'categories.nama', 'categories.warna')
            ->orderBy('total', 'desc')
            ->get();

        // 3. Monthly Trends (Last 6 months)
        $trendMonths = [];
        $incomeTrends = [];
        $expenseTrends = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $trendMonths[] = $month->translatedFormat('M Y');

            $mStart = $month->copy()->startOfMonth()->toDateString();
            $mEnd = $month->copy()->endOfMonth()->toDateString();

            $inc = (float) $user->transactions()
                ->where('tipe', 'income')
                ->whereBetween('tanggal', [$mStart, $mEnd])
                ->sum('jumlah');

            $exp = (float) $user->transactions()
                ->where('tipe', 'expense')
                ->whereBetween('tanggal', [$mStart, $mEnd])
                ->sum('jumlah');

            $incomeTrends[] = $inc;
            $expenseTrends[] = $exp;
        }

        // Metrics for selected month
        $totalIncome = $expenseByCategory->isEmpty() && $incomeByCategory->isEmpty() ? 0 : (float) $user->transactions()->where('tipe', 'income')->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('jumlah');
        $totalExpense = (float) $expenseByCategory->sum('total');
        $netCashflow = $totalIncome - $totalExpense;
        $daysInMonth = $carbonDate->daysInMonth;
        $avgDailyExpense = $daysInMonth > 0 ? round($totalExpense / $daysInMonth, 2) : 0;

        $forecast = $this->forecastService->getMonthlyForecast($user, $carbonDate);

        return view('reports.index', [
            'periode' => $periode,
            'periodeLabel' => $carbonDate->translatedFormat('F Y'),
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netCashflow' => $netCashflow,
            'avgDailyExpense' => $avgDailyExpense,
            'expenseByCategory' => $expenseByCategory,
            'incomeByCategory' => $incomeByCategory,
            'trendMonths' => $trendMonths,
            'incomeTrends' => $incomeTrends,
            'expenseTrends' => $expenseTrends,
            'forecast' => $forecast,
        ]);
    }
}
