<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche colis – Service Postal IUT</title>
    <link rel="stylesheet" href="/Public/css/style-dashboard.css?v=20">
    <link rel="stylesheet" href="/Public/css/style-recherche.css?v=1">
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
            <a href="/postal/colis/non-identifies">❓ Colis non identifiés</a>
            <a href="/postal/historique">📜 Historique global</a>


        </nav>

        <div class="deconnexion">
            <a href="/COLIS_SAE/logout.php">🚪 Déconnexion</a>
        </div>
    </aside>


    <!-- CONTENU -->
    <main class="contenu">

        <h1>🔍 Recherche d’un colis</h1>
        <p class="sous-titre">Recherchez par numéro de suivi, numéro de commande, ID colis ou département</p>

        <!-- BARRE DE RECHERCHE -->
        <form class="barre-recherche" method="get">
            <input type="text" name="q" placeholder="Rechercher..." value="<?= htmlspecialchars($motcle) ?>">
            <button type="submit">Rechercher</button>
        </form>


        <!-- TABLEAU RESULTATS -->
        <table class="tableau">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Département</th>
                    <th>N° commande</th>
                    <th>N° suivi</th>
                    <th>Date réception</th>
                    <th>Statut</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($resultats as $c): ?>
                <tr>
                    <td><?= $c["id_colis"] ?></td>
                    <td><?= $c["departement"] ?: "Non identifié" ?></td>
                    <td><?= $c["numero_commande"] ?></td>
                    <td><?= $c["numero_suivi"] ?></td>
                    <td><?= $c["date_reception"] ?></td>
                    <td>
                        <?php if ($c["statut_id"] == 1): ?>
                            <span class="badge badge-attente">En attente</span>
                        <?php elseif ($c["statut_id"] == 2): ?>
                            <span class="badge badge-livre">Livré</span>
                        <?php else: ?>
                            <span class="badge badge-autre">Autre</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($resultats) && !empty($motcle)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">
                        Aucun colis trouvé.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </main>

</body>
</html>
