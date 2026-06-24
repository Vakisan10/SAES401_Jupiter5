<?php
require_once __DIR__ . "/../models/FinanceModels.php";
require_once __DIR__ . '/../views/partials/flash.php';

class FinanceController {

    private $model;

    public function __construct() {
        $this->model = new FinanceModels();
    }

    public function dashboard() {

        $stats = [
            "devis_attente" => $this->model->countDevisEnAttente(),
            "bons_commande" => $this->model->countBonCommande()
        ];

        $budgets = $this->model->getBudgetsDepartements();
        $devis   = $this->model->getDevisEnAttente();
        $bons    = $this->model->getBonsCommandeRecents();

        require __DIR__ . "/../views/finance/dashboard.php";
    }


    public function validerDevis() {
        if (!isset($_GET["id"])) {
            die("ID devis manquant");
        }

        $id = intval($_GET["id"]);
        $this->model->validerDevis($id);

        // Ajout du message flash d'après la consigne
        setFlash('success', 'Devis validé avec succès.'); [cite: 37]
        header("Location: /finance/dashboard"); [cite: 38]
        exit; [cite: 39]
    }

    public function rejeterDevis() {
        if (!isset($_GET["id"])) {
            die("ID devis manquant");
        }

        $id = intval($_GET["id"]);
        $this->model->rejeterDevis($id);

        // Ajout du message flash d'après la consigne
        setFlash('error', 'Devis rejeté.'); [cite: 42]
        header("Location: /finance/dashboard"); [cite: 43]
        exit;
    }


    public function devisAVerifier() {

        $devis = $this->model->getDevisAVerifier();

        require __DIR__ . '/../views/finance/devis-a-verifier.php';
    }

     public function bonsCommande() {
        $bons = $this->model->getTousLesBonsCommande();
        require __DIR__ . '/../views/finance/bons-commande.php';
    }

    public function budgets() {

        $budgets = $this->model->getBudgetDepartements();

        require __DIR__ . "/../views/finance/budgets.php";
    }

    public function voirDevis() {
        if (!isset($_GET['id'])) {
            die("ID devis manquant");
        }

        $id = intval($_GET['id']);
        $devis = $this->model->getDevisComplet($id);

        if (!$devis) {
            die("Devis introuvable");
        }

        require_once __DIR__ . '/../services/PdfGenerator.php';
        $pdfGenerator = new PdfGenerator();
        $pdfGenerator->genererDevis($devis);
    }

}