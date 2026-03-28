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
        Schema::dropIfExists('categories');

        Schema::create('categories', function (Blueprint $table) {

            $table->id('CategoryID');
            $table->string('Name', 100);
            //status
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
