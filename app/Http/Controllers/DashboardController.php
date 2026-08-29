<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $now = Carbon::now();
        $currentPeriod = $now->format('Y-m');
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        // 1. Total Saldo
        $totalSaldo = $user->accounts()->sum('saldo');

        // 2. Income & Expense this month
        $incomeBulanIni = (float) $user->transactions()
            ->where('tipe', 'income')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $expenseBulanIni = (float) $user->transactions()
            ->where('tipe', 'expense')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $netSavings = $incomeBulanIni - $expenseBulanIni;
        $savingsRate = $incomeBulanIni > 0 ? max(0, round(($netSavings / $incomeBulanIni) * 100, 1)) : 0;

        // 3. Accounts
        $accounts = $user->accounts()->orderBy('saldo', 'desc')->get();

        // 4. 5 Transaksi Terakhir
        $recentTransactions = $user->transactions()
            ->with(['account', 'destinationAccount', 'category'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // 5. Budgets for current month
        $budgets = Budget::where('user_id', $user->id)
            ->where('periode', $currentPeriod)
            ->with('category')
            ->get()
            ->map(function ($budget) use ($user, $startOfMonth, $endOfMonth) {
                $terpakai = (float) $user->transactions()
                    ->where('category_id', $budget->category_id)
                    ->where('tipe', 'expense')
                    ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->sum('jumlah');

                $limit = (float) $budget->limit_bulanan;
                $persentase = $limit > 0 ? round(($terpakai / $limit) * 100, 1) : 0;
                $sisa = $limit - $terpakai;

                return [
                    'id' => $budget->id,
                    'kategori' => $budget->category->nama,
                    'warna' => $budget->category->warna ?? '#64748B',
                    'icon' => $budget->category->icon ?? 'tag',
                    'limit' => $limit,
                    'terpakai' => $terpakai,
                    'sisa' => $sisa,
                    'persentase' => $persentase,
                    'is_over' => $terpakai > $limit,
                    'is_warning' => $persentase >= 80 && $persentase <= 100,
                ];
            });

        return view('dashboard', [
            'totalSaldo' => $totalSaldo,
            'incomeBulanIni' => $incomeBulanIni,
            'expenseBulanIni' => $expenseBulanIni,
            'netSavings' => $netSavings,
            'savingsRate' => $savingsRate,
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
            'budgets' => $budgets,
            'currentPeriod' => $now->translatedFormat('F Y'),
        ]);
    }
}
