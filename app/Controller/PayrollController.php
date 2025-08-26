<?php

class PayrollController {
    public function show_payroll_form() {
        require_once BASE_PATH . '/app/Views/payroll/form.php';
    }

    public function store_payroll() {
        $errors = [];

        // Validate name
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        if (empty($name)) {
            $errors[] = 'O nome é obrigatório.';
        }

        // Validate email
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'O email é obrigatório e deve ser válido.';
        }

        // Validate position
        $position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);
        if (empty($position)) {
            $errors[] = 'O cargo é obrigatório.';
        }

        // Validate salary
        $salary = filter_input(INPUT_POST, 'salary', FILTER_VALIDATE_FLOAT);
        if ($salary === false || $salary <= 0) {
            $errors[] = 'O salário deve ser um número positivo válido.';
        }

        if (!empty($errors)) {
            // For simplicity, redirect to error page. In a real app, you'd pass errors back to the form.
            header('Location: /Rhease/public/error'); // Changed to route
            exit;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'position' => $position,
            'salary' => $salary
        ];

        $payrollModel = new PayrollModel();
        $success = $payrollModel->savePayroll($data);

        if ($success) {
            header('Location: /Rhease/public/payrolls'); // Redirect to payrolls list
        } else {
            header('Location: /Rhease/public/error'); // Changed to route
        }
        exit;
    }

    public function show_payrolls() {
        $orderBy = filter_input(INPUT_GET, 'order_by', FILTER_SANITIZE_STRING);
        $orderDirection = filter_input(INPUT_GET, 'order_direction', FILTER_SANITIZE_STRING);
        $positionFilter = filter_input(INPUT_GET, 'position', FILTER_SANITIZE_STRING);
        $nameSearch = filter_input(INPUT_GET, 'search_name', FILTER_SANITIZE_STRING);

        $payrollModel = new PayrollModel();
        $payrolls = $payrollModel->getPayrolls($orderBy, $orderDirection, $positionFilter, $nameSearch);
        
        // Pass current filter/search values to the view for persistence in the form
        $currentOrderBy = $orderBy;
        $currentOrderDirection = $orderDirection;
        $currentPositionFilter = $positionFilter;
        $currentNameSearch = $nameSearch;

        require_once BASE_PATH . '/app/Views/payroll/list.php';
    }

    public function remove_payroll()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (empty($id)) {
            header('Location: /Rhease/public/error');
            exit;
        }

        $payrollModel = new PayrollModel();
        $success = $payrollModel->deletePayroll($id);

        if ($success) {
            header('Location: /Rhease/public/payrolls');
        } else {
            header('Location: /Rhease/public/error');
        }
        exit;
    }
}
