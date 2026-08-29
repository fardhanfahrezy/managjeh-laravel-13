<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecurringRuleRequest;
use App\Models\RecurringRule;
use App\Services\RecurringTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecurringRuleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $rules = $request->user()->recurringRules()
            ->with(['account', 'category'])
            ->orderBy('tanggal_berikutnya', 'asc')
            ->get();

        return view('recurring.index', compact('rules'));
    }

    public function create(Request $request): View
    {
        $accounts = $request->user()->accounts()->orderBy('nama_akun')->get();
        $categories = $request->user()->categories()->orderBy('nama_kategori')->get();

        return view('recurring.create', compact('accounts', 'categories'));
    }

    public function store(RecurringRuleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $request->user()->recurringRules()->create($validated);

        return redirect()->route('recurring-rules.index')->with('success', 'Aturan tagihan berulang berhasil dibuat.');
    }

    public function edit(RecurringRule $recurringRule, Request $request): View
    {
        $this->authorize('update', $recurringRule);

        $accounts = $request->user()->accounts()->orderBy('nama_akun')->get();
        $categories = $request->user()->categories()->orderBy('nama_kategori')->get();

        return view('recurring.edit', compact('recurringRule', 'accounts', 'categories'));
    }

    public function update(RecurringRuleRequest $request, RecurringRule $recurringRule): RedirectResponse
    {
        $this->authorize('update', $recurringRule);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $recurringRule->update($validated);

        return redirect()->route('recurring-rules.index')->with('success', 'Aturan tagihan berulang berhasil diperbarui.');
    }

    public function destroy(RecurringRule $recurringRule): RedirectResponse
    {
        $this->authorize('delete', $recurringRule);

        $recurringRule->delete();

        return redirect()->route('recurring-rules.index')->with('success', 'Aturan tagihan berulang berhasil dihapus.');
    }

    public function toggle(RecurringRule $recurringRule): RedirectResponse
    {
        $this->authorize('update', $recurringRule);

        $recurringRule->is_active = ! $recurringRule->is_active;
        $recurringRule->save();

        $status = $recurringRule->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('recurring-rules.index')->with('success', "Status tagihan berhasil {$status}.");
    }

    public function processNow(RecurringTransactionService $service): RedirectResponse
    {
        $result = $service->processDueRules();

        $msg = "Pemrosesan selesai: {$result['processed']} transaksi berhasil dibukukan.";
        if ($result['skipped'] > 0) {
            $msg .= " ({$result['skipped']} tagihan dilewati karena saldo tidak mencukupi).";
        }

        return redirect()->route('recurring-rules.index')->with('success', $msg);
    }
}
