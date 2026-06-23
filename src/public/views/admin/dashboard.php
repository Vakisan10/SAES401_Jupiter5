<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard – Administrateur</title>
<link rel="stylesheet" href="/assets/css/theme.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body class="tableau-bord">
<aside class="barre-laterale">
<div class="entete-barre">
<img src="/assets/img/logo-iutv.png" class="logo" alt="Logo IUT">
<h2>Administrateur</h2>
<p>Gestion du systeme</p>
</div>
<nav class="menu">
<a class="actif" href="/admin/dashboard">Tableau de bord</a>
<a href="/admin/utilisateurs">Utilisateurs</a>
<a href="/admin/departements">Departements</a>
<a href="/admin/fournisseurs">Fournisseurs</a>
<a href="/admin/devis">Tous les devis</a>
<a href="/admin/colis">Tous les colis</a>
</nav>
<div class="deconnexion">
<a href="/logout">Deconnexion</a>
</div>
</aside>
<main class="contenu">
<div class="page-header">
<div class="page-header-info">
<h1 class="page-title">Tableau de bord</h1>
<p class="page-subtitle">Vue globale du systeme</p>
</div>
</div>
<div class="stats-grid">
<div class="stat-card">
<span class="stat-label">Utilisateurs</span>
<div class="stat-value"><?= $stats["utilisateurs"] ?></div>
<div class="stat-description">Total</div>
</div>
<div class="stat-card stat-blue">
<span class="stat-label">Devis</span>
<div class="stat-value"><?= $stats["devis"] ?></div>
<div class="stat-description">Total</div>
</div>
<div class="stat-card stat-warning">
<span class="stat-label">Bons de commande</span>
<div class="stat-value"><?= $stats["bons"] ?></div>
<div class="stat-description">Total</div>
</div>
<div class="stat-card stat-success">
<span class="stat-label">Colis</span>
<div class="stat-value"><?= $stats["colis"] ?></div>
<div class="stat-description">Total</div>
</div>
</div>

<!-- Bloc graphique montrant combien d'utilisateurs il y a par role -->
<div class="section">
<div class="section-header">
<h2 class="section-title">Utilisateurs par role</h2>
</div>
<canvas id="graphiqueRoles" style="max-height:350px;"></canvas>
</div>
<script>
// $roles vient de countUtilisateursParRole() dans AdminModels
// C'est un tableau avec le nom du role et le nombre d'utilisateurs
const roles = <?= json_encode($roles) ?>;
new Chart(document.getElementById('graphiqueRoles'), {
    type: 'bar',
    data: {
        labels: roles.map(r => r.libelle), // le nom de chaque role
        datasets: [{
            label: 'Nombre d\'utilisateurs',
            data: roles.map(r => r.total), // le nombre de personnes dans ce role
            backgroundColor: '#5BB8B0'
        }]
    },
    options: {
        indexAxis: 'y', // barres horizontales, plus lisible avec des noms longs
        responsive: true
    }
});
</script>

<div class="section">
<div class="section-header">
<h2 class="section-title">Repartition des utilisateurs par role</h2>
</div>
<div class="table-container">
<table class="data-table">
<thead>
<tr>
<th>Role</th>
<th>Nombre</th>
</tr>
</thead>
<tbody>
<?php if (empty($roles)): ?>
<tr><td colspan="2" class="empty-state">Aucun role trouve</td></tr>
<?php else: ?>
<?php foreach ($roles as $r): ?>
<tr>
<td><strong><?= ucfirst(htmlspecialchars($r["libelle"])) ?></strong></td>
<td><?= $r["total"] ?></td>
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