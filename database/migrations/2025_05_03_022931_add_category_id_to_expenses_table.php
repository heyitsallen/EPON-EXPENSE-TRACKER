<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToExpensesTable extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Check if the column exists before adding
            if (!Schema::hasColumn('expenses', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable(); // Add category_id column
                $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the foreign key and column if we rollback the migration
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
