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
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('room_number');
        });
        
        // Asignar una posición inicial basada en el ID para las habitaciones existentes
        // Esto es útil para que el drag & drop funcione bien desde el inicio
        \App\Models\Room::all()->each(function ($room, $index) {
            $room->update(['position' => $index]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
