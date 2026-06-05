<?php

require_once __DIR__ . '/Model.php';

class NotificationModels extends Model {

    public function insertNotification(int $userId, string $message): bool {
        $sql = "INSERT INTO notification (id_utilisateur, message, lu, date_creation) 
                VALUES (?, ?, 0, NOW())";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$userId, $message]);
    }

    public function getNotifsParUtilisateur(int $userId): array {
        $sql = "SELECT * FROM notification 
                WHERE id_utilisateur = ? 
                ORDER BY date_creation DESC 
                LIMIT 20";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countNotifsNonLues(int $userId): int {
        $sql = "SELECT COUNT(*) FROM notification 
                WHERE id_utilisateur = ? AND lu = 0";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function marquerLue(int $id): bool {
        $sql = "UPDATE notification SET lu = 1 WHERE id_notification = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function marquerToutesLues(int $userId): bool {
        $sql = "UPDATE notification SET lu = 1 WHERE id_utilisateur = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$userId]);
    }
}