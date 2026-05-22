<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vk_incoming_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('channel')->nullable()->index();
            $table->json('payload');
            $table->boolean('is_delivered')->default(false)->index();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vk_incoming_messages');
    }
};
