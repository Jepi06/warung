<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('debtors', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone')->nullable();
            $t->timestamps();
        });

        Schema::create('debt_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('debtor_id')->constrained()->cascadeOnDelete();
            $t->string('type'); // debt = kasbon bertambah | payment = dibayar
            $t->unsignedBigInteger('amount');
            $t->string('note')->nullable();
            $t->date('date')->index();
            $t->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });

        Schema::table('transactions', function (Blueprint $t) {
            $t->foreignId('debt_entry_id')->nullable()->after('order_id')
                ->constrained('debt_entries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            $t->dropConstrainedForeignId('debt_entry_id');
        });
        Schema::dropIfExists('debt_entries');
        Schema::dropIfExists('debtors');
    }
};
