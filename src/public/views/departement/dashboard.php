<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Departement</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
</head>

<body class="tableau-bord">

<aside class="barre-laterale">
    <div class="entete-barre">
        <img src="/assets/img/logo-iutv.png" class="logo" alt="Logo IUT">
        <h2><?= htmlspecialchars($userInfo['departement'] ?? 'Departement') ?></h2>
        <p><?= htmlspecialchars($userInfo['nom'] ?? 'Utilisateur') ?></p>
    </div>

    <nav class="menu">
        <a class="actif" href="/departement/dashboard">Tableau de bord</a>
        <a href="/departement/creer-devis">Creer un devis</a>
        <a href="/departement/mes-devis">Mes devis</a>
        <a href="/departement/bons-commande">Mes bons de commande</a>
        <a href="/departement/mes-colis">Mes colis</a>
        <a href="/departement/budget">Budget</a>
        <a href="/departement/fournisseurs">Fournisseurs</a>
    </nav>

    <div class="deconnexion">
        <a href="/logout">Deconnexion</a>
    </div>
</aside>

<main class="contenu">

    <div class="page-header" style="position:relative;">

        <!-- WIDGET NOTIFICATIONS -->
        <div style="position:absolute; top:0; right:0;">
            <div style="position:relative; display:inline-block;">
                <span style="font-size:26px; cursor:pointer;" onclick="document.getElementById('notif-panel').style.display = document.getElementById('notif-panel').style.display === 'none' ? 'block' : 'none'">
                    🔔
                    <?php if ($notifCount > 0): ?>
                        <span style="position:absolute; top:-6px; right:-10px; background:#e74c3c; color:white; border-radius:50%; font-size:12px; width:20px; height:20px; display:flex; align-items:center; justify-content:center;">
                            <?= $notifCount ?>
                        </span>
                    <?php endif; ?>
                </span>
                <div id="notif-panel" style="display:none; position:absolute; right:0; top:36px; width:320px; background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:999;">
                    <div style="padding:12px 16px; font-weight:bold; border-bottom:1px solid #eee;">Notifications</div>
                    <?php if (empty($notifications)): ?>
                        <div style="padding:16px; color:#888; text-align:center;">Aucune notification</div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <div style="padding:12px 16px; border-bottom:1px solid #f5f5f5; font-size:14px; <?= $notif['lu'] == 0 ? 'background:#f0f6ff;' : '' ?>">
                                <?= htmlspecialchars($notif['message_notification']) ?>
                                <div style="font-size:11px; color:#aaa; margin-top:4px;">
                                    <?= date('d/m/Y H:i', strtotime($notif['date_envoi'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- FIN WIDGET -->

        <div class="page-header-info">
            <h1 class="page-title">Tableau de bord</h1>
            <p class="page-subtitle">Gerez vos devis, commandes et colis</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <button class="btn btn-primary" onclick="window.location.href='/departement/creer-devis'">
                Creer un devis
            </button>
            <div style="position:relative;">
                <span style="font-size:26px; cursor:pointer;" onclick="document.getElementById('notif-panel').style.display = document.getElementById('notif-panel').style.display === 'none' ? 'block' : 'none'">🔔<?php if ($notifCount > 0): ?><span style="position:absolute; top:-6px; right:-10px; background:#e74c3c; color:white; border-radius:50%; font-size:12px; width:20px; height:20px; display:flex; align-items:center; justify-content:center;"><?= $notifCount ?></span><?php endif; ?></span>
                <div id="notif-panel" style="display:none; position:absolute; right:0; top:36px; width:320px; background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:999;">
                    <div style="padding:12px 16px; font-weight:bold; border-bottom:1px solid #eee;">Notifications</div>
                    <?php if (empty($notifications)): ?><div style="padding:16px; color:#888; text-align:center;">Aucune notification</div><?php else: ?><?php foreach ($notifications as $notif): ?><div style="padding:12px 16px; border-bottom:1px solid #f5f5f5; font-size:14px; <?= $notif['lu'] == 0 ? 'background:#f0f6ff;' : '' ?>"><?= htmlspecialchars($notif['message_notification']) ?><div style="font-size:11px; color:#aaa; margin-top:4px;"><?= date('d/m/Y H:i', strtotime($notif['date_envoi'])) ?></div></div><?php endforeach; ?><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <span class="stat-label">Colis total</span>
            <div class="stat-value"><?php echo $stats['colis_total']; ?></div>
            <div class="stat-description">Total des colis</div>
        </div>

        <div class="stat-card stat-warning">
            <span class="stat-label">Colis en attente</span>
            <div class="stat-value"><?php echo $stats['en_attente']; ?></div>
            <div class="stat-description">A recuperer</div>
        </div>

        <div class="stat-card stat-success">
            <span class="stat-label">Colis retires</span>
            <div class="stat-value"><?php echo $stats['retire']; ?></div>
            <div class="stat-description">Receptions confirmees</div>
        </div>
    </div>

    <?php if (isset($budget)): ?>
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Budget du departement</h2>
            <span class="section-subtitle">Situation budgetaire</span>
        </div>

        <div class="stats-grid" style="margin-bottom: 0;">
            <div class="stat-card">
                <span class="stat-label">Budget total</span>
                <div class="stat-value" style="font-size: 24px;"><?php echo number_format($budget['budget_total'], 2, ',', ' '); ?> EUR</div>
            </div>
            <div class="stat-card">
                <span class="stat-label">Budget utilise</span>
                <div class="stat-value" style="font-size: 24px;"><?php echo number_format($budget['budget_utilise'], 2, ',', ' '); ?> EUR</div>
            </div>
            <div class="stat-card">
                <span class="stat-label">Budget restant</span>
                <div class="stat-value" style="font-size: 24px;"><?php echo number_format($budget['budget_restant'], 2, ',', ' '); ?> EUR</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Derniers colis</h2>
            <span class="section-subtitle">Suivez vos livraisons recentes</span>
            <a href="/departement/mes-colis" class="btn-link">Voir tout</a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Suivi</th>
                        <th>BC lie</th>
                        <th>Destinataire</th>
                        <th>Date reception</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($colis)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">Aucun colis trouve</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($colis as $col): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($col['numero_suivi'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($col['numero_commande'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($col['destinataire_nom'] ?? 'Non assigne'); ?></td>
                                <td><?php echo isset($col['date_reception']) ? date('d/m/Y', strtotime($col['date_reception'])) : 'N/A'; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '_', $col['statut_libelle'])); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $col['statut_libelle'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

</body>
</html>