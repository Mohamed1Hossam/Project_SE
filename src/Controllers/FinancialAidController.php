<?php
require_once '../Modules/FinancialAidModel.php';

class FinancialAidController {
    private $model;
    
    public function __construct() {
        $this->model = new FinancialAidModel();
    }
    
    public function index() {
        $formSubmitted = false;
        $errorMessage = '';
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'fullName' => $_POST['fullName'] ?? 'Not provided',
                'email' => $_POST['email'] ?? 'Not provided',
                'phone' => $_POST['phone'] ?? 'Not provided',
                'amount' => $_POST['amount'] ?? 'Not provided'
            ];
            
            $result = $this->model->submitFinancialAidRequest($data);
            
            if ($result === true) {
                $formSubmitted = true;
            } else {
                $errorMessage = $result;
            }
        }
        
        // Include the view
        require_once 'Views/Financialaid.php';
    }
}
