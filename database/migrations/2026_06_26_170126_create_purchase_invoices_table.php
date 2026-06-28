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
            $table->string('invoice_number')->unique();
            $table->string('supplier_name')->nullable();
            $table->date('purchase_date');
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
