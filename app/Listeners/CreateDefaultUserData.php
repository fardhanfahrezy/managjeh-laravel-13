<?php

namespace App\Listeners;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;

class CreateDefaultUserData
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        // Default Accounts (Kas, Bank, e-Wallet)
        $defaultAccounts = [
            [
                'nama_akun' => 'Kas / Dompet Tunai',
                'tipe' => 'kas',
                'saldo' => 0.00,
                'warna' => '#10B981',
                'catatan' => 'Uang tunai fisik harian',
            ],
            [
                'nama_akun' => 'Rekening Bank Utama',
                'tipe' => 'bank',
                'saldo' => 0.00,
                'warna' => '#3B82F6',
                'catatan' => 'Rekening tabungan perbankan utama',
            ],
            [
                'nama_akun' => 'e-Wallet / Dompet Digital',
                'tipe' => 'e-wallet',
                'saldo' => 0.00,
                'warna' => '#8B5CF6',
                'catatan' => 'GoPay, OVO, ShopeePay, DANA, dll.',
            ],
        ];

        foreach ($defaultAccounts as $acc) {
            Account::create(array_merge($acc, ['user_id' => $user->id]));
        }

        // Default Categories
        $defaultCategories = [
            // Income
            ['nama' => 'Gaji & Upah', 'tipe' => 'income', 'warna' => '#10B981', 'icon' => 'briefcase'],
            ['nama' => 'Investasi & Bunga', 'tipe' => 'income', 'warna' => '#06B6D4', 'icon' => 'chart-line'],
            ['nama' => 'Bonus & Hadiah', 'tipe' => 'income', 'warna' => '#8B5CF6', 'icon' => 'gift'],
            ['nama' => 'Pemasukan Lainnya', 'tipe' => 'income', 'warna' => '#64748B', 'icon' => 'wallet'],

            // Expense
            ['nama' => 'Makanan & Minuman', 'tipe' => 'expense', 'warna' => '#F59E0B', 'icon' => 'utensils'],
            ['nama' => 'Transportasi', 'tipe' => 'expense', 'warna' => '#3B82F6', 'icon' => 'car'],
            ['nama' => 'Belanja & Kebutuhan', 'tipe' => 'expense', 'warna' => '#EC4899', 'icon' => 'shopping-bag'],
            ['nama' => 'Tagihan & Utilitas', 'tipe' => 'expense', 'warna' => '#EF4444', 'icon' => 'bolt'],
            ['nama' => 'Kesehatan & Medis', 'tipe' => 'expense', 'warna' => '#14B8A6', 'icon' => 'heart-pulse'],
            ['nama' => 'Hiburan & Liburan', 'tipe' => 'expense', 'warna' => '#6366F1', 'icon' => 'film'],
            ['nama' => 'Pendidikan', 'tipe' => 'expense', 'warna' => '#F97316', 'icon' => 'graduation-cap'],
            ['nama' => 'Pengeluaran Lainnya', 'tipe' => 'expense', 'warna' => '#94A3B8', 'icon' => 'credit-card'],
        ];

        foreach ($defaultCategories as $category) {
            Category::create(array_merge($category, ['user_id' => $user->id]));
        }
    }
}
