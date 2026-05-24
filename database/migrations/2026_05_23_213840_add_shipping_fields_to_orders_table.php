<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('notes');
            $table->string('shipping_email')->nullable()->after('shipping_name');
            $table->string('shipping_country')->nullable()->after('shipping_email');
            $table->string('shipping_city')->nullable()->after('shipping_country');
            $table->string('shipping_street')->nullable()->after('shipping_city');
            $table->string('shipping_number')->nullable()->after('shipping_street');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name', 'shipping_email', 'shipping_country',
                'shipping_city', 'shipping_street', 'shipping_number',
            ]);
        });
    }
};
