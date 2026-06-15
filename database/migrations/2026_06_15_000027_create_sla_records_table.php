<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('sla_name');
            $table->decimal('target_value', 10, 2);
            $table->decimal('actual_value', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->boolean('is_met')->default(false);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->timestamps();

            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_records');
    }
};
