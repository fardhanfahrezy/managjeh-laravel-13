<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Laporan Keuangan & Grafik</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Analisis visual sebaran pengeluaran dan tren arus kas bulanan.</p>
            </div>
            <!-- Month Selector -->
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
                <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()" class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-xs font-semibold py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" />
            </form>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- 4 Summary Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Pemasukan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Pemasukan</span>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                    + Rp {{ number_format($totalIncome, 2, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Periode {{ $periodeLabel }}</p>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Pengeluaran</span>
                <div class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-2">
                    - Rp {{ number_format($totalExpense, 2, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Periode {{ $periodeLabel }}</p>
            </div>

            <!-- Net Cashflow -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Arus Kas Bersih (Net)</span>
                <div class="text-xl font-bold {{ $netCashflow >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }} mt-2">
                    {{ $netCashflow >= 0 ? '+' : '' }} Rp {{ number_format($netCashflow, 2, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Surplus / Defisit</p>
            </div>

            <!-- Rata-rata Harian -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Rata-rata Pengeluaran/Hari</span>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-2">
                    Rp {{ number_format($avgDailyExpense, 2, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Estimasi harian</p>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pie Chart: Pengeluaran per Kategori -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Proporsi Pengeluaran per Kategori</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Distribusi pengeluaran bulan {{ $periodeLabel }}</p>
                </div>

                <div class="my-6 relative flex items-center justify-center min-h-[260px]">
                    @if ($expenseByCategory->isNotEmpty())
                        <canvas id="expensePieChart" class="max-h-[260px]"></canvas>
                    @else
                        <div class="text-center text-gray-400 text-xs py-12">
                            Belum ada transaksi pengeluaran pada periode ini.
                        </div>
                    @endif
                </div>

                <!-- Table Breakdown -->
                @if ($expenseByCategory->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-48 overflow-y-auto pr-1">
                        @foreach ($expenseByCategory as $item)
                            @php
                                $percent = $totalExpense > 0 ? round(($item->total / $totalExpense) * 100, 1) : 0;
                            @endphp
                            <div class="py-2 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $item->warna ?? '#F59E0B' }};"></span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item->kategori }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-400">{{ $percent }}%</span>
                                    <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Bar/Line Chart: Tren 6 Bulan Terakhir -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Tren Arus Kas (6 Bulan Terakhir)</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbandingan pemasukan vs pengeluaran antar bulan</p>
                </div>

                <div class="my-6 relative min-h-[260px]">
                    <canvas id="trendBarChart" class="max-h-[280px] w-full"></canvas>
                </div>

                <div class="flex items-center justify-center gap-6 pt-2 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span>Pemasukan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <span>Pengeluaran</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($expenseByCategory->isNotEmpty())
                // 1. Pie Chart
                const pieCtx = document.getElementById('expensePieChart');
                if (pieCtx && window.Chart) {
                    new window.Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($expenseByCategory->pluck('kategori')) !!},
                            datasets: [{
                                data: {!! json_encode($expenseByCategory->pluck('total')) !!},
                                backgroundColor: {!! json_encode($expenseByCategory->map(fn($item) => $item->warna ?? '#F59E0B')) !!},
                                borderWidth: 2,
                                borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        font: { size: 11 },
                                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
            @endif

            // 2. Bar Chart Trends
            const trendCtx = document.getElementById('trendBarChart');
            if (trendCtx && window.Chart) {
                new window.Chart(trendCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($trendMonths) !!},
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: {!! json_encode($incomeTrends) !!},
                                backgroundColor: '#10B981',
                                borderRadius: 6
                            },
                            {
                                label: 'Pengeluaran',
                                data: {!! json_encode($expenseTrends) !!},
                                backgroundColor: '#EF4444',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                                    font: { size: 11 }
                                }
                            },
                            y: {
                                grid: {
                                    color: document.documentElement.classList.contains('dark') ? '#374151' : '#f3f4f6'
                                },
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                                    font: { size: 11 },
                                    callback: function(value) {
                                        if (value >= 1000000) return 'Rp ' + (value/1000000) + ' jt';
                                        if (value >= 1000) return 'Rp ' + (value/1000) + ' rb';
                                        return 'Rp ' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
