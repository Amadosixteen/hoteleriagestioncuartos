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
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('email')->unique()->after('surnames')->nullable(); // Nullable initially to avoid issues with existing data
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('id')->constrained('sellers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn('seller_id');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
