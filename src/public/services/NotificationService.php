<?php

require_once __DIR__ . '/../models/NotificationModels.php';

class NotificationService {

    private NotificationModels $model;

    public function __construct() {
        $this->model = new NotificationModels();
    }

    public function envoyerNotification(int $userId, string $message): bool {
        return $this->model->insertNotification($userId, $message);
    }

    public function getNotificationsNonLues(int $userId): array {
        return $this->model->getNotifsParUtilisateur($userId);
    }

    public function countNonLues(int $userId): int {
        return $this->model->countNotifsNonLues($userId);
    }

    public function marquerCommeLue(int $id): bool {
        return $this->model->marquerLue($id);
    }

    public function marquerToutesLues(int $userId): bool {
        return $this->model->marquerToutesLues($userId);
    }
}