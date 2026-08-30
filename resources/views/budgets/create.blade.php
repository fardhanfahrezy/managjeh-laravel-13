<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('budgets.index') }}" aria-label="Kembali ke daftar anggaran" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Tetapkan Budget Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Tentukan batas maksimal pengeluaran bulanan per kategori.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('budgets.store') }}" class="space-y-6">
                @csrf

                <!-- Periode Bulan -->
                <div>
                    <x-input-label for="periode" value="Periode Bulan (Format: YYYY-MM)" />
                    <x-text-input type="month" id="periode" name="periode" :value="old('periode', $periode)" class="mt-1.5 block w-full font-semibold" required />
                    <x-input-error class="mt-2" :messages="$errors->get('periode')" />
                </div>

                <!-- Kategori Pengeluaran -->
                <div>
                    <x-input-label for="category_id" value="Kategori Pengeluaran" />
                    <select id="category_id" name="category_id" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold" required>
                        <option value="">-- Pilih Kategori Pengeluaran --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', request('category_id')) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>

                <!-- Limit Bulanan -->
                <div>
                    <x-input-label for="limit_bulanan" value="Batas Limit Bulanan (Rp)" />
                    <div class="relative mt-1.5 rounded-xl shadow-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-slate-400 sm:text-sm font-bold">Rp</span>
                        </div>
                        <x-text-input type="number" step="0.01" id="limit_bulanan" name="limit_bulanan" :value="old('limit_bulanan')" placeholder="Contoh: 1500000" class="block w-full pl-12 font-bold" required />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('limit_bulanan')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('budgets.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <x-primary-button>
                        Simpan Budget
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
