<?php

namespace Tests\Unit;

use App\Services\FinancingCalculator;
use PHPUnit\Framework\TestCase;

class FinancingCalculatorTest extends TestCase
{
    public function test_calculates_installment_with_interest(): void
    {
        $calc = new FinancingCalculator();
        $result = $calc->calculate(financedAmount: 10000, monthlyRate: 0.025, installments: 24);

        // PMT correto pela Tabela Price: 10000 * (0.025 * 1.025^24) / (1.025^24 - 1) ≈ 559,13
        $this->assertEqualsWithDelta(559.13, $result['installment_value'], 0.05);
        $this->assertGreaterThan(10000, $result['total_amount']);
        $this->assertGreaterThan(0, $result['total_interest']);
    }

    public function test_handles_zero_interest(): void
    {
        $calc = new FinancingCalculator();
        $result = $calc->calculate(10000, 0, 10);

        $this->assertEquals(1000.0, $result['installment_value']);
        $this->assertEquals(10000.0, $result['total_amount']);
        $this->assertEquals(0.0, $result['total_interest']);
    }

    public function test_returns_zero_for_invalid_input(): void
    {
        $calc = new FinancingCalculator();

        $this->assertEquals(0.0, $calc->calculate(0, 0.025, 12)['installment_value']);
        $this->assertEquals(0.0, $calc->calculate(10000, 0.025, 0)['installment_value']);
        $this->assertEquals(0.0, $calc->calculate(-1000, 0.025, 12)['installment_value']);
    }

    public function test_amortization_table_balances_to_zero(): void
    {
        $calc = new FinancingCalculator();
        $rows = $calc->amortizationTable(10000, 0.025, 12);

        $this->assertCount(12, $rows);
        // Saldo final pode ter centavos por arredondamento — tolera até R$ 1,00
        $this->assertEqualsWithDelta(0.0, $rows[11]['balance'], 1.00);
        $this->assertEqualsWithDelta(250.0, $rows[0]['interest'], 0.01);
    }
}
