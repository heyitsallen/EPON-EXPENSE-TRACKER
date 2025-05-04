<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'tracker_id')) {
                $table->unsignedBigInteger('tracker_id')->after('id');
                $table->foreign('tracker_id')->references('id')->on('trackers')->onDelete('cascade');
            }
    
            if (!Schema::hasColumn('incomes', 'description')) {
                $table->string('description')->nullable();
            }
    
            if (!Schema::hasColumn('incomes', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0);
            }
    
            if (!Schema::hasColumn('incomes', 'date')) {
                $table->date('date')->nullable();
            }
        });
    }
    
    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['tracker_id']);
            $table->dropColumn(['tracker_id', 'description', 'amount', 'date']);
        });
    }
};
