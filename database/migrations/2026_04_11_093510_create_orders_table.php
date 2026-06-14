<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('restaurant_id');
                $table->unsignedBigInteger('driver_id')->nullable();
                $table->string('status');
                $table->decimal('total_price', 10, 2)->default(0);
                $table->timestamps();
            });

            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'restaurant_id')) {
                $table->unsignedBigInteger('restaurant_id')->default(1)->after('user_id');
            }

            if (! Schema::hasColumn('orders', 'driver_id')) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('restaurant_id');
            }

            if (! Schema::hasColumn('orders', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'restaurant_id')) {
                $table->dropColumn('restaurant_id');
            }

            if (Schema::hasColumn('orders', 'driver_id')) {
                $table->dropColumn('driver_id');
            }

            if (Schema::hasColumn('orders', 'total_price')) {
                $table->dropColumn('total_price');
            }
        });
    }
};
