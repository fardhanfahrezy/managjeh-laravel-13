<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Tagihan & Transaksi Berulang') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Kelola langganan bulanan, tagihan rutin (listrik, internet, sewa), dan pemasukan terjadwal secara otomatis.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('recurring-rules.process-now') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl shadow-xs transition">
                        <svg class="w-3.5 h-3.5 text-emerald-600 animate-spin" style="animation-duration: 4s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Proses Jatuh Tempo Sekarang</span>
                    </button>
                </form>
                <a href="{{ route('recurring-rules.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Aturan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="text-xs font-semibold uppercase bg-gray-50 dark:bg-gray-750 text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
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
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-750">
                            @forelse($rules as $rule)
                                @php
                                    $nextDate = \Carbon\Carbon::parse($rule->tanggal_berikutnya);
                                    $isDue = $nextDate->isPast() || $nextDate->isToday();
                                    $daysUntil = (int) \Carbon\Carbon::today()->diffInDays($nextDate, false);
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $rule->catatan ?: ($rule->category?->nama_kategori ?? 'Tagihan') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-medium text-gray-900 dark:text-gray-200">
                                            {{ $rule->account?->nama_akun ?? '-' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $rule->category?->nama_kategori ?? '-' }} ({{ ucfirst($rule->category?->tipe ?? 'expense') }})
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            @switch($rule->frekuensi)
                                                @case('daily') Harian @break
                                                @case('weekly') Mingguan @break
                                                @case('monthly') Bulanan @break
                                                @case('yearly') Tahunan @break
                                                @default {{ ucfirst($rule->frekuensi) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($rule->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-semibold {{ $isDue ? 'text-rose-600 font-bold' : ($daysUntil <= 3 ? 'text-amber-600' : 'text-gray-700 dark:text-gray-300') }}">
                                            {{ $nextDate->translatedFormat('d F Y') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
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
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition {{ $rule->is_active ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 hover:bg-gray-200' }}">
                                                {{ $rule->is_active ? '● Aktif' : '○ Nonaktif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('recurring-rules.edit', $rule) }}" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('recurring-rules.destroy', $rule) }}" onsubmit="return confirm('Hapus aturan tagihan berulang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 transition">
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
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
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
