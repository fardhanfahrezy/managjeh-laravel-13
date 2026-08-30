<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl text-slate-900 dark:text-white leading-tight">
                    {{ __('Tagihan & Transaksi Berulang') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Kelola langganan bulanan, tagihan rutin (listrik, internet, sewa), dan pemasukan terjadwal secara otomatis.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('recurring-rules.process-now') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl shadow-xs transition">
                        <svg class="w-3.5 h-3.5 text-blue-600 animate-spin" style="animation-duration: 4s;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Proses Jatuh Tempo Sekarang</span>
                    </button>
                </form>
                <a href="{{ route('recurring-rules.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Aturan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="text-xs font-bold uppercase bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th scope="col" class="px-6 py-4">Deskripsi / Catatan</th>
                                <th scope="col" class="px-6 py-4">Akun & Kategori</th>
                                <th scope="col" class="px-6 py-4">Frekuensi</th>
                                <th scope="col" class="px-6 py-4">Nominal</th>
                                <th scope="col" class="px-6 py-4">Jatuh Tempo Berikutnya</th>
                                <th scope="col" class="px-6 py-4 text-center">Status</th>
                                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($rules as $rule)
                                @php
                                    $nextDate = \Carbon\Carbon::parse($rule->tanggal_berikutnya);
                                    $isDue = $nextDate->isPast() || $nextDate->isToday();
                                    $daysUntil = (int) \Carbon\Carbon::today()->diffInDays($nextDate, false);
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                        {{ $rule->catatan ?: ($rule->category?->nama_kategori ?? 'Tagihan') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-semibold text-slate-900 dark:text-slate-200">
                                            {{ $rule->account?->nama_akun ?? '-' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $rule->category?->nama_kategori ?? '-' }} ({{ ucfirst($rule->category?->tipe ?? 'expense') }})
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            @switch($rule->frekuensi)
                                                @case('daily') Harian @break
                                                @case('weekly') Mingguan @break
                                                @case('monthly') Bulanan @break
                                                @case('yearly') Tahunan @break
                                                @default {{ ucfirst($rule->frekuensi) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900 dark:text-white">
                                        Rp {{ number_format($rule->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold {{ $isDue ? 'text-red-600' : ($daysUntil <= 3 ? 'text-amber-500' : 'text-slate-700 dark:text-slate-300') }}">
                                            {{ $nextDate->translatedFormat('d F Y') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-medium">
                                            @if($isDue)
                                                Jatuh tempo hari ini/lewat
                                            @else
                                                {{ $daysUntil }} hari lagi
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form method="POST" action="{{ route('recurring-rules.toggle', $rule) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider transition {{ $rule->is_active ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200' }}">
                                                {{ $rule->is_active ? '● Aktif' : '○ Nonaktif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('recurring-rules.edit', $rule) }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl transition" title="Edit Tagihan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('recurring-rules.destroy', $rule) }}" onsubmit="return confirm('Hapus aturan tagihan berulang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-400 hover:text-red-600 rounded-xl transition" title="Hapus Tagihan">
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
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">
                                        Belum ada aturan tagihan atau pemasukan berulang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

