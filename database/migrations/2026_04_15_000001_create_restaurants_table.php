<?php

use App\Enums\RestaurantStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('phone', 20)->nullable();
            $table->string('status', 20)->default(RestaurantStatus::ACTIVE->value);
            $table->boolean('is_open')->default(false);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('is_open');
            $table->index(['status', 'is_open']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
