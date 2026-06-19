<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'TaxID')) {
                $table->dropForeign(['TaxID']);
                $table->dropColumn('TaxID');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'TaxID')) {
                $table->dropForeign(['TaxID']);
                $table->dropColumn('TaxID');
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('receipts', 'TaxID')) {
                $table->foreignId('TaxID')
                    ->nullable()
                    ->after('OrderID')
                    ->constrained('taxes', 'TaxID')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('receipts', 'TaxRate')) {
                $table->decimal('TaxRate', 8, 2)->default(0.00)->after('TaxID');
            }

            if (! Schema::hasColumn('receipts', 'TaxAmount')) {
                $table->decimal('TaxAmount', 10, 2)->default(0.00)->after('TaxRate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'TaxID')) {
                $table->dropForeign(['TaxID']);
                $table->dropColumn(['TaxID', 'TaxRate', 'TaxAmount']);
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'TaxID')) {
                $table->foreignId('TaxID')
                    ->nullable()
                    ->after('Name')
                    ->constrained('taxes', 'TaxID')
                    ->nullOnDelete();
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'TaxID')) {
                $table->foreignId('TaxID')
                    ->nullable()
                    ->after('CategoryID')
                    ->constrained('taxes', 'TaxID')
                    ->nullOnDelete();
            }
        });
    }
};
