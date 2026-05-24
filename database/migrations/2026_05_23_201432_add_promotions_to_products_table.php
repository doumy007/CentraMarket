<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('promotion_price', 10, 2)->nullable()->after('price');
            $table->boolean('promotion_active')->default(false)->after('promotion_price');
            $table->dateTime('promotion_start')->nullable()->after('promotion_active');
            $table->dateTime('promotion_end')->nullable()->after('promotion_start');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['promotion_price', 'promotion_active', 'promotion_start', 'promotion_end']);
        });
    }
};
