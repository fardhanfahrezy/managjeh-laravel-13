<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                {{ __('Kesehatan Finansial & Forecast') }}
            </h2>
            <div class="text-sm text-slate-500 dark:text-slate-400">
                Analisis & Proyeksi Bulan {{ Carbon\Carbon::now()->translatedFormat('F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Section 1: Financial Health Score Gauge & Grade -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-100 dark:border-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    
                    <!-- Gauge Meter Display -->
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <div class="relative flex items-center justify-center w-40 h-40">
                            <!-- Circular background stroke -->
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-200 dark:text-slate-700" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="text-{{ $health['grade_color'] }}-500 transition-all duration-1000 ease-out" stroke-width="3.5" stroke-dasharray="{{ $health['overall_score'] }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="absolute flex flex-col items-center">
                                <span class="text-4xl font-extrabold text-slate-800 dark:text-slate-100">{{ $health['overall_score'] }}</span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">/ 100</span>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $health['grade_color'] }}-100 text-{{ $health['grade_color'] }}-800 dark:bg-{{ $health['grade_color'] }}-900/40 dark:text-{{ $health['grade_color'] }}-300">
                                {{ $health['grade'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Breakdown Metrics -->
                    <div class="md:col-span-2 space-y-4">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg">Indikator Kesehatan Finansial</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Savings Rate Metric -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Rasio Tabungan</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $health['savings_rate'] }}% (Target &ge;20%)</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $health['savings_rate'] * 5) }}%"></div>
                                </div>
                                <div class="mt-1 text-right text-[10px] text-slate-400">Skor: {{ $health['metrics']['savings']['weighted'] }}/30</div>
                            </div>

                            <!-- Emergency Fund Metric -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Dana Darurat (Likuid)</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $health['emergency_fund_months'] }} bln (Target 3-6)</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($health['emergency_fund_months'] / 6) * 100) }}%"></div>
                                </div>
                                <div class="mt-1 text-right text-[10px] text-slate-400">Saldo Likuid: Rp {{ number_format($health['liquid_balance'], 0, ',', '.') }}</div>
                            </div>

                            <!-- Budget Adherence -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Kepatuhan Budget</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $health['budget_adherence_rate'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $health['budget_adherence_rate'] }}%"></div>
                                </div>
                                <div class="mt-1 text-right text-[10px] text-slate-400">Skor: {{ $health['metrics']['budget']['weighted'] }}/20</div>
                            </div>

                            <!-- Debt Ratio -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Kartu Kredit / Hutang</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Rp {{ number_format($health['credit_card_balance'], 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $health['metrics']['debt']['score'] }}%"></div>
                                </div>
                                <div class="mt-1 text-right text-[10px] text-slate-400">Skor: {{ $health['metrics']['debt']['weighted'] }}/20</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Section 2: Spending Forecast -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-100 dark:border-slate-700 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Proyeksi Pengeluaran Akhir Bulan (Spending Forecast)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Dihitung berdasarkan rata-rata kecepatan transaksi harian (run-rate) bulan ini.</p>
                    </div>

                    @if($forecast['has_enough_data'])
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $forecast['status_color'] }}-100 text-{{ $forecast['status_color'] }}-800 dark:bg-{{ $forecast['status_color'] }}-900/40 dark:text-{{ $forecast['status_color'] }}-300">
                            Status: {{ $forecast['status_label'] }}
                        </span>
                    @endif
                </div>

                @if(!$forecast['has_enough_data'])
                    <div class="p-6 text-center rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-dashed border-slate-200 dark:border-slate-700">
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="font-semibold text-slate-700 dark:text-slate-300">Data Pengeluaran Belum Cukup</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">Proyeksi pengeluaran baru akan ditampilkan secara akurat mulai hari ke-3 di setiap bulannya untuk menghindari angka proyeksi yang terdistorsi di awal bulan.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                            <div class="text-xs text-slate-500 dark:text-slate-400">Rata-rata Pengeluaran / Hari</div>
                            <div class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1">Rp {{ number_format($forecast['daily_rate'], 0, ',', '.') }}</div>
                            <div class="text-[11px] text-slate-400 mt-1">Hari ke-{{ $forecast['days_elapsed'] }} dari {{ $forecast['total_days_in_month'] }} hari</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                            <div class="text-xs text-slate-500 dark:text-slate-400">Proyeksi Total Akhir Bulan</div>
                            <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">Rp {{ number_format($forecast['projected_total'], 0, ',', '.') }}</div>
                            <div class="text-[11px] text-slate-400 mt-1">Pengeluaran saat ini: Rp {{ number_format($forecast['current_expense'], 0, ',', '.') }}</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50">
                            <div class="text-xs text-slate-500 dark:text-slate-400">Total Limit Budget Bulanan</div>
                            <div class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1">Rp {{ number_format($forecast['total_budget'], 0, ',', '.') }}</div>
                            <div class="text-[11px] font-semibold text-{{ $forecast['status_color'] }}-600 dark:text-{{ $forecast['status_color'] }}-400 mt-1">
                                Proyeksi pemakaian: {{ $forecast['projected_percentage'] }}%
                            </div>
                        </div>
                    </div>

                    @if(count($forecast['category_forecasts']) > 0)
                        <div class="mt-6 space-y-3">
                            <h4 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Proyeksi Per Kategori Budget</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($forecast['category_forecasts'] as $cat)
                                    <div class="p-3 rounded-lg border border-slate-100 dark:border-slate-700/70 bg-white dark:bg-slate-900/30 flex items-center justify-between">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ $cat['category_warna'] }}"></span>
                                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $cat['category_nama'] }}</span>
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                Terpakai: Rp {{ number_format($cat['spent'], 0, ',', '.') }} &rarr; Proyeksi: <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($cat['projected'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-{{ $cat['status_color'] }}-100 text-{{ $cat['status_color'] }}-800 dark:bg-{{ $cat['status_color'] }}-900/40 dark:text-{{ $cat['status_color'] }}-300">
                                                {{ $cat['percentage'] }}% ({{ $cat['status_label'] }})
                                            </span>
                                            <div class="text-[10px] text-slate-400 mt-1">Limit: Rp {{ number_format($cat['limit'], 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Section 3: Recommendations -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    Saran & Recommendations Keuangan
                </h3>

                <div class="space-y-3">
                    @foreach($health['recommendations'] as $rec)
                        <div class="p-4 rounded-xl flex items-start gap-3 border {{ $rec['type'] === 'success' ? 'bg-emerald-50 border-emerald-100 dark:bg-emerald-950/20 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200' : ($rec['type'] === 'danger' ? 'bg-rose-50 border-rose-100 dark:bg-rose-950/20 dark:border-rose-800/40 text-rose-900 dark:text-rose-200' : 'bg-amber-50 border-amber-100 dark:bg-amber-950/20 dark:border-amber-800/40 text-amber-900 dark:text-amber-200') }}">
                            <div class="mt-0.5">
                                @if($rec['type'] === 'success')
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($rec['type'] === 'danger')
                                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">{{ $rec['title'] }}</h4>
                                <p class="text-xs mt-0.5 opacity-90">{{ $rec['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
