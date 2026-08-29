<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('budgets.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Edit Budget</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui batas limit budget kategori {{ $budget->category->nama }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Periode Bulan -->
                <div>
                    <label for="periode" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Periode Bulan (Format: YYYY-MM)</label>
                    <input type="month" id="periode" name="periode" value="{{ old('periode', $budget->periode) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('periode')" />
                </div>

                <!-- Kategori Pengeluaran -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kategori Pengeluaran</label>
                    <select id="category_id" name="category_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $budget->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>

                <!-- Limit Bulanan -->
                <div>
                    <label for="limit_bulanan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Batas Limit Bulanan (Rp)</label>
                    <div class="relative mt-1.5 rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-gray-400 sm:text-sm font-bold">Rp</span>
                        </div>
                        <input type="number" step="0.01" id="limit_bulanan" name="limit_bulanan" value="{{ old('limit_bulanan', $budget->limit_bulanan) }}" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white pl-12 pr-4 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-semibold" required />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('limit_bulanan')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('budgets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                        Perbarui Budget
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
