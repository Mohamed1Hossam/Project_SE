<?php
require_once '../Modules/User.php';
require_once '../Modules/Payment.php';
require_once '../Modules/VolunteerEvent.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/login.php");
    exit();
}

class ProfileController {
    private $user;
    private $payment;
    private $volunteerEvent;

    public function __construct() {
        $this->user = new User();
        $this->payment = new Payment();
        $this->volunteerEvent = new VolunteerEvent();
    }

    public function displayProfile($userId) {
        $profile = null;
        $paymentHistory = [];
        $eventParticipation = [];

        try {
            $profile = $this->user->getProfile($userId);

            if (!$profile) {
                throw new Exception("User profile not found");
            }

            if (!empty($profile['is_donor'])) {
                $paymentHistory = $this->payment->getPaymentHistory($userId);
            }
            if (!empty($profile['is_volunteer'])) {
                $eventParticipation = $this->volunteerEvent->getEventParticipation($userId);
            }

        } catch (Exception $e) {
            echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
            $this->handlePasswordChange(
                $userId,
                $_POST['current_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
        }

        $this->renderProfile($profile, $paymentHistory, $eventParticipation);
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
            echo $result === true ? "<p class='success'>Password changed successfully!</p>" : "<p class='error'>$result</p>";
        } catch (Exception $e) {
            echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    private function renderProfile($profile, $paymentHistory, $eventParticipation) {
        // Scope-safe include
        include '../Views/profileView.php';
    }
}

// Create controller and display profile
$controller = new ProfileController();
$controller->displayProfile($_SESSION['user_id']);
?>
