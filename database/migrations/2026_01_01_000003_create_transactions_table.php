<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $t) {
            $t->id();
            $t->string('type'); // in | out
            $t->unsignedBigInteger('amount');
            $t->string('description');
            $t->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $t->date('date')->index();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
