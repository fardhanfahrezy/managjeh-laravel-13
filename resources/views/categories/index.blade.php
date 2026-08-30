<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Kategori Transaksi</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola kategori pengeluaran dan pemasukan untuk pengelompokan laporan.</p>
            </div>
            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Tambah Kategori</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ tab: 'expense' }">
        <!-- Tab Switcher -->
        <div class="flex border-b border-slate-200 dark:border-slate-800">
            <button @click="tab = 'expense'" :class="tab === 'expense' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-black' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                <span>Kategori Pengeluaran ({{ $expenseCategories->count() }})</span>
            </button>
            <button @click="tab = 'income'" :class="tab === 'income' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-black' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>Kategori Pemasukan ({{ $incomeCategories->count() }})</span>
            </button>
        </div>

        <!-- Expense Categories -->
        <div x-show="tab === 'expense'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($expenseCategories as $cat)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm" style="background-color: {{ $cat->warna ?? '#EF4444' }}20; color: {{ $cat->warna ?? '#EF4444' }};">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->warna ?? '#EF4444' }};"></span>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $cat->nama }}</h3>
                            <span class="text-xs text-slate-400">{{ $cat->transactions_count }} transaksi</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('categories.edit', $cat) }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:text-red-600 rounded-xl transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-400 font-medium">
                    Belum ada kategori pengeluaran.
                </div>
            @endforelse
        </div>

        <!-- Income Categories -->
        <div x-show="tab === 'income'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" style="display: none;">
            @forelse ($incomeCategories as $cat)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm" style="background-color: {{ $cat->warna ?? '#10B981' }}20; color: {{ $cat->warna ?? '#10B981' }};">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->warna ?? '#10B981' }};"></span>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $cat->nama }}</h3>
                            <span class="text-xs text-slate-400">{{ $cat->transactions_count }} transaksi</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('categories.edit', $cat) }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:text-red-600 rounded-xl transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-400 font-medium">
                    Belum ada kategori pemasukan.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
