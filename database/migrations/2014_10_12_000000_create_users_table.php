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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone', 15)->nullable();
            $table->string('password');

            $table->timestamp('password_changed_at')->useCurrent();
            $table->timestamp('email_verified_at')->nullable();

            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('1 - Active, 0 - Inactive');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
