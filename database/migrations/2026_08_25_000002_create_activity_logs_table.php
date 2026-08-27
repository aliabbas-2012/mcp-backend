<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('task_id')->nullable();
            $table->string('action'); // CREATE, UPDATE, MOVE, DELETE, SEED
            $table->enum('actor', ['USER', 'AI_MCP'])->default('USER');
            $table->text('details');
            $table->integer('execution_ms')->default(10);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
