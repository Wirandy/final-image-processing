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
            $table->string('annotated_path')->nullable()->after('processed_path');
            $table->json('forensic_analysis')->nullable()->after('features_text');
            $table->text('forensic_summary')->nullable()->after('forensic_analysis');
            $table->integer('injury_count')->default(0)->after('forensic_summary');
            $table->string('severity_level')->nullable()->after('injury_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_images', function (Blueprint $table) {
            $table->dropColumn([
                'annotated_path',
                'forensic_analysis',
                'forensic_summary',
                'injury_count',
                'severity_level',
            ]);
        });
    }
};
