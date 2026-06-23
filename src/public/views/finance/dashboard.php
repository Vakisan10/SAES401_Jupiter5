<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Service Financier</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <!-- Librairie pour faire des graphiques, chargée depuis internet -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>

<body class="tableau-bord">

<aside class="barre-laterale">
    <div class="entete-barre">
        <img src="/assets/img/logo-iutv.png" class="logo" alt="Logo IUT">
        <h2>Service Financier</h2>
        <p>Gestion budgetaire</p>
    </div>

    <nav class="menu">
        <a class="actif" href="/finance/dashboard">Tableau de bord</a>
        <a href="/finance/devis">Devis a verifier</a>
        <a href="/finance/bons-commande">Bons de commande</a>
        <a href="/finance/budgets">Budgets</a>
    </nav>

    <div class="deconnexion">
        <a href="/logout">Deconnexion</a>
    </div>
</aside>

<main class="contenu">

    <div class="page-header">
        <div class="page-header-info">
            <h1 class="page-title">Tableau de bord</h1>
            <p class="page-subtitle">Suivi budgetaire et validation des devis</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-warning">
            <span class="stat-label">Devis en attente</span>
            <div class="stat-value"><?= $stats["devis_attente"] ?></div>
            <div class="stat-description">A verifier</div>
        </div>

        <div class="stat-card stat-blue">
            <span class="stat-label">Bons de commande</span>
            <div class="stat-value"><?= $stats["bons_commande"] ?></div>
            <div class="stat-description">Total</div>
        </div>
    </div>

    <!-- Bloc qui contient le graphique du budget par departement -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Budget par departement</h2>
        </div>
        <!-- Le canvas est la zone ou le graphique va se dessiner -->
        <canvas id="graphiqueBudget" style="max-height:350px;"></canvas>
    </div>
    <script>
        // On recupere les donnees envoyees par le controller en PHP
        // et on les convertit en format JSON pour que JavaScript puisse les lire
        const budgets = <?= json_encode($budgets) ?>;
        // On extrait juste les noms des departements pour l'axe horizontal
        const noms = budgets.map(b => b.nom);
        // On extrait le budget total de chaque departement
        const totaux = budgets.map(b => b.budget_total);
        // On extrait le budget deja utilise de chaque departement
        const utilises = budgets.map(b => b.budget_utilise);

        // On recupere la zone de dessin (le canvas) et on cree le graphique dedans
        new Chart(document.getElementById('graphiqueBudget'), {
            type: 'bar', // barres verticales
            data: {
                labels: noms, // les noms des departements en bas
                datasets: [
                    {
                        label: 'Budget total',
                        data: totaux,
                        backgroundColor: '#2E6DA4' // bleu
                    },
                    {
                        label: 'Budget utilise',
                        data: utilises,
                        backgroundColor: '#E74C3C' // rouge
                    }
                ]
            },
            options: {
                responsive: true // le graphique s'adapte a la taille de l'ecran
            }
        });
    </script>

    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Budgets des departements</h2>
            <a href="/finance/budgets" class="btn-link">Voir tout</a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Departement</th>
                        <th>Budget total</th>
                        <th>Budget utilise</th>
                        <th>Restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($budgets)): ?>
                        <tr><td colspan="4" class="empty-state">Aucun budget trouve</td></tr>
                    <?php else: ?>
                        <?php foreach ($budgets as $b): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($b["nom"]) ?></strong></td>
                            <td><?= number_format($b["budget_total"], 2, ',', ' ') ?> EUR</td>
                            <td><?= number_format($b["budget_utilise"], 2, ',', ' ') ?> EUR</td>
                            <td><span class="montant"><?= number_format($b["budget_total"] - $b["budget_utilise"], 2, ',', ' ') ?> EUR</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Devis a verifier</h2>
            <a href="/finance/devis" class="btn-link">Voir tout</a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Objet</th>
                        <th>Departement</th>
                        <th>Montant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($devis)): ?>
                        <tr><td colspan="5" class="empty-state">Aucun devis en attente</td></tr>
                    <?php else: ?>
                        <?php foreach ($devis as $d): ?>
                        <tr>
                            <td>#<?= $d["id_devis"] ?></td>
                            <td><strong><?= htmlspecialchars($d["objet"]) ?></strong></td>
                            <td><?= htmlspecialchars($d["departement"]) ?></td>
                            <td><span class="montant"><?= number_format($d["montant_estime"], 2, ',', ' ') ?> EUR</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-sm btn-success" href="/finance/valider-devis?id=<?= $d["id_devis"] ?>">Valider</a>
                                    <a class="btn btn-sm btn-danger" href="/finance/rejeter-devis?id=<?= $d["id_devis"] ?>">Rejeter</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================
         SECTION : Commandes en retard
         $commandesEnRetard est fourni par le contrôleur via
         $data["commandesEnRetard"] = $devisModel->getDevisEnRetard();
         ============================================================ -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">&#9888; Commandes en retard</h2>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Departement</th>
                        <th>Fournisseur</th>
                        <th>Date estimee</th>
                        <th>Jours de retard</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commandesEnRetard)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">Aucune commande en retard</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commandesEnRetard as $cmd): ?>
                            <?php $jours = (int) $cmd["jours_retard"]; ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($cmd["numero_commande"]) ?></strong></td>
                                <td><?= htmlspecialchars($cmd["departement"]) ?></td>
                                <td><?= htmlspecialchars($cmd["fournisseur"]) ?></td>
                                <td><?= htmlspecialchars($cmd["date_estimee"]) ?></td>
                                <td>
                                    <span style="
                                        font-weight: <?= $jours > 7 ? 'bold' : '500' ?>;
                                        color: <?= $jours > 7 ? '#c0392b' : '#e67e22' ?>;
                                    ">
                                        <?= $jours ?> jour<?= $jours > 1 ? 's' : '' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- FIN section commandes en retard -->

</main>

</body>
</html>