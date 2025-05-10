<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <title>Children of the Land</title>
</head>
<body class="home-page">
    <!-- Header/Navigation -->
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
            </div>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search...">
                <button onclick="handleSearch()">🔍</button>
            </div>
              
            <nav class="nav-links">
                <a href="/MVCMahmoud/get_campaign_data.php">
                    <div class="icon-globe"></div>
                    <span>Campaigns</span>
                </a>
                
                <a href="/MVCMahmoud/get_event_data.php">
                    <div class="icon-ticket-check"></div>
                    <span>Events</span>
                </a>

                <a href="/MVCMahmoud/process_donation.php">
                    <div class="icon-globe"></div>
                    <span>Donate Now</span>
                </a>
                <a href="/MVCMahmoud/zakat-calculator.php">
                    <div class="icon-calculator"></div>
                    <span>Calculate Zakat</span>
                </a>
                <a href="/MVCMahmoud/financial_aid.php">
                    <div class="icon-landmark"></div>
                    <span>Financial Aid</span>
                </a>
                
                <a href="../Views/VolunteerPage.php">
                    <div class="icon-user"></div>
                    <span>Join US</span>
                </a>

                <a href="/MVCMahmoud/Profile/profile.php">
                    <div class="icon-user"></div>
                    <span>Profile</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Interface Section -->
    <section class="Interface">
        <div class="Interface-Container">
            <div class="Interface-content">
                <h1 class="Interface-title">Make a difference<br><span>in someone's life</span></h1>
                <p class="Interface-subtitle">Join our community of donors and make a lasting impact. Every contribution counts towards creating positive change in the world.</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <?php foreach ($features as $feature): ?>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <div class="icon-<?php echo htmlspecialchars($feature['icon']); ?>"></div>
                        </div>
                        <h3 class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                        <p class="feature-desc"><?php echo htmlspecialchars($feature['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="impact">
        <div class="container">
            <h2 class="impact-title">Our Impact</h2>
            <p class="impact-subtitle">Together we've made a difference</p>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Donations</div>
                    <div class="stat-value">$<?php echo htmlspecialchars($stats['totalDonations']); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">People Helped</div>
                    <div class="stat-value"><?php echo htmlspecialchars($stats['peopleHelped']); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Active Donors</div>
                    <div class="stat-value"><?php echo htmlspecialchars($stats['activeDonors']); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container footer-content">
            <div class="copyright">© 2024 Children of the Land. All rights reserved.</div>
            <div class="footer-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#20B2AA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                </svg>
            </div>
        </div>
    </footer>
    <script src="../Script.js"></script>
</body>
</html>
