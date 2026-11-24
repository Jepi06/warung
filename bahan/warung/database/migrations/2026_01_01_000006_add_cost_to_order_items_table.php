<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $t) {
            $t->unsignedInteger('cost')->default(0)->after('price'); // harga beli saat terjual
        });

        // Isi data lama dengan harga beli barang saat ini
        DB::table('order_items')->update([
            'cost' => DB::raw('COALESCE((SELECT cost_price FROM products WHERE products.id = order_items.product_id), 0)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $t) {
            $t->dropColumn('cost');
        });
    }
};
