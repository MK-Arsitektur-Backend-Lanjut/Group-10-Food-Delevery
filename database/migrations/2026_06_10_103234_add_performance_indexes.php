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
        try {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->index(['restaurant_id', 'menu_category_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('menu_categories', function (Blueprint $table) {
                $table->index(['restaurant_id', 'sort_order']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('drivers', function (Blueprint $table) {
                $table->index('status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('delivery_histories', function (Blueprint $table) {
                $table->index('driver_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->index(['status', 'is_open']);
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_open']);
        });

        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->dropIndex(['driver_id']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id', 'sort_order']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id', 'menu_category_id']);
        });
    }
};
