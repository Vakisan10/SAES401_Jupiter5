<?php

require_once __DIR__ . "/../models/PostalUnivModels.php";

class PostalUnivController {

    private $model;

    public function __construct() {
        $this->model = new PostalUnivModels();
    }

    /* ===================== DASHBOARD ===================== */
    public function dashboard() {

        $stats = [
            "recus_aujourdhui"     => $this->model->countColisRecusUnivAujourdhui(),
            "en_attente_transfert" => $this->model->countColisEnAttenteTransfert(),
            "transferes_auj"       => $this->model->countColisTransferesAujourdhui(),
            "total_colis"          => $this->model->countTotalColisUniv()
        ];

        $colis_recents        = $this->model->getColisRecentsUniv();
        $statuts_repartition  = $this->model->getRepartitionStatutsUniv();
        $colis_departements   = $this->model->getColisByDepartement();

        require __DIR__ . "/../views/postal-univ/dashboard.php";
    }

    /* ===================== COLIS RECUS UNIV ===================== */
    public function colisRecusUniv() {
        $colis = $this->model->getAllColisUniv();
        $statuts = $this->model->getAllStatuts();

        require __DIR__ . "/../views/postal-univ/colis-recus-univ.php";
    }

    /* ===================== COLIS EN ATTENTE DE TRANSFERT ===================== */
    public function colisEnAttenteTransfert() {
        $colis = $this->model->getColisEnAttenteTransfert();
        require __DIR__ . "/../views/postal-univ/colis-attente-transfert.php";
    }

    /* ===================== HISTORIQUE DES TRANSFERTS ===================== */
    public function historiqueTransferts() {
        $historique = $this->model->getHistoriqueTransferts();
        require __DIR__ . "/../views/postal-univ/historique-transferts.php";
    }

    /* ===================== RECHERCHE COLIS ===================== */
    public function rechercheColisUniv() {

        $resultats = [];
        $motcle = "";

        if (!empty($_GET["q"])) {
            $motcle = trim($_GET["q"]);
            $resultats = $this->model->rechercherColisUniv($motcle);
        }

        require __DIR__ . "/../views/postal-univ/recherche-colis-univ.php";
    }

    /* ===================== AJOUTER UN COLIS ===================== */
    public function ajouterColisUniv() {

        $message = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $num_bc      = trim($_POST["numero_bc"]);
            $num_suivi   = trim($_POST["numero_suivi"]);
            $commentaire = trim($_POST["commentaire"]);

            // Récupérer info sur le BC
            $bcInfo = $this->model->getBCInfo($num_bc);

            if (!$bcInfo) {
                $message = "❌ Numéro de bon de commande introuvable.";
            } else {

                $data = [
                    "bon_commande_id" => $bcInfo["id_bon_commande"],
                    "numero_suivi"    => $num_suivi,
                    "destinataire_id" => $bcInfo["destinataire_id"],
                    "commentaire"     => $commentaire
                ];

                $ok = $this->model->insertColisUniv($data);

                if ($ok) {
                    $message = "✅ Colis ajouté avec succès et prêt pour transfert IUT.";
                } else {
                    $message = "❌ Erreur lors de l'enregistrement du colis.";
                }
            }
        }

        require __DIR__ . "/../views/postal-univ/ajouter-colis-univ.php";
    }

    /* ===================== DETAILS COLIS ===================== */
    public function colisDetailsUniv() {

        if (!isset($_GET["id"])) {
            die("ID colis manquant");
        }

        $id = intval($_GET["id"]);
        $colis = $this->model->getColisById($id);
        $historique = $this->model->getHistorique($id);

        if (!$colis) {
            die("Colis introuvable");
        }

        require __DIR__ . "/../views/postal-univ/colis-details-univ.php";
    }

    /* ===================== TRANSFERER VERS IUT ===================== */
    public function actionTransfererIUT() {
        if (!isset($_GET["id"])) {
            die("ID manquant");
        }

        $id = intval($_GET["id"]);
        $this->model->transfererVersIUT($id);

        header("Location: colis-details-univ.php?id=$id");
        exit;
    }

    public function actionConfirmerTransfert() {
        if (!isset($_GET["id"])) {
            die("ID manquant");
        }

        $id = intval($_GET["id"]);
        $this->model->confirmerTransfertIUT($id);

        header("Location: colis-details-univ.php?id=$id");
        exit;
    }

    /* ===================== LISTE COLIS TRANSFERES ===================== */
    public function colisTransferes() {
        $colis = $this->model->getColisTransferes();
        require __DIR__ . "/../views/postal-univ/colis-transferes.php";
    }
}