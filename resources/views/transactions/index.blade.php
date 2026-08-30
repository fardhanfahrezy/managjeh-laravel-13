<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Riwayat Transaksi</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Semua catatan pemasukan, pengeluaran, transfer, tabungan goal, dan pemecahan kategori.</p>
            </div>
            <div class="flex items-center gap-2" x-data="{ importModal: false }">
                <!-- Export CSV -->
                <a href="{{ route('transactions.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export CSV</span>
                </a>

                <!-- Import CSV Button -->
                <button @click="importModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import CSV</span>
                </button>

                <!-- Create Transaction -->
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Transaksi Baru</span>
                </a>

                <!-- Import Modal -->
                <div x-show="importModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                    <div @click.away="importModal = false" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-7 space-y-4 animate-glide">
                        <div class="flex items-center justify-between">
                            <h3 class="font-black text-lg text-slate-900 dark:text-white">Import Transaksi dari CSV</h3>
                            <button @click="importModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Unggah file CSV dengan header: <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-blue-600 font-bold">Tanggal, Akun, Tipe, Kategori, Jumlah, Catatan</code>.
                            Nama Akun dan Kategori wajib sesuai persis dengan data yang sudah terdaftar.
                        </p>

                        <div class="p-3.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-2xl border border-blue-100 dark:border-blue-900/50 flex items-center justify-between">
                            <span class="text-xs text-blue-800 dark:text-blue-300 font-bold">Belum punya template CSV?</span>
                            <a href="{{ route('transactions.template') }}" class="text-xs font-black text-blue-600 dark:text-blue-400 hover:underline">
                                Unduh Template &rarr;
                            </a>
                        </div>

                        <form method="POST" action="{{ route('transactions.import') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pilih File CSV</label>
                                <input type="file" name="file" accept=".csv,text/csv" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950 dark:file:text-blue-300" />
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <button type="button" @click="importModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-md transition">Mulai Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Filter Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Cari Catatan</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-xs py-2.5 px-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600" />
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tipe</label>
                    <select name="tipe" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-xs py-2.5 px-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        <option value="">Semua Tipe</option>
                        <option value="expense" {{ request('tipe') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="income" {{ request('tipe') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="transfer" {{ request('tipe') === 'transfer' ? 'selected' : '' }}>Transfer Antar Akun</option>
                        <option value="saving" {{ request('tipe') === 'saving' ? 'selected' : '' }}>Tabungan (Goal)</option>
                    </select>
                </div>

                <!-- Akun -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Akun Dompet</label>
                    <select name="account_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-xs py-2.5 px-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        <option value="">Semua Akun</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-xs py-2.5 px-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }} ({{ $cat->tipe }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-xs py-2.5 px-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600" />
                </div>

                <!-- Action Filter Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'tipe', 'account_id', 'category_id', 'start_date', 'end_date']))
                        <a href="{{ route('transactions.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-4 px-4 sm:px-6">Tanggal</th>
                            <th class="py-4 px-4 sm:px-6">Keterangan & Kategori</th>
                            <th class="py-4 px-4 sm:px-6">Akun Dompet</th>
                            <th class="py-4 px-4 sm:px-6">Tipe</th>
                            <th class="py-4 px-4 sm:px-6 text-right">Jumlah</th>
                            <th class="py-4 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-900 dark:text-slate-200">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <!-- Tanggal -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-xs font-medium text-slate-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}
                                </td>

                                <!-- Keterangan / Kategori -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 dark:text-white">
                                            @if ($tx->tipe === 'transfer')
                                                Transfer Antar Akun
                                            @elseif ($tx->tipe === 'saving')
                                                Goal: {{ $tx->goal?->nama_goal ?? 'Tabungan' }}
                                            @elseif ($tx->isSplit())
                                                <span class="text-blue-600 dark:text-blue-400">Pecah Transaksi ({{ $tx->splits->count() }} Kategori)</span>
                                            @else
                                                {{ $tx->category?->nama ?? 'Tanpa Kategori' }}
                                            @endif
                                        </span>
                                    </div>
                                    @if ($tx->catatan)
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $tx->catatan }}</div>
                                    @endif
                                    @if ($tx->isSplit())
                                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                                            @foreach($tx->splits as $sp)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-[11px] text-slate-600 dark:text-slate-300 font-medium">
                                                    <span>{{ $sp->category?->nama }}:</span>
                                                    <span class="font-bold">Rp {{ number_format($sp->jumlah, 0, ',', '.') }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($tx->attachment_url)
                                        <a href="{{ Storage::url($tx->attachment_url) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span>Lihat Lampiran Struk</span>
                                        </a>
                                    @endif
                                </td>

                                <!-- Akun -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-xs font-semibold">
                                    @if ($tx->tipe === 'transfer')
                                        <span class="text-slate-800 dark:text-slate-200">{{ $tx->account->nama_akun }}</span>
                                        <span class="text-slate-400 mx-1">&rarr;</span>
                                        <span class="text-slate-800 dark:text-slate-200">{{ $tx->destinationAccount?->nama_akun ?? 'Akun Tujuan' }}</span>
                                    @else
                                        <span class="text-slate-800 dark:text-slate-200">{{ $tx->account->nama_akun }}</span>
                                    @endif
                                </td>

                                <!-- Tipe Badge -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    @php
                                        $badgeStyles = match($tx->tipe) {
                                            'income' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                                            'expense' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
                                            'transfer' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
                                            'saving' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider {{ $badgeStyles }}">
                                        {{ $tx->tipe === 'saving' ? 'Goal' : ucfirst($tx->tipe) }}
                                    </span>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-right font-black {{ $tx->tipe === 'income' ? 'text-emerald-600 dark:text-emerald-400' : ($tx->tipe === 'expense' ? 'text-red-600 dark:text-red-400' : ($tx->tipe === 'saving' ? 'text-indigo-600 dark:text-indigo-400' : 'text-blue-600 dark:text-blue-400')) }}">
                                    {{ $tx->tipe === 'income' ? '+' : ($tx->tipe === 'expense' ? '-' : '') }} Rp {{ number_format($tx->jumlah, 2, ',', '.') }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($tx->tipe !== 'saving')
                                            <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg transition" title="Edit Transaksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Saldo akun terkait akan disesuaikan kembali.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 rounded-lg transition" title="Hapus Transaksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                    <p class="text-sm font-medium">Tidak ada data transaksi yang sesuai filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

