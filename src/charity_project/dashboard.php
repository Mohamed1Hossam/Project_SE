<?php
require 'db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if($_SESSION['role'] != 'admin') {
  header('location: ../Views/login.php');
  exit;
}

// Fetch total volunteers
$volunteerCount = $pdo->query("SELECT COUNT(*) FROM volunteer")->fetchColumn();

// Fetch total campaigns
$campaignCount = $pdo->query("SELECT COUNT(*) FROM campaign")->fetchColumn();

// Fetch total events
$eventCount = $pdo->query("SELECT COUNT(*) FROM event")->fetchColumn();

// Fetch total donations from both onetime and recurring
$oneTime = $pdo->query("SELECT SUM(amount) FROM onetime_donation")->fetchColumn();
$recurring = $pdo->query("SELECT SUM(amount) FROM recurring_donation")->fetchColumn();

$totalDonations = ($oneTime ?: 0) + ($recurring ?: 0);
?>

<!DOCTYPE html>
<html>
<head>
<style>
  .custom-hover {
    transition: box-shadow 0.3s ease;
  }

  .custom-hover:hover {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.36); /* Bottom-only shadow */
  }
  .card{
    border-color: rgba(0, 0, 0, 0) 
    /* width: 700px !important; */
  }
  .test .cont{
    background-color: #007bff8b;
}
.underline1{
    height: 4px;
    background-color:rgba(0, 49, 101, 0.55);
    width: 70px;
    border-radius: 5px;
}
.underline2{
    height: 4px;
    background-color:rgba(0, 49, 101, 0.55);
    width:110px;
    margin:5px;
    border-radius: 5px;
}
.underline3{
    height: 4px;
    background-color:rgba(0, 49, 101, 0.55);
    width: 70px;
    margin-bottom:10px;
    border-radius: 5px;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer">
 <link rel="stylesheet" href="style.css"> 
  <link rel="stylesheet" href="bootstrap.min.css">

  <title>Charity Dashboard</title>
 
</head>
<body>
    <!-- <div class="dashboard">
       
        <div class="card">Total Volunteers: <strong><?= $volunteerCount ?></strong></div>
        <div class="card">Total Campaigns: <strong><?= $campaignCount ?></strong></div>
        <div class="card">Total Events: <strong><?= $eventCount ?></strong></div>
        <div class="card">Total Donations: <strong>$<?= number_format($totalDonations, 2) ?></strong></div>
    </div> -->
<div class="d-flex justify-content-center align-items-center flex-column">
 <h2 class="text-center">Mersal Dashboard</h2>
 <div class="underline1"></div>
 <div class="underline2"></div>
 <div class="underline3"></div>
</div>
    <div class="test ">
<div class="cont flex-wrap d-flex justify-content-between align-items-center ">
  <div class="item w-20 m-5 text-center ">
    <div class=" text-white temp  d-flex justify-content-center align-items-center"><i class="fa-solid fa-3x fa-list-check border border-white border-4 rounded-circle p-3"></i></div>
<h1><strong><?= $volunteerCount ?></strong></h1>
<p class="text-uppercase">Volunteer(s)</p>

  </div>
  
    <div class="item w-20 m-5 text-center">
    <div class="temp text-white  d-flex justify-content-center align-items-center"><i class="fa-solid fa-3x fa-list-check border border-white border-4 rounded-circle p-3"></i></div>
<h1><strong><?= $campaignCount ?></strong></h1>
<p class="text-uppercase">Campaign(s)</p>

  </div>
  <div class="item w-20 m-5 text-center">
    <div class="ico text-white d-flex justify-content-center align-items-center"><i class="fa-solid fa-3x fa-list-check border border-white border-4 rounded-circle p-3"></i></div>
<h1><strong><?= $eventCount ?></strong></h1>
<p class="text-uppercase">Event(s)</p>

  </div>
  <div class="item w-20 m-5 text-center">
    <div class="ico text-white d-flex justify-content-center align-items-center"><i class="fa-solid fa-3x fa-list-check border border-white border-4 rounded-circle p-3"></i></div>
<h1><strong>$<?= number_format($totalDonations, 2) ?></strong></h1>
<p class="text-uppercase">Total Donations</p>

  </div>

</div>
</div>
<div class="options d-flex justify-content-start align-content-center flex-wrap ">
<a href="add_campaign.php" class="text-black text-decoration-none">
<div class="card custom-hover border  m-5 h-75 " style="width: 400px;">
  <div class="card-body text-center  w-20 ">
    <h1 class="card-title">Add Campaign</h1>
  </div>

</div></a>
<a href="manage_campaigns.php" class="text-black text-decoration-none">
<div class="card custom-hover border  m-5 h-75 "style="width: 400px;">
  <div class="card-body text-center">
    <h1 class="card-title">Delete Campaign</h1>
  </div>

</div></a>
<a href="add_event.php" class="text-black text-decoration-none">

<div class="card custom-hover border  m-5 h-75 "style="width: 400px;">
  <div class="card-body text-center">
    <h1 class="card-title">Add Event</h1>
  </div>
</div></a>
<a href="manage_events.php" class="text-black text-decoration-none">

<div class="card custom-hover border  m-5 h-75 "style="width: 400px;">
  <div class="card-body text-center">
    <h1 class="card-title">Delete Event</h1>
  </div>
</div></a>
<a href="form.html" class="text-black text-decoration-none">

<div class="card custom-hover border  m-5 h-75 "style="width: 400px;">
  <div class="card-body text-center">
    <h1 class="card-title">Send Emails</h1>
  </div>
</div></a>
<a id="downloadReport"  href="" class="text-black text-decoration-none">

<div class="card custom-hover border  m-5 h-75 "style="width: 400px;">
  <div class="card-body text-center">
  <h1 class="card-title">Download Report</h1>
  </div>
</div></a>



</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
  document.getElementById('downloadReport').addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    let y = 20;
    doc.setFontSize(16);
    doc.text("Mersal Dashboard Report", 20, y);
    y += 10;

    doc.setFontSize(12);
    doc.text("Volunteer(s): <?= $volunteerCount ?>", 20, y += 10);
    doc.text("Campaign(s): <?= $campaignCount ?>", 20, y += 10);
    doc.text("Event(s): <?= $eventCount ?>", 20, y += 10);
    doc.text("Total Donations: $<?= number_format($totalDonations, 2) ?>", 20, y += 10);

    doc.save("dashboard_report.pdf");
  });
</script>


</body>

</html>
