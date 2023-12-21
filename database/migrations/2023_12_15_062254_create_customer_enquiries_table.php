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
            $table->unsignedBigInteger('service')->nullable();
            $table->unsignedBigInteger('service_variant')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->longText('message')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service')->references('id')->on('services');
            $table->foreign('service_variant')->references('id')->on('service_variants');
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
