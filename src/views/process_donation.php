<<<<<<< HEAD
<!-- HTML PART (for GET requests) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Page</title>
    <link rel="stylesheet" href="../../Style.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <link rel="stylesheet" href="../../charity_project/bootstrap.min.css">
    <script src="../../Script.js" defer></script>
=======
<!-- donation_form_view.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Donation Page</title>
    <link rel="stylesheet" href="../../Style.css" />
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <link rel="stylesheet" href="../../charity_project/bootstrap.min.css" />
>>>>>>> 080cf1b (.)
</head>
<body class="home-page">
<header>
    <div class="container header-content">
        <div class="logo">
            <div class="logo-circle">
                <div class="icon-heart"></div>
            </div>
            <div class="logo-text">
                <h1>Children of the <span>Land</span></h1>
                <p>Empowering communities together</p>
            </div>
            <div class="search-container">
<<<<<<< HEAD
                <input type="text" id="searchInput" placeholder="Search...">
                <button onclick="handleSearch()">🔍</button>
              </div>
=======
                <input type="text" id="searchInput" placeholder="Search..." />
                <button onclick="handleSearch()">🔍</button>
            </div>
>>>>>>> 080cf1b (.)
        </div>
        <nav class="nav-links">
            <a href="get_campaign_data.php"><div class="icon-globe"></div><span>Campaigns</span></a>
            <a href="zakat-calculator.php"><div class="icon-calculator"></div><span>Calculate Zakat</span></a>
            <a href="financial_aid.php"><div class="icon-landmark"></div><span>Financial Aid</span></a>
            <a href="Profile/profile.php"><div class="icon-user"></div><span>Profile</span></a>
        </nav>
    </div>
</header>
<<<<<<< HEAD
=======

>>>>>>> 080cf1b (.)
<main>
    <div class="donation-page-container">
        <h1 class="donation-title">Donation Page</h1>
        <div id="donationFormContainer" class="donation-form-container">
            <form id="donationForm">
                <input type="hidden" id="campaignId" name="campaignId" value="<?php echo isset($_GET['campaign_id']) ? htmlspecialchars($_GET['campaign_id']) : ''; ?>">
<<<<<<< HEAD
                <div class="form-group">
                    <label for="donorName">Name:</label>
                    <input type="text" id="donorName" name="donorName" required>
                </div>
                <div class="form-group">
                    <label for="donorEmail">Email:</label>
                    <input type="email" id="donorEmail" name="donorEmail" required>
                </div>
                <div class="form-group">
                    <label for="donationAmount">Amount to Donate(EGP):</label>
                    <input type="number" id="donationAmount" name="donationAmount" min="1" required>
=======
                
                <div class="form-group">
                    <label for="donorName">Name:</label>
                    <input type="text" id="donorName" name="donorName" required />
                </div>
                <div class="form-group">
                    <label for="donorEmail">Email:</label>
                    <input type="email" id="donorEmail" name="donorEmail" required />
                </div>
                <div class="form-group">
                    <label for="donationAmount">Amount to Donate (EGP):</label>
                    <input type="number" id="donationAmount" name="donationAmount" min="1" required />
>>>>>>> 080cf1b (.)
                </div>

                <div class="form-group">
                    <label>Donation Type:</label>
                    <div class="donation-type-options">
<<<<<<< HEAD
                        <label><input type="radio" name="donationType" value="onetime"> One-Time</label>
                        <label><input type="radio" name="donationType" value="recurring"> Recurring</label>
=======
                        <label><input type="radio" name="donationType" value="onetime" /> One-Time</label>
                        <label><input type="radio" name="donationType" value="recurring" /> Recurring</label>
>>>>>>> 080cf1b (.)
                    </div>
                </div>

                <div id="recurringOptions" class="form-group hidden">
                    <label>Recurring Frequency:</label>
                    <div class="recurring-options">
<<<<<<< HEAD
                        <label><input type="radio" name="recurringFrequency" value="weekly"> Weekly</label>
                        <label><input type="radio" name="recurringFrequency" value="monthly"> Monthly</label>
=======
                        <label><input type="radio" name="recurringFrequency" value="weekly" /> Weekly</label>
                        <label><input type="radio" name="recurringFrequency" value="monthly" /> Monthly</label>
>>>>>>> 080cf1b (.)
                    </div>
                </div>

                <div class="form-group">
                    <label>Payment Method:</label>
                    <div class="payment-options">
<<<<<<< HEAD
                        <label><input type="radio" name="paymentMethod" value="credit_card"> Credit Card</label>
                        <label><input type="radio" name="paymentMethod" value="paypal"> PayPal</label>
                    </div>
                </div>
                <div id="creditCardFields" class="hidden">
                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <input type="text" id="cardNumber" name="cardNumber" pattern="^[0-9]{16}$" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label for="cardExpiry">Expiry Date (MM/YY):</label>
                        <input type="text" id="cardExpiry" name="cardExpiry" pattern="^(0[1-9]|1[0-2])\/[0-9]{2}$" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cardCvv">CVV:</label>
                        <input type="password" id="cardCvv" name="cardCvv" pattern="^[0-9]{3}$" maxlength="3">
                    </div>
                </div>
                <div id="paypalFields" class="hidden">
                    <div class="form-group">
                        <label for="paypalEmail">PayPal Email:</label>
                        <input type="email" id="paypalEmail" name="paypalEmail">
                    </div>
                    <div class="form-group">
                        <label for="paypalPassword">PayPal Password:</label>
                        <input type="password" id="paypalPassword" name="paypalPassword">
                    </div>
                </div>
=======
                        <label><input type="radio" name="paymentMethod" value="credit_card" /> Credit Card</label>
                        <label><input type="radio" name="paymentMethod" value="paypal" /> PayPal</label>
                    </div>
                </div>

                <div id="creditCardFields" class="hidden">
                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <input type="text" id="cardNumber" name="cardNumber" pattern="^[0-9]{16}$" maxlength="16" />
                    </div>
                    <div class="form-group">
                        <label for="cardExpiry">Expiry Date (MM/YY):</label>
                        <input type="text" id="cardExpiry" name="cardExpiry" pattern="^(0[1-9]|1[0-2])\/[0-9]{2}$" maxlength="5" />
                    </div>
                    <div class="form-group">
                        <label for="cardCvv">CVV:</label>
                        <input type="password" id="cardCvv" name="cardCvv" pattern="^[0-9]{3}$" maxlength="3" />
                    </div>
                </div>

                <div id="paypalFields" class="hidden">
                    <div class="form-group">
                        <label for="paypalEmail">PayPal Email:</label>
                        <input type="email" id="paypalEmail" name="paypalEmail" />
                    </div>
                    <div class="form-group">
                        <label for="paypalPassword">PayPal Password:</label>
                        <input type="password" id="paypalPassword" name="paypalPassword" />
                    </div>
                </div>

>>>>>>> 080cf1b (.)
                <button class="btnd" type="submit">Donate Now</button>
            </form>
        </div>
    </div>
<<<<<<< HEAD
</main>
</body>
</html>
=======
    <script>
        document.addEventListener('DOMContentLoaded', () => {
  const donationForm = document.getElementById('donationForm');
  const donationTypeRadios = document.querySelectorAll('input[name="donationType"]');
  const recurringOptions = document.getElementById('recurringOptions');

  const paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
  const creditCardFields = document.getElementById('creditCardFields');
  const paypalFields = document.getElementById('paypalFields');

  // Toggle recurring frequency
  donationTypeRadios.forEach(radio => {
    radio.addEventListener('change', () => {
      recurringOptions.classList.toggle('hidden', radio.value !== 'recurring');
    });
  });

  // Toggle payment fields
  paymentRadios.forEach(radio => {
    radio.addEventListener('change', () => {
      creditCardFields.classList.toggle('hidden', radio.value !== 'credit_card');
      paypalFields.classList.toggle('hidden', radio.value !== 'paypal');
    });
  });

  // Form submit
  donationForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(donationForm);
    const data = {
      donorName: formData.get('donorName'),
      donorEmail: formData.get('donorEmail'),
      donationAmount: formData.get('donationAmount'),
      campaignId: formData.get('campaignId'),
      donationType: formData.get('donationType'),
      paymentMethod: formData.get('paymentMethod')
    };

    // Only include recurring frequency if donation is recurring
    if (data.donationType === 'recurring') {
      data.recurringFrequency = formData.get('recurringFrequency');
    }

    // Handle payment method details
    if (data.paymentMethod === 'credit_card') {
      data.cardNumber = formData.get('cardNumber');
      data.cardExpiry = formData.get('cardExpiry');
      data.cardCvv = formData.get('cardCvv');
    } else if (data.paymentMethod === 'paypal') {
      data.paypalEmail = formData.get('paypalEmail');
      data.paypalPassword = formData.get('paypalPassword');
    }

    try {
      const response = await fetch('../controllers/donation.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });

      const result = await response.json();

      alert(result.message); // or update a message box on the page
    } catch (err) {
      console.error(err);
      alert('An error occurred while processing your donation.');
    }
  });
});
    </script>
</main>
</body>
</html>
>>>>>>> 080cf1b (.)
