<?php

require_once __DIR__ . "/Model.php";

class PostalUnivModels {

    private $db;

    public function __construct() {
        $this->db = Model::getModel()->bd;
    }

    /* ===================== STATISTIQUES DASHBOARD ===================== */

    public function countColisRecusUnivAujourdhui() {
        $sql = $this->db->query("SELECT COUNT(*) FROM colis WHERE DATE(date_reception) = CURDATE() AND statut_id IN (1, 5)");
        return $sql->fetchColumn();
    }

    public function countColisEnAttenteTransfert() {
        $sql = $this->db->query("SELECT COUNT(*) FROM colis WHERE statut_id = 1");
        return $sql->fetchColumn();
    }

    public function countColisTransferesAujourdhui() {
        $sql = $this->db->query("SELECT COUNT(*) FROM colis WHERE DATE(date_transfert_iut) = CURDATE() AND statut_id = 6");
        return $sql->fetchColumn();
    }

    public function countTotalColisUniv() {
        $sql = $this->db->query("SELECT COUNT(*) FROM colis WHERE statut_id IN (1, 5, 6)");
        return $sql->fetchColumn();
    }

    /* ===================== COLIS RECENTS ===================== */

    public function getColisRecentsUniv() {
        $sql = $this->db->query("
            SELECT
                c.id_colis,
                c.numero_suivi,
                c.date_reception,
                c.statut_id,
                b.numero_commande,
                d.nom AS departement
            FROM colis c
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE c.statut_id IN (1, 5, 6)
            ORDER BY c.date_reception DESC
            LIMIT 10
        ");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRepartitionStatutsUniv() {
        $sql = "SELECT statut_id AS statut, COUNT(*) AS total
                FROM colis
                WHERE statut_id IN (1, 5, 6)
                GROUP BY statut_id";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColisByDepartement() {
        $sql = "SELECT d.nom AS departement, COUNT(*) AS total
                FROM colis c
                LEFT JOIN departement d ON c.destinataire_id = d.id_departement
                WHERE c.statut_id IN (1, 5, 6)
                GROUP BY c.destinataire_id
                ORDER BY total DESC
                LIMIT 5";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================== LISTE DES COLIS UNIV ===================== */

    public function getAllColisUniv() {
        $sql = "
            SELECT c.id_colis,
                   c.numero_suivi,
                   c.date_reception,
                   c.statut_id,
                   b.numero_commande,
                   d.nom AS departement
            FROM colis c
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE c.statut_id IN (1, 5, 6)
            ORDER BY c.date_reception DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColisEnAttenteTransfert() {
        $sql = "
            SELECT c.id_colis,
                   c.numero_suivi,
                   c.date_reception,
                   c.statut_id,
                   b.numero_commande,
                   d.nom AS departement
            FROM colis c
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE c.statut_id = 1
            ORDER BY c.date_reception DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColisTransferes() {
        $sql = "
            SELECT
                c.id_colis,
                c.numero_suivi,
                c.date_transfert_iut,
                c.date_reception,
                b.numero_commande,
                d.nom AS departement
            FROM colis c
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE c.statut_id = 6
            ORDER BY c.date_transfert_iut DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================== RECHERCHE COLIS ===================== */

    public function rechercherColisUniv($motcle) {
        $motcle = "%".$motcle."%";

        $sql = "
            SELECT
                c.id_colis,
                c.numero_suivi,
                c.date_reception,
                c.statut_id,
                b.numero_commande,
                d.nom AS departement
            FROM colis c
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE
                (c.numero_suivi LIKE ? OR
                b.numero_commande LIKE ? OR
                d.nom LIKE ? OR
                c.id_colis LIKE ?)
                AND c.statut_id IN (1, 5, 6)
            ORDER BY c.date_reception DESC
        ";

        $req = $this->db->prepare($sql);
        $req->execute([$motcle, $motcle, $motcle, $motcle]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================== AJOUTER UN COLIS ===================== */

    public function insertColisUniv($data) {
        $sql = "INSERT INTO colis
                (bon_commande_id, numero_suivi, destinataire_id, date_reception, statut_id, commentaire)
                VALUES (?, ?, ?, NOW(), 1, ?)";

        $req = $this->db->prepare($sql);

        return $req->execute([
            $data["bon_commande_id"],
            $data["numero_suivi"],
            $data["destinataire_id"],
            $data["commentaire"]
        ]);
    }

    public function getBCInfo($num_bc) {
        $sql = "SELECT b.id_bon_commande, u.id_utilisateur AS destinataire_id, d.id_departement
                FROM bon_commande b
                LEFT JOIN utilisateur u ON b.createur_id = u.id_utilisateur
                LEFT JOIN departement d ON b.departement_id = d.id_departement
                WHERE b.numero_commande = ?";

        $req = $this->db->prepare($sql);
        $req->execute([$num_bc]);

        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /* ===================== DETAILS COLIS ===================== */

    public function getColisById($id) {
        $sql = "
            SELECT c.*,
                b.numero_commande,
                d.nom AS departement
            FROM colis c
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE c.id_colis = ?
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /* ===================== TRANSFERT VERS IUT ===================== */

    public function transfererVersIUT($id_colis) {
        // Marquer comme "en transfert"
        $sql = "UPDATE colis
                SET statut_id = 5, date_transfert_iut = NOW()
                WHERE id_colis = ?";
        $req = $this->db->prepare($sql);
        $req->execute([$id_colis]);

        $this->addHistorique($id_colis, "Colis en transfert vers l'IUT");
    }

    public function confirmerTransfertIUT($id_colis) {
        // Marquer comme "transféré"
        $sql = "UPDATE colis
                SET statut_id = 6
                WHERE id_colis = ?";
        $req = $this->db->prepare($sql);
        $req->execute([$id_colis]);

        $this->addHistorique($id_colis, "Colis transféré à l'IUT (confirmé)");
    }

    /* ===================== HISTORIQUE ===================== */

    public function addHistorique($colis_id, $action) {
        $sql = "INSERT INTO historique_colis (colis_id, action, date_action) VALUES (?, ?, NOW())";
        $req = $this->db->prepare($sql);
        $req->execute([$colis_id, $action]);
    }

    public function getHistorique($id_colis) {
        $sql = "SELECT * FROM historique_colis
                WHERE colis_id = ?
                ORDER BY date_action DESC";
        $req = $this->db->prepare($sql);
        $req->execute([$id_colis]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHistoriqueTransferts() {
        $sql = "
            SELECT
                h.colis_id,
                h.date_action,
                h.action,
                c.numero_suivi,
                b.numero_commande,
                d.nom AS departement
            FROM historique_colis h
            LEFT JOIN colis c ON h.colis_id = c.id_colis
            LEFT JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            LEFT JOIN departement d ON c.destinataire_id = d.id_departement
            WHERE h.action LIKE '%transfert%' OR h.action LIKE '%IUT%'
            ORDER BY h.date_action DESC
            LIMIT 100
        ";

        $req = $this->db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================== UTILITAIRES ===================== */

    public function getAllStatuts() {
        return $this->db->query("SELECT * FROM statut_colis")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDepartement() {
        return $this->db->query("SELECT * FROM departement")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBonCommandes() {
        $sql = "SELECT id_bon_commande, numero_commande FROM bon_commande ORDER BY date_commande DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}