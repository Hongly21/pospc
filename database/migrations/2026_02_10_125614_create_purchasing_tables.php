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
        // 1. SUPPLIERS TABLE
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('SupplierID'); // Primary Key
            $table->string('Name', 150);
            $table->string('Contact', 100);
            $table->string('Address', 255)->nullable();
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });

        // 2. PURCHASES TABLE
        Schema::create('purchases', function (Blueprint $table) {
            $table->id('PurchaseID'); // Primary Key

            // Foreign Key to Suppliers
            $table->foreignId('SupplierID')->constrained('suppliers', 'SupplierID')->onDelete('cascade');

            $table->date('Date');
            $table->decimal('Total', 10, 2);
            $table->timestamps();
        });

        // 3. PURCHASE DETAILS TABLE
        Schema::create('purchasedetails', function (Blueprint $table) {
            $table->id('PurchaseDetailID'); // Primary Key

            // Link to Purchases
            $table->foreignId('PurchaseID')->constrained('purchases', 'PurchaseID')->onDelete('cascade');

            // Link to Products (Make sure 'products' table exists!)
            $table->foreignId('ProductID')->constrained('products', 'ProductID');

            $table->integer('Qty');
            $table->decimal('CostPrice', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchasing_tables');
    }
};
