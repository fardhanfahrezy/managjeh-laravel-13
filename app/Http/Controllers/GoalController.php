<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalDepositWithdrawRequest;
use App\Http\Requests\GoalRequest;
use App\Models\Account;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $user = $request->user();
        $goals = $user->goals()->orderBy('created_at', 'desc')->get();
        $accounts = $user->accounts()->orderBy('nama_akun')->get();

        $totalTarget = $goals->sum('target');
        $totalProgres = $goals->sum('progres');
        $overallPercentage = $totalTarget > 0 ? min(100, round(($totalProgres / $totalTarget) * 100, 1)) : 0;

        return view('goals.index', compact('goals', 'accounts', 'totalTarget', 'totalProgres', 'overallPercentage'));
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(GoalRequest $request): RedirectResponse
    {
        $request->user()->goals()->create($request->validated());

        return redirect()->route('goals.index')->with('success', 'Tujuan finansial berhasil dibuat.');
    }

    public function edit(Goal $goal): View
    {
        $this->authorize('update', $goal);

        return view('goals.edit', compact('goal'));
    }

    public function update(GoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return redirect()->route('goals.index')->with('success', 'Tujuan finansial berhasil diperbarui.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Tujuan finansial berhasil dihapus.');
    }

    public function deposit(GoalDepositWithdrawRequest $request, Goal $goal, GoalService $service): RedirectResponse
    {
        $this->authorize('update', $goal);

        $account = Account::where('user_id', $request->user()->id)->where('id', $request->validated('account_id'))->firstOrFail();

        $service->deposit(
            $goal,
            $account,
            (float) $request->validated('jumlah'),
            $request->validated('catatan')
        );

        return redirect()->route('goals.index')->with('success', 'Berhasil menyetor Rp '.number_format($request->validated('jumlah'), 0, ',', '.')." ke goal '{$goal->nama_goal}'.");
    }

    public function withdraw(GoalDepositWithdrawRequest $request, Goal $goal, GoalService $service): RedirectResponse
    {
        $this->authorize('update', $goal);

        $account = Account::where('user_id', $request->user()->id)->where('id', $request->validated('account_id'))->firstOrFail();

        $service->withdraw(
            $goal,
            $account,
            (float) $request->validated('jumlah'),
            $request->validated('catatan')
        );

        return redirect()->route('goals.index')->with('success', 'Berhasil menarik Rp '.number_format($request->validated('jumlah'), 0, ',', '.')." dari goal '{$goal->nama_goal}'.");
    }
}
