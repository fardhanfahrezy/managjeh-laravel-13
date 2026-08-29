<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Edit Akun</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui informasi akun {{ $account->nama_akun }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('accounts.update', $account) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Akun -->
                <div>
                    <label for="nama_akun" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Akun / Dompet</label>
                    <input type="text" id="nama_akun" name="nama_akun" value="{{ old('nama_akun', $account->nama_akun) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_akun')" />
                </div>

                <!-- Tipe Akun -->
                <div>
                    <label for="tipe" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tipe Akun</label>
                    <select id="tipe" name="tipe" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        <option value="kas" {{ old('tipe', $account->tipe) === 'kas' ? 'selected' : '' }}>Kas / Uang Tunai</option>
                        <option value="bank" {{ old('tipe', $account->tipe) === 'bank' ? 'selected' : '' }}>Rekening Bank</option>
                        <option value="e-wallet" {{ old('tipe', $account->tipe) === 'e-wallet' ? 'selected' : '' }}>E-Wallet / Dompet Digital</option>
                        <option value="kartu_kredit" {{ old('tipe', $account->tipe) === 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Saldo Info -->
                <div>
                    <span class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Saldo Saat Ini</span>
                    <div class="mt-1.5 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-sm font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($account->saldo, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Saldo dimutasi secara otomatis oleh riwayat transaksi.</p>
                </div>

                <!-- Warna Penanda -->
                <div>
                    <label for="warna" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Warna Penanda</label>
                    <div class="flex items-center gap-3 mt-1.5">
                        <input type="color" id="warna" name="warna" value="{{ old('warna', $account->warna ?? '#10B981') }}" class="w-10 h-10 p-1 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer bg-transparent" />
                        <span class="text-xs text-gray-400">Pilih warna untuk membedakan kartu akun.</span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('warna')" />
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" rows="3" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan', $account->catatan) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('accounts.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                        Perbarui Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
