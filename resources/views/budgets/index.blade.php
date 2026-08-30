<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Anggaran & Budgeting</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kontrol pengeluaran bulanan per kategori dan pantau batas toleransi budget.</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Month Filter -->
                <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-xs font-bold py-2.5 px-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600" />
                </form>

                <a href="{{ route('budgets.create', ['periode' => $periode]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Set Budget Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Budget Overall Summary Card (Hero bg-slate-900) -->
        <div class="p-6 sm:p-7 rounded-3xl bg-slate-900 text-white shadow-xl shadow-slate-900/10 border border-slate-800 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Anggaran ({{ $periodeLabel }})</span>
                    <div class="text-3xl sm:text-4xl font-black mt-1 tracking-tight text-white">
                        Rp {{ number_format($totalBudget, 2, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-4 text-xs text-slate-300 mt-3 font-medium">
                        <div>Terpakai: <span class="font-bold text-white">Rp {{ number_format($totalTerpakai, 2, ',', '.') }}</span></div>
                        <div>&bull;</div>
                        <div>Sisa: <span class="font-bold {{ $totalSisa < 0 ? 'text-red-400' : 'text-emerald-400' }}">Rp {{ number_format($totalSisa, 2, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="w-full md:w-64 bg-slate-800/80 p-4 rounded-2xl border border-slate-700">
                    <div class="flex justify-between text-xs font-bold mb-2">
                        <span>Total Realisasi</span>
                        <span class="{{ $totalPersentase > 100 ? 'text-red-400' : ($totalPersentase >= 80 ? 'text-amber-400' : 'text-blue-400') }}">{{ $totalPersentase }}%</span>
                    </div>
                    <div class="w-full bg-slate-700 h-2.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $totalPersentase > 100 ? 'bg-red-500' : ($totalPersentase >= 80 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ min(100, $totalPersentase) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Cards List -->
        <div class="space-y-4">
            <h2 class="text-base font-black text-slate-900 dark:text-white">Alokasi Budget per Kategori</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($budgets as $b)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <!-- Header Card -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs" style="background-color: {{ $b['warna'] }}20; color: {{ $b['warna'] }};">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $b['warna'] }};"></span>
                                    </div>
                                    <h3 class="font-bold text-base text-slate-900 dark:text-white">{{ $b['kategori'] }}</h3>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('budgets.edit', $b['model']) }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition" title="Edit Budget">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('budgets.destroy', $b['model']) }}" onsubmit="return confirm('Hapus limit budget ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-xl transition" title="Hapus Budget">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Progress Bar & Status -->
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-slate-500 dark:text-slate-400">Terpakai: Rp {{ number_format($b['terpakai'], 0, ',', '.') }}</span>
                                    <span class="{{ $b['is_over'] ? 'text-red-600 dark:text-red-400' : ($b['is_warning'] ? 'text-amber-500' : 'text-blue-600 dark:text-blue-400') }}">
                                        {{ $b['persentase'] }}%
                                    </span>
                                </div>

                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $b['is_over'] ? 'bg-red-500' : ($b['is_warning'] ? 'bg-amber-500' : 'bg-blue-600') }}" style="width: {{ min(100, $b['persentase']) }}%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] pt-1">
                                    <span class="text-slate-400 font-medium">Limit: Rp {{ number_format($b['limit'], 0, ',', '.') }}</span>
                                    <span class="font-bold {{ $b['sisa'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-600 dark:text-slate-300' }}">
                                        {{ $b['sisa'] < 0 ? 'Over: Rp ' . number_format(abs($b['sisa']), 0, ',', '.') : 'Sisa: Rp ' . number_format($b['sisa'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Warning Badge -->
                            @if ($b['is_over'])
                                <div class="mt-4 p-3 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 text-xs font-bold text-red-700 dark:text-red-300 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Peringatan: Pengeluaran telah melebihi batas anggaran!</span>
                                </div>
                            @elseif ($b['is_warning'])
                                <div class="mt-4 p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-xs font-bold text-amber-700 dark:text-amber-300 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Perhatian: Pengeluaran sudah mendekati batas limit ({{ $b['persentase'] }}%).</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-800">
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Belum ada alokasi budget untuk periode {{ $periodeLabel }}.</p>
                        <a href="{{ route('budgets.create', ['periode' => $periode]) }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-md">
                            + Tetapkan Budget Sekarang
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Unbudgeted Categories Quick List -->
        @if ($unbudgetedCategories->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1.5">Kategori Pengeluaran Belum Dianggarkan:</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Tambahkan batas budget untuk kategori berikut agar pengeluaran tetap terkontrol:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($unbudgetedCategories as $cat)
                        <a href="{{ route('budgets.create', ['periode' => $periode, 'category_id' => $cat->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:border-blue-600 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $cat->warna ?? '#94A3B8' }};"></span>
                            <span>{{ $cat->nama }}</span>
                            <span class="text-blue-600 font-bold">+</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

