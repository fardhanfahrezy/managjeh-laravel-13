<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $user = $request->user();
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $carbonDate = Carbon::createFromFormat('Y-m', $periode) ?: Carbon::now();
        $startOfMonth = $carbonDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $carbonDate->copy()->endOfMonth()->toDateString();

        $budgets = Budget::where('user_id', $user->id)
            ->where('periode', $periode)
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
                    'model' => $budget,
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

        $totalBudget = $budgets->sum('limit');
        $totalTerpakai = $budgets->sum('terpakai');
        $totalSisa = $totalBudget - $totalTerpakai;
        $totalPersentase = $totalBudget > 0 ? round(($totalTerpakai / $totalBudget) * 100, 1) : 0;

        // Categories without budget in this period
        $budgetedCategoryIds = $budgets->pluck('model.category_id')->toArray();
        $unbudgetedCategories = $user->categories()
            ->where('tipe', 'expense')
            ->whereNotIn('id', $budgetedCategoryIds)
            ->orderBy('nama')
            ->get();

        return view('budgets.index', [
            'budgets' => $budgets,
            'periode' => $periode,
            'periodeLabel' => $carbonDate->translatedFormat('F Y'),
            'totalBudget' => $totalBudget,
            'totalTerpakai' => $totalTerpakai,
            'totalSisa' => $totalSisa,
            'totalPersentase' => $totalPersentase,
            'unbudgetedCategories' => $unbudgetedCategories,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $categories = $user->categories()
            ->where('tipe', 'expense')
            ->orderBy('nama')
            ->get();

        return view('budgets.create', [
            'categories' => $categories,
            'periode' => $periode,
        ]);
    }

    public function store(BudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        Budget::create($data);

        return redirect()->route('budgets.index', ['periode' => $data['periode']])
            ->with('success', 'Budget kategori berhasil ditetapkan.');
    }

    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);
        $user = auth()->user();

        $categories = $user->categories()
            ->where('tipe', 'expense')
            ->orderBy('nama')
            ->get();

        return view('budgets.edit', [
            'budget' => $budget,
            'categories' => $categories,
        ]);
    }

    public function update(BudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);

        $data = $request->validated();
        $budget->update($data);

        return redirect()->route('budgets.index', ['periode' => $data['periode']])
            ->with('success', 'Budget berhasil diperbarui.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);

        $periode = $budget->periode;
        $budget->delete();

        return redirect()->route('budgets.index', ['periode' => $periode])
            ->with('success', 'Budget berhasil dihapus.');
    }
}
