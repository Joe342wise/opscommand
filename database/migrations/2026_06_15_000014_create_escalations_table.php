<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('incident_id')->nullable();
            $table->foreignId('owner_id')->constrained('users')->nullOnDelete();
            $table->foreignId('target_team_id')->constrained('teams')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index('status');
            $table->index('owner_id');
            $table->index('target_team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalations');
    }
};
