<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord – Service Postal IUT</title>
    <link rel="stylesheet" href="/Public/css/style-dashboard.css?v=11">
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
            <a class="actif" href="/postal/dashboard">📦 Tableau de bord</a>
            <a href="/postal/colis/recus">📥 Colis reçus</a>
            <a href="/postal/colis/remis">📤 Colis remis</a>
            <a href="/postal/colis/recherche">🔍 Recherche colis</a>
            <a href="/postal/colis/non-identifies">❓ Colis non identifiés</a>
            <a href="/postal/historique">📜 Historique global</a>

        </nav>

        <div class="deconnexion">
            <a href="/COLIS_SAE/logout.php">🚪 Déconnexion</a>
        </div>
    </aside>


    <!-- CONTENU PRINCIPAL -->
    <main class="contenu">

        <h1>Tableau de bord – Service Postal IUT</h1>
        <p class="sous-titre">Résumé des colis de la journée</p>

        <!-- ACTIONS RAPIDES -->
        <h2 class="titre-section">⚡ Actions rapides</h2>

        <div class="actions-rapides">
            <a href="/postal/colis/ajouter" class="btn-action">📦 Ajouter un colis</a>
            <a href="/postal/colis/attente" class="btn-action">⏳ Colis en attente</a>
            <a href="/postal/colis/recherche" class="btn-action">🔍 Recherche colis</a>
        </div>

        <!-- CARTES STATISTIQUES -->
        <div class="cartes">

            <div class="carte">
                <h3>📦 Colis reçus aujourd’hui</h3>
                <p class="valeur"><?= $stats["recus_aujourdhui"] ?></p>
            </div>

            <div class="carte">
                <h3>⏳ Colis en attente</h3>
                <p class="valeur"><?= $stats["en_attente"] ?></p>
            </div>

            <div class="carte">
                <h3>✔️ Colis remis aujourd’hui</h3>
                <p class="valeur"><?= $stats["livres_auj"] ?></p>
            </div>

            <div class="carte">
                <h3>❓ Colis non identifiés</h3>
                <p class="valeur"><?= $stats["non_identifies"] ?></p>
            </div>

        </div>

        <!-- TABLEAU DES COLIS RECENTS -->
        <h2 class="titre-section">📋 Derniers colis reçus</h2>

        <table class="tableau">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Département</th>
                    <th>N° commande</th>
                    <th>Date réception</th>
                    <th>Statut</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($colis_recents as $c): ?>
                <tr>
                    <td><?= $c["id_colis"] ?></td>
                    <td><?= $c["departement"] ?: "Non identifié" ?></td>
                    <td><?= $c["numero_commande"] ?></td>
                    <td><?= $c["date_reception"] ?></td>
                    <td>
                        <?php if ($c["statut_id"] == 1): ?>
                            <span class="badge badge-attente">En attente</span>

                        <?php elseif ($c["statut_id"] == 2): ?>
                            <span class="badge badge-livre">Livré</span>

                        <?php elseif ($c["statut_id"] == 3): ?>
                            <span class="badge badge-retire">Retiré</span>

                        <?php elseif ($c["statut_id"] == 4): ?>
                            <span class="badge badge-nonid">Non identifié</span>

                        <?php else: ?>
                            <span class="badge badge-autre">Autre</span>
                        <?php endif; ?>

                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- REPARTITION DES STATUTS -->
        <h2 class="titre-section">📊 Répartition des statuts aujourd’hui</h2>

        <table class="tableau">
            <thead>
                <tr>
                    <th>Statut</th>
                    <th>Nombre</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($statuts_repartition as $row): ?>
                <tr>
                    <td><?= $row["statut"] ?></td>
                    <td><?= $row["total"] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TOP DEPARTEMENTS -->
        <h2 class="titre-section">🏛 Top départements destinataires (aujourd’hui)</h2>

        <table class="tableau">
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Colis reçus</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($top_departements as $row): ?>
                <tr>
                    <td><?= $row["departement"] ?></td>
                    <td><?= $row["total"] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>

</body>
</html>