<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Colis en attente – Service Postal IUT</title>
    <link rel="stylesheet" href="/Public/css/style-dashboard.css">
    <link rel="stylesheet" href="/Public/css/style-colis-attente.css">
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
            <a class="actif" href="/postal/colis/attente">⏳ Colis en attente</a>
            <a href="/postal/historique">📜 Historique global</a>

        </nav>

        <div class="deconnexion">
            <a href="/COLIS_SAE/logout.php">🚪 Déconnexion</a>
        </div>
    </aside>

    <!-- CONTENU -->
    <main class="contenu">

        <h1>⏳ Colis en attente</h1>
        <p class="sous-titre">Tous les colis reçus mais non encore livrés</p>

        <table class="tableau">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Département</th>
                    <th>N° commande</th>
                    <th>N° suivi</th>
                    <th>Date réception</th>
                    <th>Détails</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($colis as $c): ?>
                <tr>
                    <td>#<?= $c["id_colis"] ?></td>
                    <td><?= $c["departement"] ?: "Non identifié" ?></td>
                    <td><?= $c["numero_commande"] ?></td>
                    <td><?= $c["numero_suivi"] ?></td>
                    <td><?= $c["date_reception"] ?></td>
                    <td>
                        <a class="btn-action" href="/postal/colis/details/<?= $c["id_colis"] ?>">
                            🔎 Ouvrir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </main>

</body>
</html>
