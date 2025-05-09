<?php
require_once 'config.php';

class ZakatModel {
    private $ZAKAT_RATE = 0.025;
    private $NISAB_VALUES = [
        'USD' => 5950,
        'EUR' => 5500,
        'EGP' => 300000
    ];

    public function calculateZakat($cashAmount, $currency) {
        $errors = [];

        // Validation
        if ($cashAmount === '' || $cashAmount === null) {
            $errors[] = "Cash amount is required";
        } elseif ($cashAmount < 0) {
            $errors[] = "Cash amount cannot be negative";
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Calculation
        $cashAmount = floatval($cashAmount);
        $currency = $currency ?? 'USD';
        $nisabValue = $this->NISAB_VALUES[$currency] ?? $this->NISAB_VALUES['USD'];

        $totalWealth = $cashAmount;
        $isEligible = $totalWealth >= $nisabValue;
        $zakatAmount = $isEligible ? $totalWealth * $this->ZAKAT_RATE : 0;
        $donationAmount = $totalWealth * $this->ZAKAT_RATE;

        return [
            'errors' => [],
            'totalWealth' => $totalWealth,
            'zakatAmount' => $zakatAmount,
            'donationAmount' => $donationAmount,
            'isEligible' => $isEligible,
            'cashAmount' => $cashAmount,
            'nisabValue' => $nisabValue,
            'currency' => $currency
        ];
    }
}
?>