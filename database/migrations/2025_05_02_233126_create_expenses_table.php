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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracker_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable(); // Optional category link
            $table->enum('type', ['expense', 'income']);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('attachment_path')->nullable(); // For proof (e.g., receipt image)
            $table->timestamps();

            // Foreign Keys
            $table->foreign('tracker_id')->references('id')->on('trackers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null'); // If category deleted, keep expense
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
