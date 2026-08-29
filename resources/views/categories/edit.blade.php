<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Edit Kategori</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui label kategori {{ $category->nama }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Kategori -->
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Kategori</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $category->nama) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                </div>

                <!-- Tipe Kategori -->
                <div>
                    <label for="tipe" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tipe Kategori</label>
                    <select id="tipe" name="tipe" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        <option value="expense" {{ old('tipe', $category->tipe) === 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                        <option value="income" {{ old('tipe', $category->tipe) === 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Warna Penanda -->
                <div>
                    <label for="warna" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Warna Penanda</label>
                    <div class="flex items-center gap-3 mt-1.5">
                        <input type="color" id="warna" name="warna" value="{{ old('warna', $category->warna ?? '#10B981') }}" class="w-10 h-10 p-1 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer bg-transparent" />
                        <span class="text-xs text-gray-400">Pilih warna untuk grafik laporan dan badge.</span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('warna')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('categories.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                        Perbarui Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
