<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pembelians MODIFY status_pembayaran ENUM('paid', 'unpaid', 'overdue', 'cancelled', 'partial') NOT NULL DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE pembelians SET status_pembayaran = 'unpaid' WHERE status_pembayaran = 'partial'");
        DB::statement("ALTER TABLE pembelians MODIFY status_pembayaran ENUM('paid', 'unpaid', 'overdue', 'cancelled') NOT NULL DEFAULT 'unpaid'");
    }
};
