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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('OrderID'); // Primary Key
            $table->dateTime('OrderDate');

            $table->enum('Status', ['Pending', 'Paid', 'Cancelled', 'Partial', 'Unpaid'])->default('Paid');

            $table->decimal('TotalAmount', 10, 2);
            $table->enum('PaymentType', ['Cash', 'QR', 'Card']);

            $table->foreignId('UserID')->constrained('users', 'UserID');
            $table->unsignedBigInteger('CustomerID')->nullable();
            $table->foreign('CustomerID')->references('CustomerID')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('orderdetails', function (Blueprint $table) {

            $table->id('OrderDetailID'); // Primary Key

            // Link to Order
            $table->foreignId('OrderID')->constrained('orders', 'OrderID')->onDelete('cascade');
            // Link to Product
            $table->foreignId('ProductID')->constrained('products', 'ProductID');

            $table->integer('Quantity');
            $table->decimal('Subtotal', 10, 2);

            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id('ReceiptID');
            $table->foreignId('OrderID')->constrained('orders', 'OrderID');
            $table->string('ReceiptNo', 50)->unique();
            $table->enum('PaymentMethod', ['Cash', 'QR', 'Card']);
            $table->decimal('PaidAmount', 10, 2);
            $table->decimal('ChangeAmount', 10, 2);
            $table->dateTime('CreatedAt')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('orderdetails');
        Schema::dropIfExists('orders');
    }
};
