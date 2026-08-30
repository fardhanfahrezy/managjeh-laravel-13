<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.index') }}" aria-label="Kembali ke daftar akun" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Tambah Akun Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Buat dompet baru untuk mencatat transaksi dan memantau saldo.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('accounts.store') }}" class="space-y-6">
                @csrf

                <!-- Nama Akun -->
                <div>
                    <x-input-label for="nama_akun" value="Nama Akun / Dompet" />
                    <x-text-input type="text" id="nama_akun" name="nama_akun" :value="old('nama_akun')" placeholder="Contoh: BCA Payroll, GoPay, Dompet Tunai" class="mt-1.5 block w-full font-semibold" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_akun')" />
                </div>

                <!-- Tipe Akun -->
                <div>
                    <x-input-label for="tipe" value="Tipe Akun" />
                    <select id="tipe" name="tipe" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold" required>
                        <option value="kas" {{ old('tipe') === 'kas' ? 'selected' : '' }}>Kas / Uang Tunai</option>
                        <option value="bank" {{ old('tipe') === 'bank' ? 'selected' : '' }}>Rekening Bank</option>
                        <option value="e-wallet" {{ old('tipe') === 'e-wallet' ? 'selected' : '' }}>E-Wallet / Dompet Digital</option>
                        <option value="kartu_kredit" {{ old('tipe') === 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit (Bisa Saldo Negatif)</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Saldo Awal -->
                <div>
                    <x-input-label for="saldo" value="Saldo Awal (Rp)" />
                    <div class="relative mt-1.5 rounded-xl shadow-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-slate-400 text-sm font-bold">Rp</span>
                        </div>
                        <x-text-input type="number" step="0.01" id="saldo" name="saldo" :value="old('saldo', 0)" placeholder="0" class="block w-full pl-12 font-bold" required />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('saldo')" />
                    <p class="text-xs text-slate-400 mt-1">Saldo awal sebelum pencatatan transaksi dimulai.</p>
                </div>

                <!-- Warna Penanda -->
                <div>
                    <x-input-label for="warna" value="Warna Penanda" />
                    <div class="flex items-center gap-3 mt-1.5">
                        <input type="color" id="warna" name="warna" value="{{ old('warna', '#2563EB') }}" class="w-10 h-10 p-1 rounded-xl border border-slate-300 dark:border-slate-700 cursor-pointer bg-transparent" />
                        <span class="text-xs text-slate-400">Pilih warna untuk membedakan kartu akun di dashboard.</span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('warna')" />
                </div>

                <!-- Catatan -->
                <div>
                    <x-input-label for="catatan" value="Catatan (Opsional)" />
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Contoh: No. rekening, catatan kegunaan..." class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm">{{ old('catatan') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('accounts.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <x-primary-button>
                        Simpan Akun
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
