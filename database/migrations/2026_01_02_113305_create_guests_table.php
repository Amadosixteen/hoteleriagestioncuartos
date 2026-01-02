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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', ['dni', 'carnet_extranjeria', 'otro']);
            $table->string('document_number');
            $table->string('custom_document_type')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['masculino', 'femenino', 'otro'])->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
