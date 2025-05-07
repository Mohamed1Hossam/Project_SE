<?php

session_start();

// Check if user is logged in 
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit(); 
}

require_once 'User.php';
require_once 'Payment.php';


class ProfileController {
    private $user;
    private $payment;

    public function __construct() {
        $this->user = new User();
        $this->payment = new Payment();
    }

    /**
     * Main profile display method
     * @param int $userId - Authenticated user's ID from session
     */
    public function displayProfile($userId) {
        try {
            $profile = $this->user->getProfile($userId);
            
            if (!$profile) {
                throw new Exception("User profile not found");
            }

            $paymentHistory = $this->payment->getPaymentHistory($userId);
            
        } catch (Exception $e) {
            // Handle errors 
            echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $paymentHistory = []; 
        }

        // Handle password change 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
            $this->handlePasswordChange(
                $userId,
                $_POST['current_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
        }

        $this->renderProfile($profile, $paymentHistory);
    }

    
    private function handlePasswordChange($userId, $currentPassword, $newPassword, $confirmPassword) {

        if ($newPassword !== $confirmPassword) {
            echo "<p class='error'>New passwords don't match</p>";
            return;
        }

        if (strlen($newPassword) < 8) {
            echo "<p class='error'>Password must be at least 8 characters</p>";
            return;
        }

        try {
            $result = $this->user->changePassword($userId, $currentPassword, $newPassword);
            
            if ($result === true) {
                echo "<p class='success'>Password changed successfully!</p>";
            } else {
                echo "<p class='error'>$result</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

   
    private function renderProfile($profile, $paymentHistory) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>My Profile</title>
            <link rel="stylesheet" href="profile.css">
        </head>
        <body>
            <div class="profile-section">
                <h2>Profile Details</h2>
                <div class="container">
                    <!-- Profile Information Card -->
                    <div class="card">
                        <div class="profile-image-container">
                            <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg" 
                                 alt="Profile Picture" class="profile-image" />
                            <p><strong>Name:</strong> <?= htmlspecialchars($profile['first_name'] ?? '') ?> <?= htmlspecialchars($profile['last_name'] ?? '') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($profile['email'] ?? '') ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($profile['phone_number'] ?? 'N/A') ?></p>
                            <p><strong>Role:</strong> <?= 
                                implode(', ', array_filter([
                                    $profile['is_donor'] ? 'Donor' : null,
                                    $profile['is_volunteer'] ? 'Volunteer' : null
                                ])) ?: 'None'
                            ?></p>
                        </div>
                    </div>

                    <!-- Password Change Card -->
                    <div class="card password-section">
                        <h2>Change Password</h2>
                        <form method="POST">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" required>
                            
                            <label for="new_password">New Password (min 8 chars):</label>
                            <input type="password" id="new_password" name="new_password" required>
                            
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            
                            <button type="submit" name="change_password">Change Password</button>
                        </form>
                    </div>

                    <!-- Payment History Card -->
                    <div class="card payment-section">
                        <h2>Payment History</h2>
                        <?php if (empty($paymentHistory)): ?>
                            <p>No payment history found.</p>
                        <?php else: ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paymentHistory as $payment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($payment['campaign_name'] ?? 'N/A') ?></td>
                                            <td>$<?= number_format($payment['amount'] ?? 0, 2) ?></td>
                                            <td><?= htmlspecialchars($payment['payment_method'] ?? 'N/A') ?></td>
                                            <td><?= date('M j, Y', strtotime($payment['payment_date'] ?? '')) ?></td>
                                            <td><?= htmlspecialchars($payment['status'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// Create controller instance and display profile for logged-in user
$controller = new ProfileController();
$controller->displayProfile($_SESSION['user_id']);
?>