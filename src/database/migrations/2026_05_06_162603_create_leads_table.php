<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 32);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('moto_id')->nullable()->constrained('motos')->nullOnDelete();
            $table->string('source', 32)->default('site');
            $table->string('status', 32)->default('novo');
            $table->text('notes')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('moto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
