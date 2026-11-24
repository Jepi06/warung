<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            $t->foreignId('product_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            $t->unsignedInteger('qty')->nullable()->after('product_id'); // jumlah yang ditambahkan ke stok
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            $t->dropConstrainedForeignId('product_id');
            $t->dropColumn('qty');
        });
    }
};
