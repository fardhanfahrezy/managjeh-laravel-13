<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Akun & Dompet</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola rekening bank, e-wallet, uang kas tunai, dan kartu kredit Anda.</p>
            </div>
            <a href="{{ route('accounts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Akun Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Total Net Balance Banner -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Akumulasi Saldo</span>
                <div class="text-3xl font-extrabold mt-1">
                    Rp {{ number_format($totalSaldo, 2, ',', '.') }}
                </div>
            </div>
            <div class="text-xs text-slate-300">
                <span class="font-bold text-emerald-400">{{ $accounts->count() }}</span> Akun Terdaftar
            </div>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($accounts as $acc)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider {{ $acc->tipe === 'kartu_kredit' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200' : ($acc->tipe === 'bank' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200' : ($acc->tipe === 'e-wallet' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200')) }}">
                                {{ str_replace('_', ' ', $acc->tipe) }}
                            </span>
                            <div class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $acc->warna ?? '#10B981' }};"></div>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $acc->nama_akun }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $acc->catatan ?? 'Tidak ada catatan tambahan' }}</p>
                        </div>

                        <div class="mt-5">
                            <span class="text-xs text-gray-400 font-medium">Saldo Saat Ini</span>
                            <div class="text-2xl font-black {{ $acc->saldo < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white' }} tracking-tight">
                                Rp {{ number_format($acc->saldo, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <span class="text-xs text-gray-400">{{ $acc->transactions_count }} transaksi</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('accounts.edit', $acc) }}" class="p-1.5 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="Edit Akun">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('accounts.destroy', $acc) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada akun dompet yang dibuat.</p>
                    <a href="{{ route('accounts.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl">
                        Tambah Akun Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
