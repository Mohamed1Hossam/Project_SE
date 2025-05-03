<?php
$results = null;
$formSubmitted = false;
$errors = [];


$ZAKAT_RATE = 0.025; 
$NISAB_VALUE_USD = 5950; //(85 grams of gold * gold price per gram in the country)
$NISAB_VALUE_EUR = 5500; 
$NISAB_VALUE_EGP = 300000; 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
    $formSubmitted = true;


    if (!isset($_POST['cashAmount']) || $_POST['cashAmount'] === '') {
        $errors[] = "Cash amount is required";
    } else if ($_POST['cashAmount'] < 0) {
        $errors[] = "Cash amount cannot be negative";
    }

    if (empty($errors)) {
        $cashAmount = floatval($_POST['cashAmount']);
        $currency = $_POST['currency'] ?? 'USD';

        switch ($currency) {
            case 'EUR':
                $nisabValue = $NISAB_VALUE_EUR;
                break;
            case 'EGP':
                $nisabValue = $NISAB_VALUE_EGP;
                break;
            default:
                $nisabValue = $NISAB_VALUE_USD;
        }

        $totalWealth = $cashAmount;
        $isEligible = $totalWealth >= $nisabValue;
        $zakatAmount = $isEligible ? $totalWealth * $ZAKAT_RATE : 0;
        $donationAmount = $totalWealth * $ZAKAT_RATE;

        $results = [
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
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="zakat-calculator.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakat Calculator</title>
</head>
<body>
<div class="container">
    <h1>Zakat Calculator</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="section">
            <div class="section-title">Cash</div>
            <div class="grid">
                <div class="form-group">
                    <label for="cashAmount">Cash Amount</label>
                    <input type="number" id="cashAmount" name="cashAmount" placeholder="0.00" step="0.01" min="0"
                           value="<?php echo isset($_POST['cashAmount']) ? htmlspecialchars($_POST['cashAmount']) : '0'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="currency">Currency</label>
                    <select id="currency" name="currency">
                        <option value="USD" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                        <option value="EUR" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'EUR') ? 'selected' : ''; ?>>EUR</option>
                        <option value="EGP" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'EGP') ? 'selected' : ''; ?>>EGP (Egyptian Pound)</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" name="calculate">Calculate Zakat</button>
    </form>

    <?php if ($formSubmitted && $results && empty($errors)): ?>
        <div class="results">
            <h2>Zakat Calculation Results</h2>
            <p><strong>Total Wealth:</strong> <?php echo $results['currency']; ?> <?php echo number_format($results['totalWealth'], 2); ?></p>
            <p>
                <?php if ($results['isEligible']): ?>
                    <strong>Zakat Due:</strong> <?php echo $results['currency']; ?> <?php echo number_format($results['zakatAmount'], 2); ?><br>
                    Your wealth exceeds the Nisab threshold (<?php echo $results['currency']; ?> <?php echo number_format($results['nisabValue'], 2); ?>).
                    <span class="note">Note: The Nisab is the minimum wealth a Muslim must have to be eligible for Zakat.
                    .</span>
                    <?php else: ?>
                    Your wealth is below the Nisab threshold (<?php echo $results['currency']; ?> <?php echo number_format($results['nisabValue'], 2); ?>).<br>
                    <span class="note">Note: The Nisab is the minimum wealth a Muslim must have to be eligible for Zakat.
                    .</span>
                <?php endif; ?>
            </p>

            <div class="donate-section">
                <?php if ($results['isEligible']): ?>
                    <p><strong>Ready to fulfill your Zakat obligation?</strong></p>
                    <p>Your calculated Zakat amount is <?php echo $results['currency']; ?> <?php echo number_format($results['zakatAmount'], 2); ?>.</p>
                <?php else: ?>
                    <p><strong>Would you like to make a donation?</strong></p>
                    <p>You can still donate <?php echo $results['currency']; ?> <?php echo number_format($results['donationAmount'], 2); ?> as charity.</p>
                <?php endif; ?>
                <form action="donate.php" method="GET">
                    <input type="hidden" name="amount" value="<?php echo $results['isEligible'] ? $results['zakatAmount'] : $results['donationAmount']; ?>">
                    <input type="hidden" name="currency" value="<?php echo $results['currency']; ?>">
                    <input type="hidden" name="type" value="<?php echo $results['isEligible'] ? 'zakat' : 'charity'; ?>">
                    <button type="submit">Donate Now</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
