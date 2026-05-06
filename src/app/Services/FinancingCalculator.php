<?php

namespace App\Services;

class FinancingCalculator
{
    /**
     * Tabela Price: PMT = PV * (i * (1+i)^n) / ((1+i)^n - 1)
     */
    public function calculate(float $financedAmount, float $monthlyRate, int $installments): array
    {
        if ($financedAmount <= 0 || $installments <= 0) {
            return [
                'installment_value' => 0.0,
                'total_amount' => 0.0,
                'total_interest' => 0.0,
            ];
        }

        if ($monthlyRate <= 0) {
            $installmentValue = $financedAmount / $installments;
            return [
                'installment_value' => round($installmentValue, 2),
                'total_amount' => round($financedAmount, 2),
                'total_interest' => 0.0,
            ];
        }

        $factor = (1 + $monthlyRate) ** $installments;
        $installmentValue = $financedAmount * ($monthlyRate * $factor) / ($factor - 1);
        $totalAmount = $installmentValue * $installments;

        return [
            'installment_value' => round($installmentValue, 2),
            'total_amount' => round($totalAmount, 2),
            'total_interest' => round($totalAmount - $financedAmount, 2),
        ];
    }

    public function amortizationTable(float $financedAmount, float $monthlyRate, int $installments): array
    {
        $result = $this->calculate($financedAmount, $monthlyRate, $installments);
        $installmentValue = $result['installment_value'];
        $balance = $financedAmount;
        $rows = [];

        for ($i = 1; $i <= $installments; $i++) {
            $interest = $balance * $monthlyRate;
            $amortization = $installmentValue - $interest;
            $balance -= $amortization;

            $rows[] = [
                'installment' => $i,
                'value' => round($installmentValue, 2),
                'interest' => round($interest, 2),
                'amortization' => round($amortization, 2),
                'balance' => round(max(0, $balance), 2),
            ];
        }

        return $rows;
    }
}
