<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('user can export transactions to CSV', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'nama_akun' => 'BCA Utama']);
    $category = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Gaji', 'tipe' => 'income']);

    Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'jumlah' => 5000000,
        'tipe' => 'income',
        'tanggal' => '2026-08-01',
    ]);

    $response = $this->actingAs($user)->get(route('transactions.export'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('user can download CSV import template', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('transactions.template'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('user can import valid CSV transactions', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'nama_akun' => 'Kas / Dompet Tunai', 'saldo' => 500000]);
    $catExpense = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Makanan & Minuman', 'tipe' => 'expense']);
    $catIncome = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Gaji', 'tipe' => 'income']);

    $csvContent = "Tanggal,Akun,Tipe,Kategori,Jumlah,Catatan\n".
                  "2026-08-01,Kas / Dompet Tunai,expense,Makanan & Minuman,50000,Makan Siang\n".
                  "2026-08-02,Kas / Dompet Tunai,income,Gaji,3000000,Gaji Project\n";

    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    $response = $this->actingAs($user)->post(route('transactions.import'), [
        'file' => $file,
    ]);

    $response->assertRedirect(route('transactions.index'));

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'catatan' => 'Makan Siang',
        'jumlah' => 50000,
    ]);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'catatan' => 'Gaji Project',
        'jumlah' => 3000000,
    ]);

    // Account balance updated: 500k - 50k + 3000k = 3450k
    expect((float) $account->fresh()->saldo)->toEqual(3450000.0);
});

test('import CSV strictly rejects rows with unknown or ambiguous account and category names', function () {
    $user = User::factory()->create();
    Account::factory()->create(['user_id' => $user->id, 'nama_akun' => 'Kas Tunai']);
    Category::factory()->create(['user_id' => $user->id, 'nama' => 'Makanan', 'tipe' => 'expense']);

    $csvContent = "Tanggal,Akun,Tipe,Kategori,Jumlah,Catatan\n".
                  "2026-08-01,Akun Tidak Dikenal,expense,Makanan,50000,Test\n".
                  "2026-08-02,Kas Tunai,expense,Kategori Asing,75000,Test 2\n";

    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    $response = $this->actingAs($user)->post(route('transactions.import'), [
        'file' => $file,
    ]);

    $response->assertSessionHasErrors('file');
});
