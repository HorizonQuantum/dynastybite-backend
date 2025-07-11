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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->string('payment_code');
            $table->integer('total_price');
            $table->string('note_order')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('type_order_id')->constrained('type_orders');
            $table->foreignId('status_id')->constrained('order_statuses');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->foreignId('product_type_id')->constrained('type_products');
            $table->string('address');
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('delivery_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
