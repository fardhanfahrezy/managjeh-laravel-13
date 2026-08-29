<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Dashboard Finansial</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan kondisi keuangan pribadi periode <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $currentPeriod }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Catat Transaksi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Saldo -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Saldo Bersih</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Rp {{ number_format($totalSaldo, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $accounts->count() }} akun aktif terdaftar</p>
                </div>
            </div>

            <!-- Pemasukan Bulan Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pemasukan Bulan Ini</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight">
                        + Rp {{ number_format($incomeBulanIni, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total arus kas masuk</p>
                </div>
            </div>

            <!-- Pengeluaran Bulan Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pengeluaran Bulan Ini</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 tracking-tight">
                        - Rp {{ number_format($expenseBulanIni, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total belanja & tagihan</p>
                </div>
            </div>

            <!-- Net Savings / Tabungan Bersih -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tabungan Bersih</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-bold {{ $netSavings >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }} tracking-tight">
                        {{ $netSavings >= 0 ? '+' : '' }} Rp {{ number_format($netSavings, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Savings Rate: <span class="font-semibold {{ $savingsRate >= 20 ? 'text-emerald-500' : 'text-amber-500' }}">{{ $savingsRate }}%</span></p>
                </div>
            </div>
        </div>

        <!-- Main 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Recent Transactions & Quick Actions -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Transactions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Transaksi Terbaru</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">5 aktivitas pencatatan terakhir</p>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 inline-flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($recentTransactions as $tx)
                            <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-750 transition">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $tx->tipe === 'income' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400' : ($tx->tipe === 'expense' ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400') }}">
                                        @if ($tx->tipe === 'income')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                        @elseif ($tx->tipe === 'expense')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                                @if ($tx->tipe === 'transfer')
                                                    Transfer: {{ $tx->account->nama_akun }} &rarr; {{ $tx->destinationAccount?->nama_akun ?? 'Akun' }}
                                                @else
                                                    {{ $tx->category?->nama ?? 'Tanpa Kategori' }}
                                                @endif
                                            </span>
                                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full {{ $tx->tipe === 'income' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : ($tx->tipe === 'expense' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300') }}">
                                                {{ ucfirst($tx->tipe) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            <span>{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $tx->account->nama_akun }}</span>
                                            @if ($tx->catatan)
                                                <span>&bull;</span>
                                                <span class="truncate max-w-[150px] sm:max-w-[220px]">{{ $tx->catatan }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="font-bold text-sm sm:text-base {{ $tx->tipe === 'income' ? 'text-emerald-600 dark:text-emerald-400' : ($tx->tipe === 'expense' ? 'text-rose-600 dark:text-rose-400' : 'text-blue-600 dark:text-blue-400') }}">
                                        {{ $tx->tipe === 'income' ? '+' : ($tx->tipe === 'expense' ? '-' : '') }} Rp {{ number_format($tx->jumlah, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Belum ada transaksi yang dicatat.</p>
                                <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex items-center text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    + Catat transaksi pertamamu
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Account Wallets & Monthly Budget Progress -->
            <div class="space-y-6">
                <!-- Accounts Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Dompet & Akun</h2>
                        <a href="{{ route('accounts.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">Kelola</a>
                    </div>
                    <div class="space-y-3">
                        @forelse ($accounts as $acc)
                            <div class="p-3.5 rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-750 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs" style="background-color: {{ $acc->warna ?? '#10B981' }}20; color: {{ $acc->warna ?? '#10B981' }};">
                                        {{ strtoupper(substr($acc->nama_akun, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-none">{{ $acc->nama_akun }}</h3>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $acc->tipe) }}</span>
                                    </div>
                                </div>
                                <div class="text-right font-bold text-sm text-gray-900 dark:text-white">
                                    Rp {{ number_format($acc->saldo, 2, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Belum ada akun dompet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Budget Tracker Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Budgeting Bulan Ini</h2>
                        <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">Atur</a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($budgets as $b)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $b['kategori'] }}</span>
                                    <span class="font-semibold {{ $b['is_over'] ? 'text-rose-600 dark:text-rose-400' : ($b['is_warning'] ? 'text-amber-500' : 'text-gray-600 dark:text-gray-400') }}">
                                        {{ $b['persentase'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $b['is_over'] ? 'bg-rose-500' : ($b['is_warning'] ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $b['persentase']) }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                    <span>Rp {{ number_format($b['terpakai'], 0, ',', '.') }}</span>
                                    <span>Limit: Rp {{ number_format($b['limit'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-xs text-gray-400 mb-2">Belum ada budget untuk bulan ini.</p>
                                <a href="{{ route('budgets.create') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    + Tetapkan limit budget
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
