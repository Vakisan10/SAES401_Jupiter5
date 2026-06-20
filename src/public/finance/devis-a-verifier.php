<?php
// routes/finance/pdf-devis.php
// Route : GET /finance/pdf-devis?id=X
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../controllers/FinanceController.php';

$controller = new FinanceController();
$controller->streamPdfDevis();