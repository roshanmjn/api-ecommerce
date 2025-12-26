<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 255)->index();
            $table->timestamp('expires_at');
            $table->boolean('revoked')->default(false);
            $table->unsignedBigInteger('replaced_by_token_id')->nullable();
            $table->string('created_by_ip', 45)->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('replaced_by_token_id')
                ->references('id')->on('refresh_tokens')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};


