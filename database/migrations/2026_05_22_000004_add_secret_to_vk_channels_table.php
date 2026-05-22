<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vk_channels', function (Blueprint $table): void {
            $table->string('secret')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vk_channels', function (Blueprint $table): void {
            $table->dropColumn('secret');
        });
    }
};
