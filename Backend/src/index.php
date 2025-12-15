<?php

/**
 * Point d'entrée unique de l'application
 * Toutes les requêtes passent par ce fichier
 */

// Charger les variables d'environnement
require_once __DIR__ . '/bootstrap.php';

// Charger toutes les classes (remplace l'autoloader Composer)
require_once __DIR__ . '/includes.php';

// Charger la configuration
$config = require __DIR__ . '/../config/app.php';

// Routes publiques (pas d'auth requise)
$publicRoutes = ['/dev-login'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si route publique, ne pas authentifier
if (!in_array($uri, $publicRoutes)) {
    // Initialiser CAS
    $casConfig = new CasConfiguration(
        $config['cas']['host'],
        $config['cas']['context'],
        $config['cas']['port'],
        $config['cas']['ca_cert_path'],
        $config['base_url']
    );
    $casAuth = new CasAuthenticator($casConfig);

    // Middleware d'authentification - Force l'auth CAS si nécessaire
    $authMiddleware = new AuthMiddleware($casAuth);
    $currentUser = $authMiddleware->handle();
}

// Initialiser le router
$router = new Router();

// ========== ROUTES GET ==========

// Dashboard
$router->get('/postal/dashboard', 'IutPostalController', 'dashboard');
$router->get('/postal', 'IutPostalController', 'dashboard'); // Alias

// Colis - Consultation
$router->get('/postal/colis/recus', 'IutPostalController', 'colisRecus');
$router->get('/postal/colis/remis', 'IutPostalController', 'colisRemis');
$router->get('/postal/colis/attente', 'IutPostalController', 'colisEnAttente');
$router->get('/postal/colis/details/:id', 'IutPostalController', 'colisDetails');

// Colis - Actions
$router->get('/postal/colis/ajouter', 'IutPostalController', 'ajouterColis');
$router->get('/postal/colis/modifier/:id', 'IutPostalController', 'modifierColis');
$router->get('/postal/colis/livrer/:id', 'IutPostalController', 'actionLivrer');
$router->get('/postal/colis/retirer/:id', 'IutPostalController', 'actionRetirer');

// Colis - Recherche
$router->get('/postal/colis/recherche', 'IutPostalController', 'rechercheColis');

// Colis non identifiés
$router->get('/postal/colis/non-identifies', 'IutPostalController', 'nonIdentifies');
$router->get('/postal/colis/marquer-non-identifie/:id', 'IutPostalController', 'actionMarquerNonIdentifier');

// Historique
$router->get('/postal/historique', 'IutPostalController', 'historiqueGlobal');

// ========== ROUTES POST ==========

// Colis - Création/Modification
$router->post('/postal/colis/ajouter', 'IutPostalController', 'ajouterColis');
$router->post('/postal/colis/modifier/:id', 'IutPostalController', 'updateColis');
$router->post('/postal/colis/update', 'IutPostalController', 'actionModifier'); // Legacy

// Colis non identifiés - Assignation
$router->post('/postal/colis/assigner', 'IutPostalController', 'actionAssigner');

// ========== ROUTES AUTH ==========

$router->get('/auth/logout', 'AuthController', 'logout');

// ========== ROUTES DEV (mode développement uniquement) ==========

if ($config['env'] === 'development') {
    $router->get('/dev-login', 'DevAuthController', 'loginForm');
    $router->post('/dev-login', 'DevAuthController', 'loginSubmit');
}

// ========== DISPATCH ==========

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Vérifier les permissions (sauf pour routes publiques)
    if (!in_array($uri, $publicRoutes)) {
        AuthorizationMiddleware::authorize($uri, $currentUser);
    }

    // Dispatcher vers le contrôleur
    [$controllerClass, $methodName, $params] = $router->dispatch($method, $uri);

    // Instancier le contrôleur (plus besoin de \\ devant car pas de namespace)
    // Routes publiques : pas d'injection User
    if (in_array($uri, $publicRoutes)) {
        $controller = new $controllerClass();
    } else {
        // Routes protégées : injection User
        $controller = new $controllerClass($currentUser);
    }

    // Appeler la méthode
    call_user_func_array([$controller, $methodName], $params);

} catch (\Exception $e) {
    $code = $e->getCode();

    if ($code == 404) {
        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1>";
        echo "<p>La route demandée n'existe pas.</p>";
        echo "<p><a href='" . url('postal/dashboard') . "'>Retour au dashboard</a></p>";
    } elseif ($code == 403) {
        http_response_code(403);
        echo "<h1>403 - Accès refusé</h1>";
        echo "<p>Vous n'avez pas les permissions pour accéder à cette page.</p>";
        echo "<p>Rôle actuel : " . htmlspecialchars($currentUser->getRole()) . "</p>";
        echo "<p><a href='" . url('postal/dashboard') . "'>Retour au dashboard</a></p>";
    } else {
        http_response_code(500);
        echo "<h1>500 - Erreur serveur</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        if (getenv('APP_ENV') === 'development') {
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    }
}
