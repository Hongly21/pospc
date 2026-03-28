<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
        
            $table->id('CustomerID');

            $table->string('Name', 100);
            $table->string('PhoneNumber', 20)->unique();
            $table->string('Email', 100)->nullable();
            $table->text('Address')->nullable();

            // Loyalty Points (Optional but recommended)
            $table->integer('Points')->default(0);
            $table->tinyInteger('status')->default(1);


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
