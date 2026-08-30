<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard Finansial</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan kondisi keuangan pribadi periode <span class="font-bold text-blue-600 dark:text-blue-400">{{ $currentPeriod }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Catat Transaksi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Top Metrics: Signature Net Worth Focal Card + 3 Summary Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- 1. SIGNATURE NET WORTH CARD (Focal Point: bg-slate-900) -->
            <div class="lg:col-span-2 bg-slate-900 dark:bg-slate-900 text-white rounded-3xl p-6 sm:p-7 shadow-xl shadow-slate-900/10 relative overflow-hidden flex flex-col justify-between border border-slate-800">
                <!-- Background Decorative Activity / Waveform -->
                <div class="absolute -right-12 -bottom-12 opacity-10 pointer-events-none text-white">
                    <svg class="w-[250px] h-[250px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Saldo Bersih (Net Worth)</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                            {{ $accounts->count() }} Akun Terdaftar
                        </span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                            Rp {{ number_format($totalSaldo, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span>Terakhir diperbarui: Realtime</span>
                    <a href="{{ route('accounts.index') }}" class="font-bold text-blue-400 hover:text-blue-300 hover:underline flex items-center gap-1">
                        <span>Kelola Akun & Dompet</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- 2. Pemasukan Bulan Ini -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pemasukan Bulan Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                        + Rp {{ number_format($incomeBulanIni, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Total arus kas masuk</p>
                </div>
            </div>

            <!-- 3. Pengeluaran Bulan Ini -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pengeluaran Bulan Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-black text-red-600 dark:text-red-400 tracking-tight">
                        - Rp {{ number_format($expenseBulanIni, 2, ',', '.') }}
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mt-1">
                        <span>Savings Rate:</span>
                        <span class="font-bold {{ $savingsRate >= 20 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-500' }}">{{ $savingsRate }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spending Forecast & Financial Health Highlights Bar -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Forecast Banner -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Spending Forecast</span>
                        @if($forecast['has_enough_data'])
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-{{ $forecast['status_color'] }}-100 text-{{ $forecast['status_color'] }}-800 dark:bg-{{ $forecast['status_color'] }}-900/40 dark:text-{{ $forecast['status_color'] }}-300">
                                {{ $forecast['status_label'] }}
                            </span>
                        @endif
                    </div>
                    @if($forecast['has_enough_data'])
                        <div class="text-lg font-black text-slate-900 dark:text-white">
                            Proyeksi: Rp {{ number_format($forecast['projected_total'], 0, ',', '.') }}
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Rata-rata pengeluaran Rp {{ number_format($forecast['daily_rate'], 0, ',', '.') }} / hari</p>
                    @else
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Cukup Data</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Proyeksi aktif mulai tanggal 3 setiap bulannya.</p>
                    @endif
                </div>
                <a href="{{ route('financial-health.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition shrink-0">
                    Detail Forecast &rarr;
                </a>
            </div>

            <!-- Financial Health Score Summary Banner -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Skor Kesehatan Finansial</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-{{ $financialHealth['grade_color'] }}-100 text-{{ $financialHealth['grade_color'] }}-800 dark:bg-{{ $financialHealth['grade_color'] }}-900/40 dark:text-{{ $financialHealth['grade_color'] }}-300">
                            {{ $financialHealth['grade'] }}
                        </span>
                    </div>
                    <div class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ $financialHealth['overall_score'] }} / 100</span>
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">(Dana Darurat: {{ $financialHealth['emergency_fund_months'] }} bln)</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Saldo Likuid: Rp {{ number_format($financialHealth['liquid_balance'], 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('financial-health.index') }}" class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-xl transition shrink-0">
                    Analisis Lengkap &rarr;
                </a>
            </div>
        </div>

        <!-- Main 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Recent Transactions & Active Goals -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Transactions Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-slate-900 dark:text-white">Transaksi Terbaru</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">5 aktivitas pencatatan mutasi terakhir</p>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 inline-flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($recentTransactions as $tx)
                            <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $tx->tipe === 'income' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400' : ($tx->tipe === 'expense' ? 'bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400' : ($tx->tipe === 'saving' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400')) }}">
                                        @if ($tx->tipe === 'income')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                        @elseif ($tx->tipe === 'expense')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                        @elseif ($tx->tipe === 'saving')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-sm text-slate-900 dark:text-white">
                                                @if ($tx->tipe === 'transfer')
                                                    Transfer: {{ $tx->account->nama_akun }} &rarr; {{ $tx->destinationAccount?->nama_akun ?? 'Akun' }}
                                                @elseif ($tx->tipe === 'saving')
                                                    Goal: {{ $tx->goal?->nama_goal ?? 'Tabungan' }}
                                                @else
                                                    {{ $tx->category?->nama ?? 'Tanpa Kategori' }}
                                                @endif
                                            </span>
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $tx->tipe === 'income' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($tx->tipe === 'expense' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : ($tx->tipe === 'saving' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300')) }}">
                                                {{ $tx->tipe === 'saving' ? 'Goal' : ucfirst($tx->tipe) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
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
                                    <div class="font-black text-sm sm:text-base {{ $tx->tipe === 'income' ? 'text-emerald-600 dark:text-emerald-400' : ($tx->tipe === 'expense' ? 'text-red-600 dark:text-red-400' : ($tx->tipe === 'saving' ? 'text-indigo-600 dark:text-indigo-400' : 'text-blue-600 dark:text-blue-400')) }}">
                                        {{ $tx->tipe === 'income' ? '+' : ($tx->tipe === 'expense' ? '-' : '') }} Rp {{ number_format($tx->jumlah, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center text-slate-400 dark:text-slate-500">
                                <p class="text-sm font-medium">Belum ada transaksi yang dicatat.</p>
                                <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                    + Catat transaksi pertamamu
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Active Goals Widget -->
                @if($activeGoals->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-black text-slate-900 dark:text-white">Target Tabungan Aktif</h2>
                            <a href="{{ route('goals.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">Lihat Semua</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($activeGoals as $goal)
                                @php $pct = $goal->percentage(); @endphp
                                <div class="p-4 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                                    <div class="flex items-center justify-between text-xs mb-2">
                                        <span class="font-bold text-slate-900 dark:text-white truncate">{{ $goal->nama_goal }}</span>
                                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden mb-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400">
                                        <span>Rp {{ number_format($goal->progres, 0, ',', '.') }}</span>
                                        <span>Target: Rp {{ number_format($goal->target, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: Account Wallets, Upcoming Bills, Monthly Budget -->
            <div class="space-y-6">
                <!-- Upcoming Bills Card -->
                @if($upcomingBills->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-amber-200 dark:border-amber-900/60 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                <h2 class="text-sm font-black text-slate-900 dark:text-white">Tagihan 7 Hari Mendatang</h2>
                            </div>
                            <a href="{{ route('recurring-rules.index') }}" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline">Kelola</a>
                        </div>
                        <div class="space-y-2.5">
                            @foreach($upcomingBills as $bill)
                                @php
                                    $dt = \Carbon\Carbon::parse($bill->tanggal_berikutnya);
                                    $diff = (int) \Carbon\Carbon::today()->diffInDays($dt, false);
                                @endphp
                                <div class="p-3 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-white block">{{ $bill->catatan ?: ($bill->category?->nama_kategori ?? 'Tagihan') }}</span>
                                        <span class="text-[10px] text-amber-700 dark:text-amber-400">
                                            {{ $diff === 0 ? 'Jatuh tempo hari ini' : "Jatuh tempo dalam {$diff} hari ({$dt->format('d M')})" }}
                                        </span>
                                    </div>
                                    <span class="font-black text-red-600 dark:text-red-400">
                                        Rp {{ number_format($bill->jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Accounts Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-black text-slate-900 dark:text-white">Dompet & Akun</h2>
                        <a href="{{ route('accounts.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">Kelola</a>
                    </div>
                    <div class="space-y-3">
                        @forelse ($accounts as $acc)
                            <div class="p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs" style="background-color: {{ $acc->warna ?? '#2563EB' }}20; color: {{ $acc->warna ?? '#2563EB' }};">
                                        {{ strtoupper(substr($acc->nama_akun, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-none">{{ $acc->nama_akun }}</h3>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 capitalize">{{ str_replace('_', ' ', $acc->tipe) }}</span>
                                    </div>
                                </div>
                                <div class="text-right font-black text-sm text-slate-900 dark:text-white">
                                    Rp {{ number_format($acc->saldo, 2, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">Belum ada akun dompet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Budget Tracker Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-black text-slate-900 dark:text-white">Budgeting Bulan Ini</h2>
                        <a href="{{ route('budgets.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">Atur</a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($budgets as $b)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $b['kategori'] }}</span>
                                    <span class="font-bold {{ $b['is_over'] ? 'text-red-600 dark:text-red-400' : ($b['is_warning'] ? 'text-amber-500' : 'text-slate-600 dark:text-slate-400') }}">
                                        {{ $b['persentase'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $b['is_over'] ? 'bg-red-500' : ($b['is_warning'] ? 'bg-amber-500' : 'bg-blue-600') }}" style="width: {{ min(100, $b['persentase']) }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                    <span>Rp {{ number_format($b['terpakai'], 0, ',', '.') }}</span>
                                    <span>Limit: Rp {{ number_format($b['limit'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-xs text-slate-400 mb-2">Belum ada budget untuk bulan ini.</p>
                                <a href="{{ route('budgets.create') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
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
