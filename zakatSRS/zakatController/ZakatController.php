<?php
require_once 'ZakatModel.php';

class ZakatController {
    private $model;

    public function __construct() {
        $this->model = new ZakatModel();
    }

    public function handleRequest() {
        $results = null;
        $formSubmitted = false;
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
            $formSubmitted = true;
            $cashAmount = $_POST['cashAmount'] ?? '';
            $currency = $_POST['currency'] ?? 'USD';

            $result = $this->model->calculateZakat($cashAmount, $currency);
            $errors = $result['errors'] ?? [];
            if (empty($errors)) {
                $results = $result;
            }
        }

        $viewData = [
            'formSubmitted' => $formSubmitted,
            'results' => $results,
            'errors' => $errors
        ];

        extract($viewData);
        require_once 'zakatView.php';
    }
}

$controller = new ZakatController();
$controller->handleRequest();
?>