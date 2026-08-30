<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" aria-label="Kembali ke daftar transaksi" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Edit Transaksi</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Penyesuaian transaksi akan otomatis menghitung ulang saldo akun terkait.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ tipe: '{{ old('tipe', $transaction->tipe) }}' }">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Tipe Transaksi Selector -->
                <div>
                    <x-input-label value="Tipe Transaksi" class="mb-2" />
                    <div class="grid grid-cols-3 gap-2 p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60">
                        <button type="button" @click="tipe = 'expense'" :class="tipe === 'expense' ? 'bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-400 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 font-semibold'" class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Pengeluaran</span>
                        </button>
                        <button type="button" @click="tipe = 'income'" :class="tipe === 'income' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 font-semibold'" class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Pemasukan</span>
                        </button>
                        <button type="button" @click="tipe = 'transfer'" :class="tipe === 'transfer' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 font-semibold'" class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Transfer</span>
                        </button>
                    </div>
                    <input type="hidden" name="tipe" :value="tipe" />
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Jumlah / Nominal -->
                <div>
                    <x-input-label for="jumlah" value="Nominal Transaksi (Rp)" />
                    <div class="relative mt-1.5 rounded-xl shadow-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-slate-400 sm:text-sm font-bold">Rp</span>
                        </div>
                        <x-text-input type="number" step="0.01" id="jumlah" name="jumlah" :value="old('jumlah', $transaction->jumlah)" class="block w-full pl-12 font-bold text-base" required />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('jumlah')" />
                </div>

                <!-- Akun Asal -->
                <div>
                    <x-input-label for="account_id">
                        <span x-show="tipe === 'transfer'">Akun Sumber / Asal</span>
                        <span x-show="tipe !== 'transfer'">Akun Dompet</span>
                    </x-input-label>
                    <select id="account_id" name="account_id" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold" required>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id', $transaction->account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->nama_akun }} (Saldo: Rp {{ number_format($acc->saldo, 2, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
                </div>

                <!-- Akun Tujuan (Khusus Transfer) -->
                <div x-show="tipe === 'transfer'" style="{{ $transaction->tipe === 'transfer' ? '' : 'display: none;' }}">
                    <x-input-label for="destination_account_id" value="Akun Tujuan" />
                    <select id="destination_account_id" name="destination_account_id" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold">
                        <option value="">-- Pilih Akun Penerima --</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('destination_account_id', $transaction->destination_account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->nama_akun }} (Saldo: Rp {{ number_format($acc->saldo, 2, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('destination_account_id')" />
                </div>

                <!-- Kategori -->
                <div x-show="tipe !== 'transfer'">
                    <x-input-label value="Kategori" />
                    
                    <select x-show="tipe === 'expense'" id="category_id_expense" name="category_id" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold" :disabled="tipe !== 'expense'">
                        <option value="">-- Pilih Kategori Pengeluaran --</option>
                        @foreach ($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>

                    <select x-show="tipe === 'income'" id="category_id_income" name="category_id" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm font-semibold" :disabled="tipe !== 'income'" style="display: none;">
                        <option value="">-- Pilih Kategori Pemasukan --</option>
                        @foreach ($incomeCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>

                <!-- Tanggal -->
                <div>
                    <x-input-label for="tanggal" value="Tanggal Transaksi" />
                    <x-text-input type="date" id="tanggal" name="tanggal" :value="old('tanggal', $transaction->tanggal->format('Y-m-d'))" class="mt-1.5 block w-full font-semibold" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                </div>

                <!-- Catatan -->
                <div>
                    <x-input-label for="catatan" value="Catatan (Opsional)" />
                    <textarea id="catatan" name="catatan" rows="3" class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-xs focus:border-blue-600 focus:ring-2 focus:ring-blue-600 text-sm">{{ old('catatan', $transaction->catatan) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <!-- Lampiran -->
                <div>
                    <x-input-label for="attachment" value="Ganti Lampiran Bukti (Opsional)" />
                    @if ($transaction->attachment_url)
                        <div class="text-xs text-slate-500 dark:text-slate-400 my-2 flex items-center gap-1.5">
                            <span>Lampiran saat ini:</span>
                            <a href="{{ Storage::url($transaction->attachment_url) }}" target="_blank" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                                Lihat Lampiran
                            </a>
                        </div>
                    @endif
                    <input type="file" id="attachment" name="attachment" accept="image/*,.pdf" class="mt-1.5 block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-950/60 file:text-blue-600 dark:file:text-blue-400 hover:file:bg-blue-100" />
                    <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <x-primary-button>
                        Perbarui Transaksi
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
