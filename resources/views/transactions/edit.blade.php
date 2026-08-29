<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Edit Transaksi</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Penyesuaian transaksi akan otomatis menghitung ulang saldo akun terkait.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ tipe: '{{ old('tipe', $transaction->tipe) }}' }">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Tipe Transaksi Selector -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe Transaksi</label>
                    <div class="grid grid-cols-3 gap-2 p-1 rounded-xl bg-gray-100 dark:bg-gray-900">
                        <button type="button" @click="tipe = 'expense'" :class="tipe === 'expense' ? 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 font-medium'" class="py-2.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Pengeluaran</span>
                        </button>
                        <button type="button" @click="tipe = 'income'" :class="tipe === 'income' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 font-medium'" class="py-2.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Pemasukan</span>
                        </button>
                        <button type="button" @click="tipe = 'transfer'" :class="tipe === 'transfer' ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 font-medium'" class="py-2.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Transfer</span>
                        </button>
                    </div>
                    <input type="hidden" name="tipe" :value="tipe" />
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Jumlah / Nominal -->
                <div>
                    <label for="jumlah" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nominal Transaksi (Rp)</label>
                    <div class="relative mt-1.5 rounded-xl shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-gray-400 sm:text-sm font-bold">Rp</span>
                        </div>
                        <input type="number" step="0.01" id="jumlah" name="jumlah" value="{{ old('jumlah', $transaction->jumlah) }}" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white pl-12 pr-4 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-base font-semibold" required />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('jumlah')" />
                </div>

                <!-- Akun Asal -->
                <div>
                    <label for="account_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <span x-show="tipe === 'transfer'">Akun Sumber / Asal</span>
                        <span x-show="tipe !== 'transfer'">Akun Dompet</span>
                    </label>
                    <select id="account_id" name="account_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
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
                    <label for="destination_account_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Akun Tujuan</label>
                    <select id="destination_account_id" name="destination_account_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
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
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kategori</label>
                    
                    <select x-show="tipe === 'expense'" id="category_id_expense" name="category_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" :disabled="tipe !== 'expense'">
                        <option value="">-- Pilih Kategori Pengeluaran --</option>
                        @foreach ($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>

                    <select x-show="tipe === 'income'" id="category_id_income" name="category_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" :disabled="tipe !== 'income'" style="display: none;">
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
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal Transaksi</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $transaction->tanggal->format('Y-m-d')) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" rows="3" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan', $transaction->catatan) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <!-- Lampiran -->
                <div>
                    <label for="attachment" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Ganti Lampiran Bukti (Opsional)</label>
                    @if ($transaction->attachment_url)
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                            Lampiran saat ini: <a href="{{ Storage::url($transaction->attachment_url) }}" target="_blank" class="text-emerald-600 hover:underline">Lihat Lampiran</a>
                        </div>
                    @endif
                    <input type="file" id="attachment" name="attachment" accept="image/*,.pdf" class="mt-1.5 block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
                    <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                        Perbarui Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
