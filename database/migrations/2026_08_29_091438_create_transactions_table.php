<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->enum('tipe', ['income', 'expense', 'transfer', 'saving']);
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->string('attachment_url')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'tanggal']);
            $table->index(['account_id', 'tanggal']);
            $table->index(['category_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
