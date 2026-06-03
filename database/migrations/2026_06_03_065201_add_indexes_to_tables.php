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
            Schema::table('orders', function (Blueprint $table) {
                $table->index('status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('created_at');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->index('is_active');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->index('is_available');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->index('is_open');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->index('status');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropIndex(['is_open']);
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropIndex(['is_available']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {}
    }
};
