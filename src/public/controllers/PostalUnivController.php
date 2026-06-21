<?php
require_once __DIR__ . '/../models/PostalUnivModels.php';

class PostalUnivController
{

    private $model;

    public function __construct()
    {
        $this->model = new PostalUnivModels();
    }

    public function dashboard()
    {

        $stats = [
            "recus" => $this->model->getColisRecus(),
            "a_transferer" => $this->model->getColisATransferer(),
            "transferes" => $this->model->getColisTransferes(),
            "non_identifies" => $this->model->getColisNonIdentifies()
        ];

        $colis_recents = $this->model->getDerniersColis();

        require __DIR__ . '/../views/postal-univ/dashboard.php';
    }


    public function receptionColis()
    {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $this->model->ajouterColisUniversite([
                "numero_commande" => $_POST["numero_commande"],
                "numero_suivi" => $_POST["numero_suivi"],
                "commentaire" => $_POST["commentaire"] ?? null
            ]);

            header("Location: /postal-univ/reception?ok=1");
            exit;
        }

        require __DIR__ . '/../views/postal-univ/reception-colis.php';
    }


    public function listeColis()
    {

        $colis = $this->model->getTousLesColis();

        require __DIR__ . '/../views/postal-univ/colis.php';
    }


    public function transfererColis()
    {

        if (!isset($_GET["id"])) {
            die("ID colis manquant");
        }

        $id_colis = intval($_GET["id"]);

        $this->model->transfererVersIUT($id_colis);

        header("Location: /postal-univ/colis?transfer=ok");
        exit;
    }

    public function nonIdentifies()
    {

        $colis = $this->model->getColisNonIdentifiesListe();

        require __DIR__ . '/../views/postal-univ/non-identifies.php';
    }


    public function historique()
    {

        $historique = $this->model->getHistorique();

        require __DIR__ . '/../views/postal-univ/historique.php';
    }


    public function lookup()
    {
        header('Content-Type: application/json');

        $numero = $_GET['bc'] ?? '';

        if ($numero === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Numéro vide'
            ]);
            return;
        }

        $infos = $this->model->getInfosParNumeroCommande($numero);

        if ($infos) {
            echo json_encode([
                'success' => true,
                'data' => $infos
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Bon de commande introuvable'
            ]);
        }
    }

    public function assignerNonIdentifie()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            die("Accès invalide");
        }

        $id_colis = intval($_POST["id_colis"]);
        $numero_commande = trim($_POST["numero_commande"]);

        $infos = $this->model->getInfosParNumeroCommande($numero_commande);

        if (!$infos) {
            header("Location: /postal-univ/non-identifies?erreur=bc");
            exit;
        }

        $this->model->assignerColisAvecBonCommande(
            $id_colis,
            $infos["id_bon_commande"],
            $infos["destinataire_id"]
        );

        $this->model->ajouterHistoriqueAssignation(
            $id_colis,
            $infos["numero_commande"]
        );

        header("Location: /postal-univ/non-identifies?ok=1");
        exit;
    }


}