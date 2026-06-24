<?php
require_once __DIR__ . '/../models/PostalUnivModels.php';
require_once __DIR__ . '/../views/partials/flash.php';

class PostalUnivController {

    private $model;

    public function __construct() {
        $this->model = new PostalUnivModels();
    }

    public function dashboard() {

        $stats = [
            "recus"          => $this->model->getColisRecus(),
            "a_transferer"   => $this->model->getColisATransferer(),
            "transferes"    => $this->model->getColisTransferes(),
            "non_identifies" => $this->model->getColisNonIdentifies()
        ];

        $colis_recents = $this->model->getDerniersColis();

        require __DIR__ . '/../views/postal-univ/dashboard.php';
    }


    public function receptionColis() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $this->model->ajouterColisUniversite([
                "numero_commande" => $_POST["numero_commande"],
                "numero_suivi"    => $_POST["numero_suivi"],
                "commentaire"     => $_POST["commentaire"] ?? null
            ]);

            header("Location: /postal-univ/reception?ok=1");
            exit;
        }

        require __DIR__ . '/../views/postal-univ/reception-colis.php';
    }


    public function listeColis() {

        $colis = $this->model->getTousLesColis();

        require __DIR__ . '/../views/postal-univ/colis.php';
    }


    public function transfererColis() {

        if (!isset($_GET["id"])) {
            die("ID colis manquant");
        }

        $id_colis = intval($_GET["id"]);

        $this->model->transfererVersIUT($id_colis);

        header("Location: /postal-univ/colis?transfer=ok");
        exit;
    }

    public function nonIdentifies() {

        $colis = $this->model->getColisNonIdentifiesListe();

        require __DIR__ . '/../views/postal-univ/non-identifies.php';
    }


    public function historique() {

        $historique = $this->model->getHistorique();

        require __DIR__ . '/../views/postal-univ/historique.php';
    }

}
    public function assignerNonIdentifie() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /postal-univ/non-identifies');
            exit;
        }
        $id_colis = intval($_POST['id_colis'] ?? 0);
        $numero_bc = $_POST['numero_commande'] ?? null;

        if (!$id_colis || !$numero_bc) {
            header('Location: /postal-univ/non-identifies');
            exit;
        }

        $bc = $this->model->getInfosParNumeroCommande($numero_bc);

        if (!$bc) {
            header('Location: /postal-univ/non-identifies');
            exit;
        }

        $this->model->assignerColisBC($id_colis, $bc['id_bon_commande']);
        header('Location: /postal-univ/non-identifies');
        exit;
    }

    public function lookup() {
        if (!isset($_GET['bc'])) {
            echo json_encode(['error' => 'Numéro BC manquant']);
            exit;
        }
        $numero = $_GET['bc'];
        $infos = $this->model->getInfosParNumeroCommande($numero);
        header('Content-Type: application/json');
        if ($infos) {
            echo json_encode(['success' => true, 'data' => $infos]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Introuvable']);
        }
        exit;
    }
