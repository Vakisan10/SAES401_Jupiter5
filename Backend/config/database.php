<?php

// Fichier requis par Model.php ligne 11
$config = require __DIR__ . '/app.php';

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $config['database']['host'],
    $config['database']['port'],
    $config['database']['name']
);

$user = $config['database']['user'];
$password = $config['database']['password'];
