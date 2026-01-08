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
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('overtime_rate_per_hour', 8, 2)->default(0)->after('logo');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('overtime_charge', 8, 2)->default(0)->after('total_price');
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('overtime_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('overtime_rate_per_hour');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['overtime_charge', 'overtime_hours']);
        });
    }
};
