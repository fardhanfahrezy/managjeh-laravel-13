<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Riwayat Transaksi</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Semua catatan pemasukan, pengeluaran, dan mutasi transfer antar akun.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Transaksi Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Filter Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cari Catatan</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tipe</label>
                    <select name="tipe" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Tipe</option>
                        <option value="expense" {{ request('tipe') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="income" {{ request('tipe') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="transfer" {{ request('tipe') === 'transfer' ? 'selected' : '' }}>Transfer Antar Akun</option>
                    </select>
                </div>

                <!-- Akun -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Akun Dompet</label>
                    <select name="account_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Akun</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="category_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }} ({{ $cat->tipe }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>

                <!-- Action Filter Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'tipe', 'account_id', 'category_id', 'start_date', 'end_date']))
                        <a href="{{ route('transactions.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold text-xs rounded-xl transition text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-750 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Tanggal</th>
                            <th class="py-3.5 px-4 sm:px-6">Keterangan & Kategori</th>
                            <th class="py-3.5 px-4 sm:px-6">Akun Dompet</th>
                            <th class="py-3.5 px-4 sm:px-6">Tipe</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Jumlah</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-900 dark:text-gray-200">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/50 transition">
                                <!-- Tanggal -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}
                                </td>

                                <!-- Keterangan / Kategori -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        @if ($tx->tipe === 'transfer')
                                            Transfer Antar Akun
                                        @else
                                            {{ $tx->category?->nama ?? 'Tanpa Kategori' }}
                                        @endif
                                    </div>
                                    @if ($tx->catatan)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $tx->catatan }}</div>
                                    @endif
                                    @if ($tx->attachment_url)
                                        <a href="{{ Storage::url($tx->attachment_url) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 hover:underline mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span>Lihat Lampiran</span>
                                        </a>
                                    @endif
                                </td>

                                <!-- Akun -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-xs">
                                    @if ($tx->tipe === 'transfer')
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $tx->account->nama_akun }}</span>
                                        <span class="text-gray-400 mx-1">&rarr;</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $tx->destinationAccount?->nama_akun ?? 'Akun Tujuan' }}</span>
                                    @else
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $tx->account->nama_akun }}</span>
                                    @endif
                                </td>

                                <!-- Tipe Badge -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider {{ $tx->tipe === 'income' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : ($tx->tipe === 'expense' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300') }}">
                                        {{ ucfirst($tx->tipe) }}
                                    </span>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-right font-bold {{ $tx->tipe === 'income' ? 'text-emerald-600 dark:text-emerald-400' : ($tx->tipe === 'expense' ? 'text-rose-600 dark:text-rose-400' : 'text-blue-600 dark:text-blue-400') }}">
                                    {{ $tx->tipe === 'income' ? '+' : ($tx->tipe === 'expense' ? '-' : '') }} Rp {{ number_format($tx->jumlah, 2, ',', '.') }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg transition" title="Edit Transaksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Saldo akun terkait akan disesuaikan kembali.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg transition" title="Hapus Transaksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                    <p class="text-sm font-medium">Tidak ada data transaksi yang sesuai filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
