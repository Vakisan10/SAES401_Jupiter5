<?php

return [
    // Le admin a donc tous accès
    'admin' => [
        'admin/*',              
        'departement/*',        
        'finance/*',           
        'directeur_iut/*',     
        'postal_univ/*',        
        'postal_iut/*',         
    ],

    
    // SERVICE FINANCIER
    'finance' => [
        'finance/*',
    ],

    // DIRECTEUR
    'directeur' => [
        'directeur_iut/*',
    ],

    // SERVICE POSTAL UNIVERSITÉ
    'postal_univ' => [
        'postal_univ/*',
    ],

    // SERVICE POSTAL IUT
    'postal_iut' => [
        'postal_iut/*',
    ],

    // DÉPARTEMENT / ACTEUR
    'acteur' => [
        'departement/*',
    ],

];
