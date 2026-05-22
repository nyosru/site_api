<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vk_channels', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('tag')->unique();
            $table->unsignedBigInteger('group_id')->unique();
            $table->string('confirmation_code');
            $table->string('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vk_channels');
    }
};
