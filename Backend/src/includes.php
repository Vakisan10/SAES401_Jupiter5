<?php
/**
 * Fichier central pour charger toutes les classes
 * À inclure au début de index.php
 */

// ============ COMPOSER AUTOLOADER (phpCAS) ============
require_once __DIR__ . '/vendor/autoload.php';

// ============ CORE ============
require_once __DIR__ . '/Core/Router.php';

// ============ AUTH ============
require_once __DIR__ . '/Auth/User.php';
require_once __DIR__ . '/Auth/CasConfiguration.php';
require_once __DIR__ . '/Auth/CasAuthenticator.php';
require_once __DIR__ . '/Auth/AuthMiddleware.php';
require_once __DIR__ . '/Auth/AuthorizationMiddleware.php';

// ============ MODELS ============
require_once __DIR__ . '/Models/Model.php';
require_once __DIR__ . '/Models/IutPostalModels.php';

// ============ REPOSITORIES ============
require_once __DIR__ . '/Repositories/UserRepository.php';

// ============ CONTROLLERS ============
require_once __DIR__ . '/Controllers/IutPostalController.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/DevAuthController.php';
require_once __DIR__ . '/Controllers/ServicePostalController.php';

// ============ HELPERS ============
require_once __DIR__ . '/Helpers/helpers.php';
