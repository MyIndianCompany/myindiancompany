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
        Schema::create('agent_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('file');
            $table->string('mime_type');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agent_id')->references('id')->on('agents');
            $table->foreign('bank_account_id')->references('id')->on('agent_bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_documents');
    }
};
