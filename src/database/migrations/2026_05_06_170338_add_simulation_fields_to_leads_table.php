<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('vehicle_price', 10, 2)->nullable()->after('moto_id');
            $table->decimal('down_payment', 10, 2)->nullable()->after('vehicle_price');
            $table->decimal('financed_amount', 10, 2)->nullable()->after('down_payment');
            $table->unsignedSmallInteger('installments')->nullable()->after('financed_amount');
            $table->decimal('interest_rate', 6, 4)->nullable()->after('installments');
            $table->decimal('installment_value', 10, 2)->nullable()->after('interest_rate');
            $table->decimal('total_amount', 10, 2)->nullable()->after('installment_value');
            $table->boolean('whatsapp_clicked')->default(false)->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_price',
                'down_payment',
                'financed_amount',
                'installments',
                'interest_rate',
                'installment_value',
                'total_amount',
                'whatsapp_clicked',
            ]);
        });
    }
};
