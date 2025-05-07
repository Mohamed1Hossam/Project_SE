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

        .volunteer-form-container {
            margin-left: auto;
            margin-right: auto;
            min-height: 100vh;
            padding-top: 150px; /* Push form below the header */
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
        <nav class="nav-links">
            <a href="#">
                <div class="icon-globe"></div>
                <span>Campaigns</span>
            </a>
            <a href="#">
                <div class="icon-calculator"></div>
                <span>Calculate Zakat</span>
            </a>
            <a href="#">
                <div class="icon-landmark"></div>
                <span>Financial Aid</span>
            </a>
            <a href="#">
                <div class="icon-user"></div>
                <span>Profile</span>
            </a>
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
