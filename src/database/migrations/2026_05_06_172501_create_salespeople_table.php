<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salespeople', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 32)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_assigned_at')->nullable();
            $table->unsignedInteger('leads_count')->default(0);
            $table->timestamps();

            $table->index(['active', 'last_assigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salespeople');
    }
};
