<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('goals.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Kembali ke Goals</a>
            <span class="text-gray-300">/</span>
            <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Buat Tujuan Finansial Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
                <form method="POST" action="{{ route('goals.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="nama_goal" value="Nama Tujuan / Impian" />
                        <x-text-input id="nama_goal" name="nama_goal" type="text" class="mt-1 block w-full" :value="old('nama_goal')" required placeholder="Contoh: Beli Laptop Baru, Dana Darurat, Umroh" />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_goal')" />
                    </div>

                    <div>
                        <x-input-label for="target" value="Target Nominal (Rp)" />
                        <x-text-input id="target" name="target" type="number" min="1" step="1" class="mt-1 block w-full" :value="old('target')" required placeholder="Contoh: 15000000" />
                        <x-input-error class="mt-2" :messages="$errors->get('target')" />
                    </div>

                    <div>
                        <x-input-label for="deadline" value="Target Tenggat Waktu (Opsional)" />
                        <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline')" />
                        <x-input-error class="mt-2" :messages="$errors->get('deadline')" />
                    </div>

                    <div>
                        <x-input-label for="catatan" value="Catatan / Rencana Alokasi (Opsional)" />
                        <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: Nabung 500rb per bulan dari gaji">{{ old('catatan') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('goals.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">Batal</a>
                        <x-primary-button>Simpan Goal</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
