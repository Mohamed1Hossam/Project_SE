<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <title>Financial Aid Request - Children of the Land</title>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="src/index.php" class="logo">
                    <div class="logo-icon">
                        <div class="icon-heart"></div>
                    </div>
                    <div class="logo-text">
                        <h1>Children of the <span>Land</span></h1>
                        <p>Empowering communities together</p>
                    </div>
                </a>

                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <button onclick="handleSearch()">🔍</button>
                </div>

                <div class="nav-links">
                    <a href="../Views/get_campaign_data.php">
                    <div class="icon-globe"></div>
                    <span>Campaigns</span>
                </a>
                
                <a href="../Views/get_event_data.php">
                        <div class="icon-ticket-check"></div>
                        <span>Events</span>
                </a>

                <a href="../EventCountDown.html">
                    <div class="icon-clock"></div>
                    <span>Countdown</span>

                <a href="../Views/process_donation.php">
                    <div class="icon-globe"></div>
                    <span>Donate Now</span>
                </a>
                <a href="../Views/zakatView.php">
                    <div class="icon-calculator"></div>
                    <span>Calculate Zakat</span>
                </a>
                <a href="../Views/Financialaid.php">
                    <div class="icon-landmark"></div>
                    <span>Financial Aid</span>
                </a>
                <a href="../Views/VolunteerPage.html">
                    <div class="icon-user"></div>
                    <span>Join US</span>
            </a>
                <a href="../Views/profileView.php">
                    <div class="icon-user"></div>
                    <span>Profile</span>
                </a>
        </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <?php if ($formSubmitted): ?>
                <div class="form-container form-submitted" id="success-message">
                    <h3>Request Submitted Successfully!</h3>
                    <p>Thank you for submitting your financial aid request. Our team will review your information and contact you within 2-3 business days.</p>
                    <p>A confirmation email has been sent to your provided email address.</p>
                    <a href="HomePage.php" class="return-link">Return to Home</a>
                </div>
            <?php else: ?>
                <div class="form-container" id="form-container">
                    <h2>Financial Aid Request</h2>
                    <p>Please fill out this form with accurate information to help us assess your situation.</p>

                    <?php if ($errorMessage): ?>
                        <div class="error-alert">
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                    <?php endif; ?>

                    <form id="financial-aid-form" method="post" action="">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your full name" required>
                            <div class="error-message" id="fullName-error">Please enter your full name</div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required>
                            <div class="error-message" id="email-error">Please enter a valid email address</div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your phone number" required>
                            <div class="error-message" id="phone-error">Please enter a valid phone number</div>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount Needed ($)</label>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount needed" min="1" required>
                            <div class="error-message" id="amount-error">Please enter a valid amount</div>
                        </div>

                        <button type="submit" class="submit-btn">Submit Request</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../Script.js"></script>
</body>
</html>
