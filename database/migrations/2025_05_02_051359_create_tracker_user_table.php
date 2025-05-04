<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracker_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracker_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('position', ['owner', 'full', 'partial', 'viewer']);
            $table->timestamps();

            // Composite key for user_id and tracker_id
            $table->unique(['user_id', 'tracker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracker_user');
    }
};
