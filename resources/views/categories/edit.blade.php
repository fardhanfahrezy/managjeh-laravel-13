<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" aria-label="Kembali ke daftar kategori" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Edit Kategori</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Perbarui label kategori {{ $category->nama }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Kategori -->
                <div>
                    <x-input-label for="nama" value="Nama Kategori" />
                    <x-text-input type="text" id="nama" name="nama" :value="old('nama', $category->nama)" class="mt-1.5 block w-full font-semibold" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                </div>

                <!-- Tipe Kategori -->
                <div>
                    <x-input-label for="tipe" value="Tipe Kategori" />
                    <select id="tipe" name="tipe" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold" required>
                        <option value="expense" {{ old('tipe', $category->tipe) === 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                        <option value="income" {{ old('tipe', $category->tipe) === 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Warna Penanda -->
                <div>
                    <x-input-label for="warna" value="Warna Penanda" />
                    <div class="flex items-center gap-3 mt-1.5">
                        <input type="color" id="warna" name="warna" value="{{ old('warna', $category->warna ?? '#F59E0B') }}" class="w-10 h-10 p-1 rounded-xl border border-slate-300 dark:border-slate-700 cursor-pointer bg-transparent" />
                        <span class="text-xs text-slate-400">Pilih warna untuk grafik laporan dan badge.</span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('warna')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('categories.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <x-primary-button>
                        Perbarui Kategori
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
