<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('items', 'harga_dasar')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('harga_dasar');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('items', 'harga_dasar')) {
            Schema::table('items', function (Blueprint $table) {
                $table->decimal('harga_dasar', 15, 2)->unsigned()->default(0);
            });
        }
    }
};
