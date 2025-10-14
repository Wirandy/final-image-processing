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
            $table->text('annotations_data')->nullable()->after('forensic_summary');
            $table->text('measurements_data')->nullable()->after('annotations_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_images', function (Blueprint $table) {
            $table->dropColumn(['annotations_data', 'measurements_data']);
        });
    }
};
