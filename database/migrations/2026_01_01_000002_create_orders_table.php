<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('code', 20)->unique();
            $t->string('customer_name');
            $t->string('customer_phone')->nullable();
            $t->string('note')->nullable();
            $t->unsignedBigInteger('total')->default(0);
            $t->string('status')->default('pending')->index(); // pending | completed | cancelled
            $t->timestamp('confirmed_at')->nullable();
            $t->timestamps();
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('product_name');
            $t->unsignedInteger('qty');
            $t->unsignedInteger('price'); // harga saat dipesan
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
