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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_pembelian');
            $table->decimal('grand_total', 15, 2);
            $table->text('metode_pembayaran')->nullable();
            $table->enum('status_pembayaran', ['paid', 'unpaid', 'overdue', 'cancelled'])->default('unpaid');
            $table->enum('status_setor', ['sudah', 'belum'])->default('belum');
            $table->text('bukti_setor')->nullable();
            $table->text('alasan_cancel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
