<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('ProductID'); // Primary Key
            $table->foreignId('CategoryID')->constrained('categories', 'CategoryID')->onDelete('cascade');

            $table->string('Name', 100);
            $table->string('Brand', 100)->nullable();
            $table->string('Model', 100)->nullable();

            $table->decimal('CostPrice', 10, 2);
            $table->decimal('SellPrice', 10, 2);

            $table->integer('WarrantyMonths')->default(0);
            $table->string('Barcode', 100)->nullable()->unique();
            $table->text('Description')->nullable();
            $table->string('Image', 255)->nullable();
            //status
            $table->boolean('Status')->default(1);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
