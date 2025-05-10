<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../profile.css">
</head>
<body>
    <div class="profile-section">
        <h2>Profile Details</h2>
        <div class="container">
            <div class="card">
                <div class="profile-image-container">
                    <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg" 
                         alt="Profile Picture" class="profile-image" />
                    <?php if (!empty($profile)): ?>
                        <p><strong>Name:</strong> <?= htmlspecialchars($profile['first_name'] ?? '') ?> <?= htmlspecialchars($profile['last_name'] ?? '') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email'] ?? '') ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($profile['phone_number'] ?? 'N/A') ?></p>
                        <p><strong>Role:</strong> <?=
                            implode(', ', array_filter([
                                !empty($profile['is_donor']) ? 'Donor' : null,
                                !empty($profile['is_volunteer']) ? 'Volunteer' : null
                            ])) ?: 'None'
                        ?></p>
                    <?php else: ?>
                        <p><strong>No profile data available.</strong></p>
                    <?php endif; ?>
                </div>
            </div>

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

            <?php
                $showPayments = !empty($profile['is_donor']) && is_array($paymentHistory);
                $showEvents = !empty($profile['is_volunteer']) && is_array($eventParticipation);
            ?>

            <div class="card payment-section">
                <h2>
                    <?= $showPayments ? 'Payment History' : ($showEvents ? 'Event Participation' : 'No Data') ?>
                </h2>

                <?php if ($showPayments && empty($paymentHistory)): ?>
                    <p>No payment history found.</p>
                <?php elseif ($showEvents && empty($eventParticipation)): ?>
                    <p>No event participation found.</p>
                <?php elseif ($showPayments): ?>
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
                <?php elseif ($showEvents): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Role</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eventParticipation as $event): ?>
                                <tr>
                                    <td><?= htmlspecialchars($event['event_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($event['role'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($event['location'] ?? 'N/A') ?></td>
                                    <td><?= date('M j, Y', strtotime($event['event_date'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($event['status'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No data available to display.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
