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
        Schema::create('agent_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('agent_id')->unsigned();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('type')->nullable();
            $table->string('ifsc');
            $table->string('swift_code')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agent_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_bank_accounts');
    }
};
