<?php
require_once '../Modules/HomeModel.php';

class HomeController {
    private $model;
    
    public function __construct() {
        $this->model = new HomeModel();
    }
    
    public function index() {
        // Get data from model
        $stats = $this->model->getStats();
        $features = $this->model->getFeatures();
        
        // Include the view
        require_once 'Views/HomePage.php';
    }
}
