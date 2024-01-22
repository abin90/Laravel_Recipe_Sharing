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
        Schema::table('activity', function (Blueprint $table) {
            // Remove unwanted columns
            $table->dropColumn('activity_id');
            $table->dropColumn('activity_type');
            $table->dropColumn('type');

            // Add new columns
            $table->unsignedBigInteger('recipe_id');
            $table->date('added_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('activity_type');
            $table->string('type');

            $table->dropColumn('recipe_id');
            $table->dropColumn('added_date');
        });
    }
};
