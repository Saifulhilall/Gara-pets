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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Nomor faktur unik untuk menelusuri sumber stok masuk.
            $table->string('invoice_number')->unique();
            $table->string('supplier_name')->nullable();
            $table->date('purchase_date');
            // Total faktur dihitung dari seluruh barang masuk.
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
