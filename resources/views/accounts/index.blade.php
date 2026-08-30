<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Akun & Dompet</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola rekening bank, e-wallet, uang kas tunai, dan kartu kredit Anda.</p>
            </div>
            <a href="{{ route('accounts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Tambah Akun Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Total Net Balance Hero Card -->
        <div class="p-6 sm:p-7 rounded-3xl bg-slate-900 text-white shadow-xl shadow-slate-900/10 border border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative overflow-hidden">
            <!-- Background Decorative Activity / Waveform -->
            <div class="absolute -right-12 -bottom-12 opacity-10 pointer-events-none text-white">
                <svg class="w-[250px] h-[250px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
            </div>

            <div class="relative z-10">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Akumulasi Saldo Bersih</span>
                <div class="text-3xl sm:text-4xl font-black mt-1 tracking-tight text-white">
                    Rp {{ number_format($totalSaldo, 2, ',', '.') }}
                </div>
            </div>
            <div class="text-xs font-bold text-slate-400 flex items-center gap-2 relative z-10">
                <span class="px-3 py-1.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    {{ $accounts->count() }} Akun Dompet Terdaftar
                </span>
            </div>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($accounts as $acc)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider {{ $acc->tipe === 'kartu_kredit' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($acc->tipe === 'bank' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : ($acc->tipe === 'e-wallet' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300')) }}">
                                {{ str_replace('_', ' ', $acc->tipe) }}
                            </span>
                            <div class="w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 shadow-xs" style="background-color: {{ $acc->warna ?? '#2563EB' }};"></div>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $acc->nama_akun }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $acc->catatan ?? 'Tidak ada catatan tambahan' }}</p>
                        </div>

                        <div class="mt-5">
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Saldo Saat Ini</span>
                            <div class="text-2xl font-black {{ $acc->saldo < 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }} tracking-tight mt-0.5">
                                Rp {{ number_format($acc->saldo, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">{{ $acc->transactions_count }} mutasi dicatat</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('accounts.edit', $acc) }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition" title="Edit Akun">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('accounts.destroy', $acc) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-xl transition" title="Hapus Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-800">
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Belum ada akun dompet yang dibuat.</p>
                    <a href="{{ route('accounts.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-md">
                        + Tambah Akun Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

