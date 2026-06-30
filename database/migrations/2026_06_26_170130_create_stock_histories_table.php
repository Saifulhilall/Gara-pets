<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    
            // Tipe membedakan stok masuk, keluar, dan penyesuaian manual.
            $table->enum('type', ['masuk', 'keluar', 'penyesuaian']);
            $table->integer('quantity');
            // Simpan posisi stok sebelum dan sesudah perubahan untuk audit.
            $table->integer('stock_before');
            $table->integer('stock_after');
    
            // Sumber dan reference_id mengarah ke transaksi, faktur, atau penyesuaian.
            $table->string('source')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
    
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
