<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakat Calculator</title>
    <link rel="stylesheet" href="zakat-calculator.css">
</head>
<body>
<div class="container">
    <h1>Zakat Calculator</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"] ?? '') ?>">
        <div class="section">
            <div class="section-title">Cash</div>
            <div class="grid">
                <div class="form-group">
                    <label for="cashAmount">Cash Amount</label>
                    <input type="number" id="cashAmount" name="cashAmount" placeholder="0.00" step="0.01" min="0"
                           value="<?= isset($_POST['cashAmount']) ? htmlspecialchars($_POST['cashAmount']) : '0'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="currency">Currency</label>
                    <select id="currency" name="currency">
                        <option value="USD" <?= (isset($_POST['currency']) && $_POST['currency'] === 'USD') ? 'selected' : '' ?>>USD</option>
                        <option value="EUR" <?= (isset($_POST['currency']) && $_POST['currency'] === 'EUR') ? 'selected' : '' ?>>EUR</option>
                        <option value="EGP" <?= (isset($_POST['currency']) && $_POST['currency'] === 'EGP') ? 'selected' : '' ?>>EGP (Egyptian Pound)</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" name="calculate">Calculate Zakat</button>
    </form>

    <?php if (!empty($formSubmitted) && !empty($results) && empty($errors)): ?>
        <div class="results">
            <h2>Zakat Calculation Results</h2>
            <p><strong>Total Wealth:</strong> <?= $results['currency']; ?> <?= number_format($results['totalWealth'], 2); ?></p>
            <p>
                <?php if ($results['isEligible']): ?>
                    <strong>Zakat Due:</strong> <?= $results['currency']; ?> <?= number_format($results['zakatAmount'], 2); ?><br>
                    Your wealth exceeds the Nisab threshold (<?= $results['currency']; ?> <?= number_format($results['nisabValue'], 2); ?>).
                    <span class="note">Note: The Nisab is the minimum wealth a Muslim must have to be eligible for Zakat.</span>
                <?php else: ?>
                    Your wealth is below the Nisab threshold (<?= $results['currency']; ?> <?= number_format($results['nisabValue'], 2); ?>).<br>
                    <span class="note">Note: The Nisab is the minimum wealth a Muslim must have to be eligible for Zakat.</span>
                <?php endif; ?>
            </p>

            <div class="donate-section">
                <?php if ($results['isEligible']): ?>
                    <p><strong>Ready to fulfill your Zakat obligation?</strong></p>
                    <p>Your calculated Zakat amount is <?= $results['currency']; ?> <?= number_format($results['zakatAmount'], 2); ?>.</p>
                <?php else: ?>
                    <p><strong>Would you like to make a donation?</strong></p>
                    <p>You can still donate <?= $results['currency']; ?> <?= number_format($results['donationAmount'], 2); ?> as charity.</p>
                <?php endif; ?>
                <form action="donate.php" method="GET">
                    <input type="hidden" name="amount" value="<?= $results['isEligible'] ? $results['zakatAmount'] : $results['donationAmount']; ?>">
                    <input type="hidden" name="currency" value="<?= $results['currency']; ?>">
                    <input type="hidden" name="type" value="<?= $results['isEligible'] ? 'zakat' : 'charity'; ?>">
                    <button type="submit">Donate Now</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
