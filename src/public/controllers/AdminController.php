<?php
require_once __DIR__ . '/../models/AdminModels.php';
require_once __DIR__ . '/../views/partials/flash.php';

class AdminController {

    private $model;

    public function __construct() {
        $this->model = new AdminModels();
    }

    public function dashboard() {
        $stats = [
            "utilisateurs" => $this->model->countUtilisateurs(),
            "devis"        => $this->model->countDevis(),
            "bons"         => $this->model->countBonsCommande(),
            "colis"        => $this->model->countColis()
        ];
        $roles = $this->model->countUtilisateursParRole();
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function utilisateurs() {
        $utilisateurs = $this->model->getTousLesUtilisateurs();
        $roles        = $this->model->getRoles();
        $departements = $this->model->getDepartements();
        require __DIR__ . '/../views/admin/utilisateurs.php';
    }

    public function updateUtilisateur() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            die("Accès invalide");
        }
        $this->model->updateUtilisateur(
            $_POST["id_utilisateur"],
            $_POST["role_id"],
            $_POST["departement_id"] ?: null
        );
        header("Location: /admin/utilisateurs?ok=1");
        exit;
    }

    public function fournisseurs() {
        $fournisseurs = $this->model->getFournisseurs();
        require __DIR__ . '/../views/admin/fournisseurs.php';
    }

    public function ajouterFournisseur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->ajouterFournisseur($_POST);
            header('Location: /admin/fournisseurs');
            exit;
        }
    }

    public function updateFournisseur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateFournisseur(
                $_POST['id_fournisseur'],
                $_POST
            );
            header('Location: /admin/fournisseurs');
            exit;
        }
    }

    public function modifierFournisseur() {
        if (!isset($_GET['id'])) {
            die("ID fournisseur manquant");
        }
        $fournisseur = $this->model->getFournisseurById($_GET['id']);
        if (!$fournisseur) {
            die("Fournisseur introuvable");
        }
        require __DIR__ . '/../views/admin/modifier-fournisseur.php';
    }

    public function departements() {
        $departements = $this->model->getDepartementsAdmin();
        require __DIR__ . '/../views/admin/departements.php';
    }

    public function ajouterDepartement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->ajouterDepartement(
                $_POST['nom'],
                $_POST['budget_total']
            );
            header("Location: /admin/departements");
            exit;
        }
    }

    public function modifierDepartement() {
        if (!isset($_GET['id'])) {
            die("ID département manquant");
        }
        $departement = $this->model->getDepartementById($_GET['id']);
        require __DIR__ . '/../views/admin/modifier-departement.php';
    }

    public function updateDepartement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateDepartement(
                $_POST['id_departement'],
                $_POST['nom'],
                $_POST['budget_total']
            );
            header("Location: /admin/departements");
            exit;
        }
    }

    public function devis() {
        $stats  = $this->model->countDevisParStatut();
        $search = $_GET['q'] ?? null;
        $devis  = $this->model->getTousLesDevis($search);
        require __DIR__ . '/../views/admin/devis.php';
    }

    public function commandes() {
        $stats     = $this->model->countCommandesParStatut();
        $search    = $_GET['q'] ?? null;
        $commandes = $this->model->getToutesLesCommandes($search);
        require __DIR__ . '/../views/admin/commandes.php';
    }

    public function colis() {
        $stats  = $this->model->countColisParStatut();
        $search = $_GET['q'] ?? null;
        $colis  = $this->model->getTousLesColisAdmin($search);
        require __DIR__ . '/../views/admin/colis.php';
    }

    public function exportColis() {
        $colis = $this->model->getTousLesColisAdmin();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export-colis.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Numéro suivi', 'Statut', 'Date réception', 'Destinataire']);
        foreach ($colis as $c) {
            fputcsv($out, [
                $c['id_colis'],
                $c['numero_suivi'] ?? 'N/A',
                $c['statut_libelle'] ?? 'N/A',
                $c['date_reception'] ?? 'N/A',
                $c['destinataire_nom'] ?? 'N/A'
            ]);
        }
        fclose($out);
        exit;
    }

    public function exportDevis() {
        $devis = $this->model->getTousLesDevisExport();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export-devis.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Objet', 'Montant', 'Statut', 'Date demande', 'Département']);
        foreach ($devis as $d) {
            fputcsv($out, [
                $d['id_devis'],
                $d['objet'] ?? 'N/A',
                $d['montant_estime'] ?? 'N/A',
                $d['statut'] ?? 'N/A',
                $d['date_demande'] ?? 'N/A',
                $d['departement'] ?? 'N/A'
            ]);
        }
        fclose($out);
        exit;
    }

    public function exportBonsCommande() {
        $bons = $this->model->getTousLesBonsCommandeExport();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export-bons-commande.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Numéro commande', 'Montant', 'Statut', 'Date commande', 'Département', 'Fournisseur']);
        foreach ($bons as $b) {
            fputcsv($out, [
                $b['id_bon_commande'],
                $b['numero_commande'] ?? 'N/A',
                $b['montant_estime'] ?? 'N/A',
                $b['statut'] ?? 'N/A',
                $b['date_commande'] ?? 'N/A',
                $b['departement'] ?? 'N/A',
                $b['fournisseur'] ?? 'N/A'
            ]);
        }
        fclose($out);
        exit;
    }
}