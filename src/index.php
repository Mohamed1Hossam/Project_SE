<?php
// Required files
require_once 'Controllers/HomeController.php';
require_once 'Controllers/FinancialAidController.php';

// Get the requested page from the URL
$page = $_GET['page'] ?? 'home';

// Route to the appropriate controller
switch ($page) {
    case 'financial-aid':
        $controller = new FinancialAidController();
        $controller->index();
        break;
    
    case 'home':
    default:
        $controller = new HomeController();
        $controller->index();
        break;
}
