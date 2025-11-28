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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_invoice');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('grand_total', 15, 2);
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
        Schema::dropIfExists('invoices');
    }
};
