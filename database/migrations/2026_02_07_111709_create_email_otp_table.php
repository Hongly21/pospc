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
        Schema::create('email_otp', function (Blueprint $table) {
            $table->id(); // Standard 'id' from your image
            $table->foreignId('UserID')->references('UserID')->on('users')->onDelete('cascade');
            $table->string('email', 100);
            $table->string('otp', 6);
            $table->dateTime('expires_at');
            $table->tinyInteger('used')->default(0);
            $table->timestamps(); // created_at from your image
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_otp');
    }
};
