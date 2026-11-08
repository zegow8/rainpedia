<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chats');

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->enum('sender', ['user', 'admin'])->default('user');
            $table->text('message');
            $table->boolean('is_read')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
