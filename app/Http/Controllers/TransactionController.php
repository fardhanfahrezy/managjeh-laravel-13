<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = $user->transactions()
            ->with(['account', 'destinationAccount', 'category']);

        // Filters
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->input('tipe'));
        }

        if ($request->filled('account_id')) {
            $accountId = $request->input('account_id');
            $query->where(function ($q) use ($accountId) {
                $q->where('account_id', $accountId)
                    ->orWhere('destination_account_id', $accountId);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->input('end_date'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('catatan', 'like', "%{$search}%")
                    ->orWhere('jumlah', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $accounts = $user->accounts()->orderBy('nama_akun')->get();
        $categories = $user->categories()->orderBy('nama')->get();

        return view('transactions.index', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'categories' => $categories,
            'filters' => $request->all(),
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        return view('transactions.create', [
            'accounts' => $user->accounts()->orderBy('nama_akun')->get(),
            'incomeCategories' => $user->categories()->where('tipe', 'income')->orderBy('nama')->get(),
            'expenseCategories' => $user->categories()->where('tipe', 'expense')->orderBy('nama')->get(),
            'defaultType' => $request->query('tipe', 'expense'),
            'defaultAccountId' => $request->query('account_id'),
        ]);
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment_url'] = $path;
        }

        $this->transactionService->createTransaction($request->user(), $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);
        $user = auth()->user();

        return view('transactions.edit', [
            'transaction' => $transaction,
            'accounts' => $user->accounts()->orderBy('nama_akun')->get(),
            'incomeCategories' => $user->categories()->where('tipe', 'income')->orderBy('nama')->get(),
            'expenseCategories' => $user->categories()->where('tipe', 'expense')->orderBy('nama')->get(),
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment_url) {
                Storage::disk('public')->delete($transaction->attachment_url);
            }
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment_url'] = $path;
        }

        $this->transactionService->updateTransaction($transaction, $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus dan saldo akun telah disesuaikan.');
    }
}
