<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('simulator_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_interest_rate', 6, 4)->default(0.0250);
            $table->unsignedSmallInteger('min_installments')->default(12);
            $table->unsignedSmallInteger('max_installments')->default(48);
            $table->unsignedSmallInteger('installments_step')->default(6);
            $table->decimal('min_down_payment_percent', 5, 2)->default(10.00);
            $table->decimal('max_down_payment_percent', 5, 2)->default(80.00);
            $table->string('disclaimer_text', 500)->default('Valor estimado, sujeito a aprovação de crédito pela financeira.');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulator_settings');
    }
};
