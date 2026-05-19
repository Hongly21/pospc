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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id('TaxID');
            $table->string('Name', 100)->unique();
            $table->decimal('Rate', 8, 2)->default(0.00);
            $table->text('Description')->nullable();
            $table->boolean('Status')->default(1);
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('TaxID')->nullable()->after('Name')->constrained('taxes', 'TaxID')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('TaxID')->nullable()->after('CategoryID')->constrained('taxes', 'TaxID')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['TaxID']);
            $table->dropColumn('TaxID');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['TaxID']);
            $table->dropColumn('TaxID');
        });

        Schema::dropIfExists('taxes');
    }
};
