<?php
require_once __DIR__ . "/../models/DepartementModels.php";
class DepartementController {

    private $model;

    public function __construct() {
        $this->model = new DepartementModels();
    }

    private function getUserId(): int {
        return $_SESSION['user']->getId();
    }

    private function getDepartementId(): ?int {
        return $_SESSION['user']->getDepartementId() ?? 1;
    }

    private function getUserInfo(): array {
        $user = $_SESSION['user'];
        $departement = $this->model->getDepartementNom($this->getDepartementId());
        return [
            'nom' => $user->getFullName(),
            'departement' => $departement
        ];
    }

    public function dashboard() {
        $departement_id = $this->getDepartementId();
        $userInfo = $this->getUserInfo();

        $stats = [
            "colis_total"   => $this->model->countColisTotal($departement_id),
            "en_attente"    => $this->model->countColisEnAttente($departement_id),
            "retire"        => $this->model->countColisRetires($departement_id),
        ];

        $budget = $this->model->getBudgetDepartement($departement_id);
        if ($budget) {
            $budget['budget_restant'] = $budget['budget_total'] - $budget['budget_utilise'];
        }

        $colis = $this->model->getDerniersColis($departement_id);

        $notifService = new NotificationService();
        $userId = $this->getUserId();
        $notifications = $notifService->getNotificationsNonLues($userId);
        $notifCount = $notifService->countNonLues($userId);

        require __DIR__ . "/../views/departement/dashboard.php";
    }

    public function creerDevis() {
        $fournisseurs = $this->model->getFournisseurs();
        require __DIR__ . '/../views/departement/creer-devis.php';
    }

    /**
     * Méthode mise à jour pour gérer l'upload PDF
     */
    public function envoyerDevis() {
        $objet          = $_POST["objet"];
        $montant        = $_POST["montant_estime"];
        $fournisseur_id = $_POST["fournisseur_id"];
        $commentaire    = $_POST["commentaire"] ?? null;
        $createur_id    = $this->getUserId();

        // 1. Récupération et validation du fichier PDF
        $contenu_pdf = null;
        if (isset($_FILES['fichier_pdf']) && $_FILES['fichier_pdf']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['fichier_pdf']['tmp_name'];
            
            // Vérification stricte du type MIME
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if ($finfo->file($tmpPath) === 'application/pdf') {
                $contenu_pdf = file_get_contents($tmpPath);
            } else {
                die("Erreur : Le fichier envoyé doit être au format PDF.");
            }
        } else {
            die("Erreur : Aucun fichier valide n'a été reçu.");
        }

        // 2. Appel au modèle avec le binaire
        $this->model->insertDevis(
            $objet,
            $montant,
            $fournisseur_id,
            $createur_id,
            $commentaire,
            $contenu_pdf
        );

        header("Location: /departement/dashboard");
        exit;
    }

    public function mesDevis() {
        $idUtilisateur = $this->getUserId();
        $devis = $this->model->getMesDevis($idUtilisateur);
        require __DIR__ . "/../views/departement/mes-devis.php";
    }

    public function mesBonsCommande() {
        $departement_id = $this->getDepartementId();
        $bons = $this->model->getMesBonsCommande($departement_id);
        require __DIR__ . '/../views/departement/mes-bons-commande.php';
    }
        public function mesColis() {
        $departement_id = $this->getDepartementId();
        $colis = $this->model->getColisDepartement($departement_id);
        require __DIR__ . '/../views/departement/mes-colis.php';

    }

    public function budget() {
        $departement_id = $this->getDepartementId();
        $budget = $this->model->getBudgetDepartement($departement_id);
        $depenses = $this->model->getDepensesDepartement($departement_id);
        require __DIR__ . "/../views/departement/budget.php";
    }

    public function fournisseurs() {
        $fournisseurs = $this->model->getFournisseursAutorises();
        require __DIR__ . "/../views/departement/fournisseurs.php";
    }

}