<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('goals.index') }}" aria-label="Kembali ke daftar tujuan finansial" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Buat Goal Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Rencanakan target tabungan dan impian finansial Anda.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('goals.store') }}" class="space-y-6">
                @csrf

                <!-- Nama Goal -->
                <div>
                    <x-input-label for="nama_goal" value="Nama Tujuan / Impian" />
                    <x-text-input id="nama_goal" name="nama_goal" type="text" class="mt-1.5 block w-full font-semibold" :value="old('nama_goal')" required placeholder="Contoh: Beli Laptop Baru, Dana Darurat, Umroh" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_goal')" />
                </div>

                <!-- Target Nominal -->
                <div>
                    <x-input-label for="target" value="Target Nominal (Rp)" />
                    <div class="relative mt-1.5 rounded-xl shadow-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-slate-400 sm:text-sm font-bold">Rp</span>
                        </div>
                        <x-text-input id="target" name="target" type="number" min="1" step="1" class="block w-full pl-12 font-bold" :value="old('target')" required placeholder="Contoh: 15000000" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('target')" />
                </div>

                <!-- Tenggat Waktu -->
                <div>
                    <x-input-label for="deadline" value="Target Tenggat Waktu (Opsional)" />
                    <x-text-input id="deadline" name="deadline" type="date" class="mt-1.5 block w-full font-semibold" :value="old('deadline')" />
                    <x-input-error class="mt-2" :messages="$errors->get('deadline')" />
                </div>

                <!-- Catatan -->
                <div>
                    <x-input-label for="catatan" value="Catatan / Rencana Alokasi (Opsional)" />
                    <textarea id="catatan" name="catatan" rows="3" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600" placeholder="Contoh: Nabung 500rb per bulan dari gaji">{{ old('catatan') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('goals.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <x-primary-button>
                        Simpan Goal
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
