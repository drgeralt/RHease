<?php

class JobController {

    public function show_job() {
        // Esses dados normalmente seriam recuperados do banco de dados
        $title = 'Engenheiro de Software';
        $description = 'Estamos buscando um engenheiro de software habilidoso para nossa equipe.';
        $responsibilities = [
            'Escrever código de alta qualidade.',
            'Testar aplicativos.',
            'Participar de reuniões de equipe.'
        ];

        require_once BASE_PATH . '/app/Views/jobs/job_details_and_form.php';
    }

    public function store_application() {
        $applicationModel = new ApplicationModel();
        $success = $applicationModel->saveApplication($_POST);

        if ($success) {
            header('Location: ' . BASE_URL . '/thank_you');
        } else {
            header('Location: ' . BASE_URL . '/error');
        }
        exit;
    }

    public function show_applications() {
        $applicationModel = new ApplicationModel();
        $applications = $applicationModel->getApplications();
        require_once BASE_PATH . '/app/Views/jobs/applications.php';
    }

    public function show_bio($id){
        $applicationModel = new ApplicationModel();
        $application = $applicationModel->getApplicationById($id);
        require_once BASE_PATH . '/app/Views/jobs/bio.php';
    }

    public function show_update_form($id) {
        $applicationModel = new ApplicationModel();
        $application = $applicationModel->getApplicationById($id);

        if (!$application) {
            header('Location: ' . BASE_URL . '/error');
            exit;
        }

        require_once BASE_PATH . '/app/Views/jobs/update_application.php';
    }

    public function process_update($id) {
        $applicationModel = new ApplicationModel();
        $success = $applicationModel->updateApplicationById($id, $_POST);

        if ($success) {
            header('Location: ' . BASE_URL . '/applications');
        } else {
            header('Location: ' . BASE_URL . '/error');
        }
        exit;
    }

    public function delete_application($id) {
        $applicationModel = new ApplicationModel();
        $success = $applicationModel->deleteApplicationById($id);

        if ($success) {
            header('Location: ' . BASE_URL . '/applications');
        } else {
            header('Location: ' . BASE_URL . '/error');
        }
        exit;
    }
}