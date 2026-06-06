<?php
require_once __DIR__ . "/Model.php";

class DepartementModels {

    private $db;

    public function __construct() {
        $this->db = Model::getModel()->bd;
    }

    public function getDepartementNom($departement_id): ?string {
        $req = $this->db->prepare("SELECT nom FROM departement WHERE id_departement = ?");
        $req->execute([$departement_id]);
        return $req->fetchColumn() ?: null;
    }

    public function countColisTotal($departement_id) {
        return $this->db->prepare("
            SELECT COUNT(*) FROM colis c
            JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            WHERE b.departement_id = ?
        ")->execute([$departement_id]) 
          ? $this->db->query("SELECT FOUND_ROWS()")->fetchColumn() 
          : 0;
    }

    public function countColisEnAttente($departement_id) {
        $req = $this->db->prepare("
            SELECT COUNT(*) 
            FROM colis c
            JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            WHERE b.departement_id = ? AND c.statut_id = 1
        ");
        $req->execute([$departement_id]);
        return $req->fetchColumn();
    }

    public function countColisRetires($departement_id) {
        $req = $this->db->prepare("
            SELECT COUNT(*) 
            FROM colis c
            JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            WHERE b.departement_id = ? AND c.statut_id = 3
        ");
        $req->execute([$departement_id]);
        return $req->fetchColumn();
    }

    public function getBudgetDepartement($departement_id) {
        $req = $this->db->prepare("
            SELECT budget_total, budget_utilise
            FROM departement
            WHERE id_departement = ?
        ");
        $req->execute([$departement_id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }


    public function getDerniersColis($departement_id) {
        $req = $this->db->prepare("
            SELECT 
                c.id_colis,
                c.numero_suivi,
                b.numero_commande,
                c.date_reception,
                s.libelle AS statut_libelle
            FROM colis c
            JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            JOIN statut_colis s ON c.statut_id = s.id_statut
            WHERE b.departement_id = ?
            ORDER BY c.date_reception DESC
            LIMIT 10
        ");
        $req->execute([$departement_id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFournisseurs() {
        return $this->db->query("
            SELECT id_fournisseur, nom
            FROM fournisseur
            ORDER BY nom
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

  

    /**
     * Insère un devis avec un fichier PDF optionnel
     * @param string $objet
     * @param float $montant
     * @param int $fournisseur_id
     * @param int $createur_id
     * @param string|null $pdfBlob Contenu binaire ou null
     */
    public function insertDevis($objet, $montant, $fournisseur_id, $createur_id, $pdfBlob = null) {
        $sql = "
            INSERT INTO devis
            (date_demande, objet, montant_estime, statut, fournisseur_id, createur_id, fichier_pdf)
            VALUES (CURDATE(), ?, ?, 'en_attente', ?, ?, ?)
        ";

        $req = $this->db->prepare($sql);
        
        // Liaison des paramètres
        $req->bindValue(1, $objet);
        $req->bindValue(2, $montant);
        $req->bindValue(3, $fournisseur_id);
        $req->bindValue(4, $createur_id);
        
        // Liaison du fichier binaire avec gestion du NULL automatique si $pdfBlob est null
        $req->bindParam(5, $pdfBlob, PDO::PARAM_LOB);

        return $req->execute();
    }

    public function getMesDevis($id_utilisateur) {
        $sql = "
            SELECT 
                d.id_devis,
                d.objet,
                d.montant_estime,
                d.date_demande,
                d.statut,
                f.nom AS fournisseur_nom
            FROM devis d
            JOIN fournisseur f ON d.fournisseur_id = f.id_fournisseur
            WHERE d.createur_id = ?
            ORDER BY d.id_devis DESC
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$id_utilisateur]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getMesBonsCommande($departement_id) {
        $sql = "
            SELECT 
                b.id_bon_commande,
                b.numero_commande,
                b.date_commande,
                b.montant_estime,
                b.statut,
                f.nom AS fournisseur_nom
            FROM bon_commande b
            JOIN fournisseur f ON b.fournisseur_id = f.id_fournisseur
            WHERE b.departement_id = ?
            ORDER BY b.id_bon_commande DESC
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$departement_id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColisDepartement($departement_id) {
        $sql = "
            SELECT 
                c.id_colis,
                c.numero_suivi,
                c.date_reception,
                c.date_retrait,
                s.libelle AS statut,
                b.numero_commande,
                c.statut_id
            FROM colis c
            JOIN bon_commande b ON c.bon_commande_id = b.id_bon_commande
            JOIN statut_colis s ON c.statut_id = s.id_statut
            WHERE b.departement_id = ?
            ORDER BY c.date_reception DESC
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$departement_id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepensesDepartement($departement_id) {
        $req = $this->db->prepare("
            SELECT 
                b.numero_commande,
                b.date_commande,
                b.montant_estime,
                b.statut
            FROM bon_commande b
            WHERE b.departement_id = ?
            ORDER BY b.date_commande DESC
        ");
        $req->execute([$departement_id]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFournisseursAutorises() {
        $sql = "
            SELECT 
                id_fournisseur,
                nom,
                contact_nom,
                contact_email,
                contact_telephone
            FROM fournisseur
            ORDER BY nom
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}