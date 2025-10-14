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
            $table->dropColumn(['annotated_path', 'annotations_data', 'measurements_data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_images', function (Blueprint $table) {
            $table->string('annotated_path')->nullable()->after('processed_path');
            $table->text('annotations_data')->nullable()->after('forensic_summary');
            $table->text('measurements_data')->nullable()->after('annotations_data');
        });
    }
};
