<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('motos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');
            $table->unsignedSmallInteger('displacement_cc')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('short_description');
            $table->text('description')->nullable();
            $table->string('image');
            $table->json('gallery')->nullable();
            $table->json('highlights')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['active', 'category']);
            $table->index('featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motos');
    }
};
