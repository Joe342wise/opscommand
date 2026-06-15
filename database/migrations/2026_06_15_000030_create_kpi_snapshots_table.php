<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('kpi_name');
            $table->decimal('value', 10, 2);
            $table->string('unit')->nullable();
            $table->date('snapshot_date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('kpi_name');
            $table->index('snapshot_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_snapshots');
    }
};
