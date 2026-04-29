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
        Schema::table('ipcrfs', function (Blueprint $table) {
            $table->string('google_drive_file_id')->nullable()->after('scanned_file_path');
            $table->text('google_drive_link')->nullable()->after('google_drive_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ipcrfs', function (Blueprint $table) {
            $table->dropColumn(['google_drive_file_id', 'google_drive_link']);
        });
    }
};
