<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $accounts = $request->user()->accounts()
            ->withCount('transactions')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSaldo = $accounts->sum('saldo');

        return view('accounts.index', [
            'accounts' => $accounts,
            'totalSaldo' => $totalSaldo,
        ]);
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(AccountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['saldo'] = $data['saldo'] ?? 0;

        Account::create($data);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(Account $account): View
    {
        $this->authorize('update', $account);

        return view('accounts.edit', [
            'account' => $account,
        ]);
    }

    public function update(AccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);

        $data = $request->validated();
        unset($data['saldo']); // Saldo is mutated via transactions

        $account->update($data);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);

        // Check if account is referenced in transactions
        $hasTransactions = $account->transactions()->exists() || $account->incomingTransfers()->exists();

        if ($hasTransactions) {
            return redirect()->route('accounts.index')
                ->with('error', 'Akun tidak dapat dihapus karena masih memiliki riwayat transaksi terkait.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
