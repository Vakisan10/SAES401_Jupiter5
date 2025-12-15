<?php

return [
    // Administrateur : accès total
    'admin' => [
        'postal/*',
        'devis/*',
        'admin/*',
    ],

    // Personnel du service postal IUT : gestion complète des colis
    'postal_iut' => [
        'postal/*',
    ],

    // Acteur (étudiant, personnel) : consultation uniquement
    'acteur' => [
        'postal/colis/details/*',
        'postal/colis/recherche',
        'postal/dashboard',
    ],
];
