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
            $table->string('name')->nullable(); // デフォルト値を削除し、nullable() を追加
            $table->timestamp('verification_code_created_at')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password_reset_token')->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->string('password')->nullable();
            $table->string('verification_code')->nullable();
            $table->boolean('verification_code_checked')->nullable()->default(false);
            $table->rememberToken();
            $table->timestamps();
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
