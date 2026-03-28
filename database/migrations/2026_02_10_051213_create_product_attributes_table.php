<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id('AttributeID');

            $table->foreignId('ProductID')->constrained('products', 'ProductID')->onDelete('cascade');

            $table->string('AttributeName', 100); 
            $table->string('AttributeValue', 255);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
