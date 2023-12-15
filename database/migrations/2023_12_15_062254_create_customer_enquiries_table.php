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
        Schema::create('customer_enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->longText('message');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('service')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_enquiries');
    }
};
