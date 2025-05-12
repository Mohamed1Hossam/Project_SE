<?php
class FinancialAidController {
    private $model;    public function __construct() {
        require_once __DIR__ . '/../Modules/FinancialAidModel.php';
        $this->model = new FinancialAidModel();
    }

    public function index() {
        // Display the financial aid request form
        $data = [
            'formSubmitted' => false,
            'errorMessage' => ''
        ];
        $this->loadView($data);
    }    public function submitRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestData = [
                'fullName' => $_POST['fullName'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'amount' => $_POST['amount'] ?? '',
                'reason' => $_POST['reason'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            // Validate the data
            if ($this->validateRequestData($requestData)) {
                $result = $this->model->submitFinancialAidRequest($requestData);
                
                if ($result === true) {
                    $_SESSION['form_result'] = [
                        'formSubmitted' => true,
                        'errorMessage' => ''
                    ];
                } else {
                    $_SESSION['form_result'] = [
                        'formSubmitted' => false,
                        'errorMessage' => $result // Error message from the model
                    ];
                }
            } else {
                $_SESSION['form_result'] = [
                    'formSubmitted' => false,
                    'errorMessage' => 'Please fill in all required fields correctly.'
                ];
            }
            
            return $_SESSION['form_result'];
        }
        
        return [
            'formSubmitted' => false,
            'errorMessage' => ''
        ];
    }

    private function validateRequestData($data) {
        // Validate required fields
        if (empty($data['fullName']) || empty($data['email']) || 
            empty($data['phone']) || empty($data['amount']) || 
            empty($data['reason'])) {
            return false;
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validate amount (must be positive number)
        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return false;
        }

        return true;
    }    private function loadView($data) {
        require_once __DIR__ . '/../Views/financial_aid_view.php';
    }
}