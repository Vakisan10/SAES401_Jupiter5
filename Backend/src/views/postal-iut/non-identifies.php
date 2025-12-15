<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Colis non identifiés</title>
    <link rel="stylesheet" href="/Public/css/style-dashboard.css">
    <link rel="stylesheet" href="/Public/css/style-non-identifies.css">
</head>

<body class="tableau-bord">

    <!-- BARRE LATERALE -->
    <aside class="barre-laterale">
        <div class="entete-barre">
            <img src="/Public/img/logo-iutv.png" class="logo">
            <h2>IUT Colis</h2>
            <p>Service Postal</p>
        </div>

        <nav class="menu">
            <a href="/postal/dashboard">📦 Tableau de bord</a>
            <a href="/postal/colis/recus">📥 Colis reçus</a>
            <a href="/postal/colis/remis">📤 Colis remis</a>
            <a href="/postal/colis/recherche">🔍 Recherche colis</a>
            <a class="actif" href="/postal/colis/non-identifies">❓ Colis non identifiés</a>
            <a href="/postal/historique">📜 Historique global</a>

        </nav>

        <div class="deconnexion">
            <a href="/COLIS_SAE/logout.php">🚪 Déconnexion</a>
        </div>
    </aside>


    <!-- CONTENU PRINCIPAL -->
    <main class="contenu">

        <h1>❓ Colis non identifiés</h1>
        <p class="sous-titre">Liste des colis sans destinataire ou avec étiquette incomplète</p>

        <table class="tableau">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>N° suivi</th>
                    <th>N° commande</th>
                    <th>Date réception</th>
                    <th>Commentaire</th>
                    <th>Assigner à un département</th>
                    <th>Marquer non identifié</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($colis as $c): ?>
                <tr>
                    <td>#<?= $c["id_colis"] ?></td>
                    <td><?= $c["numero_suivi"] ?: "—" ?></td>
                    <td><?= $c["numero_commande"] ?: "—" ?></td>
                    <td><?= $c["date_reception"] ?></td>
                    <td><?= $c["commentaire"] ?></td>

                    <td>
                        <form method="post" action="/postal/colis/assigner">
                            <input type="hidden" name="id_colis" value="<?= $c["id_colis"] ?>">
                            <select name="departement_id">
                                <?php foreach ($departements as $d): ?>
                                    <option value="<?= $d["id_departement"] ?>">
                                        <?= $d["nom"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-action">Assigner</button>
                        </form>
                    </td>

                    <td>
                        <a class="btn-danger"
                           href="/postal/colis/marquer-non-identifie/<?= $c["id_colis"] ?>">
                            🚫 Marquer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>

</body>
</html>
