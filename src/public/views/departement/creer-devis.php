<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creer un devis – Departement</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
</head>

<body class="tableau-bord">
<aside class="barre-laterale">
    </aside>

<main class="contenu">
    <div class="create-devis-page">
        <div class="page-header-simple">
            <a href="/departement/dashboard" class="back-button-simple">&larr; Retour</a>
        </div>

        <div class="form-container">
            <form method="POST" action="/departement/envoyer-devis" class="devis-form" id="devisForm" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="form-group">
                        <label for="objet" class="form-label required">Objet de la demande</label>
                        <input type="text" id="objet" name="objet" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="fournisseur_id" class="form-label required">Fournisseur</label>
                        <select id="fournisseur_id" name="fournisseur_id" class="form-select" required>
                            <option value="">Selectionnez un fournisseur</option>
                            <?php foreach ($fournisseurs as $fournisseur): ?>
                                <option value="<?= $fournisseur['id_fournisseur']; ?>">
                                    <?= htmlspecialchars($fournisseur['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="montant_estime" class="form-label required">Montant estime (EUR)</label>
                        <input type="number" id="montant_estime" name="montant_estime" class="form-input" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="fichier_pdf" class="form-label required">Document PDF du devis</label>
                        <input type="file" id="fichier_pdf" name="fichier_pdf" class="form-input" accept="application/pdf" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='/departement/dashboard'">Annuler</button>
                    <button type="submit" class="btn btn-primary">Creer et envoyer le devis</button>
                </div>
            </form>
        </div>
    </div>
</main>
</body>
</html>