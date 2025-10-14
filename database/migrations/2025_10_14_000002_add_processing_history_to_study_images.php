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
        Schema::table('study_images', function (Blueprint $table) {
            // Store array of processing history (max 3)
            $table->json('processing_history')->nullable()->after('processed_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_images', function (Blueprint $table) {
            $table->dropColumn('processing_history');
        });
    }
};
