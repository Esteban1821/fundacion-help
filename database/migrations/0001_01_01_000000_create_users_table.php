<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('username')->unique(); // ej. pperez
            $table->string('cedula')->unique();
            $table->string('email')->unique();
            $table->string('password'); 
            $table->enum('role', ['usuario', 'soporte', 'admin'])->default('usuario');
            $table->boolean('requires_password_change')->default(true); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};