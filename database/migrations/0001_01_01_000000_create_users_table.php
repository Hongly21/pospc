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
        Schema::create('users', function (Blueprint $table) {
            // 1. Primary Key 'UserID' (Not 'id')
            $table->id('UserID');

            // 2. Exact columns from your diagram
            $table->string('Username', 50);
            $table->string('Email', 100)->unique();
            $table->string('PasswordHash', 255); // Matches diagram (not 'password')
            $table->string('Role', 20);
            $table->string('PhoneNumber', 20)->nullable();
            $table->string('UserImage', 255)->nullable();

            // 3. Status Enum
            $table->enum('Status', ['Pending', 'Approved', 'Reject'])->default('Pending');

            // 4. Action Fields
            $table->string('ActionBy', 30)->nullable();
            $table->timestamp('ActionAt')->nullable();

            // 5. Timestamps (CreatedAt is in your diagram)
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamps(); // Adds updated_at (good to keep for Laravel)
        });

        // Keep the sessions/reset tables below (Laravel needs them)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
