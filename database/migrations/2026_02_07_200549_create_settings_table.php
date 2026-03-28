<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('My POS Shop');
            $table->string('shop_phone')->nullable();
            $table->string('shop_address')->nullable();
            $table->timestamps();
        });

        // Insert default row immediately
        DB::table('settings')->insert([
            'shop_name' => 'My POS Shop',
            'shop_phone' => '0965429290',
            'shop_address' => 'Phnom Penh, Cambodia'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
