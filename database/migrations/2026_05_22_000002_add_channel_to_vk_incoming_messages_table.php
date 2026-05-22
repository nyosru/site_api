<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vk_incoming_messages', function (Blueprint $table): void {
            $table->string('channel')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('vk_incoming_messages', function (Blueprint $table): void {
            $table->dropColumn('channel');
        });
    }
};
