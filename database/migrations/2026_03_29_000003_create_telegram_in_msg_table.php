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
        Schema::create('telegram_in_msg', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('telegram_user_id')->nullable()->index();
            $table->unsignedBigInteger('telegram_message_id')->nullable()->index();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('language_code', 12)->nullable();
            $table->text('text')->nullable();
            $table->string('command', 64)->nullable()->index();
            $table->boolean('is_start')->default(false)->index();
            $table->string('bot_token_hash', 64)->nullable()->index();
            $table->json('payload');
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_in_msg');
    }
};
