<?php
require_once 'config.php';
require_once 'classes/Volunteer.php';
require_once 'classes/User.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $skills     = $_POST['skills'];

    // Insert into User table
    $user = new User($conn);
    $user_id = $user->createVolunteerUser($email, $first_name, $last_name, $phone);

    if ($user_id) {
        // Insert into Volunteer table
        $volunteer = new Volunteer($conn);
        $success = $volunteer->addVolunteer($user_id, $skills);

        if ($success) {
            $message = "Volunteer registered successfully!";
        } else {
            $message = "Failed to register volunteer.";
        }
    } else {
        $message = "Failed to create user.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volunteer Registration</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #fff;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 10px;
}

.search-container input[type="text"] {
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 20px 0 0 20px;
  outline: none;
  width: 200px;
  transition: width 0.4s ease-in-out;
}

.search-container button {
  padding: 8px 12px;
  border: 1px solid #ccc;
  background-color: #1abc9c;
  color: white;
  border-radius: 0 20px 20px 0;
  cursor: pointer;
  border-left: none;
}

.search-container input[type="text"]:focus {
  width: 250px;
}

        .volunteer-form-container {
            margin-left: auto;
            margin-right: auto;
            min-height: 100vh;
            padding-top: 150px; =
        }

        .volunteer-form {
            background-color: #f7f7f7;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
        }

        .volunteer-form input, .volunteer-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .volunteer-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .volunteer-form .button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .volunteer-form button {
            background-color: #0d9488;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

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

        <div class="nav-links">
          <a href="get_campaign_details.php">
          <div class="icon-globe"></div>
            Campaigns
          </a>
          
          <a href="get_event_details.php">
          <div class="icon-ticket-check"></div>
            Events
          </a>
          
          <a href="process_donation.php">
          <div class="icon-globe"></div>
            Donate Now
          </a>

          <a href="zakat-calculator.php">
          <div class="icon-calculator"></div>
            Calculate Zakat
          </a>
          <a href="financial_aid.php">
          <div class="icon-landmark"></div>
            Financial Aid
          </a>
          <a href="profile.php">
          <div class="icon-user"></div>
            Profile
          </a>
        </div>
      </nav>
    </div>
</header>

<div class="volunteer-form-container">
    <form method="POST" class="volunteer-form">
        <h2>Volunteer Registration</h2>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <textarea name="skills" placeholder="Your Skills" rows="4" required></textarea>
        <div class="button-container">
            <button type="submit">Submit</button>
        </div>
        <?
