<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('RoleID');
            $table->string('RoleName');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id('PermissionID');
            $table->string('PermissionName');
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('RoleID')->constrained('roles', 'RoleID')->onDelete('cascade');
            $table->foreignId('PermissionID')->constrained('permissions', 'PermissionID')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            $table->foreignId('RoleID')
                ->nullable()
                ->after('PasswordHash')
                ->constrained('roles', 'RoleID');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['RoleID']);
            $table->dropColumn('RoleID');
        });
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
