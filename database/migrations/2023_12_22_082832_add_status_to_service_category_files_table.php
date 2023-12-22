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
        Schema::table('service_category_files', function (Blueprint $table) {
            $table->enum('type', ['thumbnail', 'slider'])->nullable()->after('mime_type');
            $table->enum('status', ['active', 'inactive'])->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_category_files', function (Blueprint $table) {
            //
        });
    }
};
