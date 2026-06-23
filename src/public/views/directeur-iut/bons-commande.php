<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bons de commande – Directeur</title>
<link rel="stylesheet" href="/assets/css/theme.css">
</head>
<body class="tableau-bord">
<aside class="barre-laterale">
<div class="entete-barre">
<img src="/assets/img/logo-iutv.png" class="logo" alt="Logo IUT">
<h2>Directeur IUT</h2>
<p>Validation et signature</p>
</div>
<nav class="menu">
<a href="/directeur/dashboard">Tableau de bord</a>
<a href="/directeur/devis">Devis a signer</a>
<a class="actif" href="/directeur/bons-commande">Bons de commande</a>
</nav>
<div class="deconnexion">
<a href="/logout">Deconnexion</a>
</div>
</aside>
<main class="contenu">
<div class="page-header">
<div class="page-header-info">
<h1 class="page-title">Bons de commande</h1>
<p class="page-subtitle">Historique des bons de commande valides</p>
</div>
</div>
<div class="section">
<div class="table-container">
<table class="data-table">
<thead>
<tr>
<th>N° Commande</th>
<th>Departement</th>
<th>Fournisseur</th>
<th>Date estimee livraison</th>
<th>Statut</th>
</tr>
</thead>
<tbody>
<?php if (empty($bons)): ?>
<tr><td colspan="5" class="empty-state">Aucun bon de commande</td></tr>
<?php else: ?>
<?php foreach ($bons as $bc): ?>
<tr>
<td><strong><?= htmlspecialchars($bc["numero_commande"]) ?></strong></td>
<td><?= htmlspecialchars($bc["departement"] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($bc['fournisseur'] ?? 'N/A') ?></td>
<td><?= isset($bc['date_estimee_livraison']) ? date('d/m/Y', strtotime($bc['date_estimee_livraison'])) : 'N/A' ?></td>
<td>
<?php
// On choisit la couleur du badge selon le statut du bon de commande
$couleur = match($bc['statut']) {
    'en_preparation' => '#2E6DA4', // bleu
    'en_cours' => '#F39C12', // orange
    'livree' => '#27AE60', // vert
    'annule' => '#E74C3C', // rouge
    default => '#999999'
};
?>
<span style="background:<?= $couleur ?>; color:white; padding:4px 10px; border-radius:12px; font-size:12px;">
<?= ucfirst(str_replace('_', ' ', $bc['statut'])) ?>
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