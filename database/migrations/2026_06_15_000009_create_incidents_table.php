<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreignId('owner_id')->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('severity', ['P1', 'P2', 'P3', 'P4'])->default('P3');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'investigating', 'resolved', 'closed'])->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->index('status');
            $table->index('severity');
            $table->index('owner_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
