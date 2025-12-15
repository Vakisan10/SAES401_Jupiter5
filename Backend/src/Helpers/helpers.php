<?php

/**
 * Génère une URL absolue à partir d'un chemin relatif
 *
 * @param string $path Chemin relatif (ex: "postal/colis/details/123")
 * @return string URL complète
 */
function url(string $path): string
{
    global $config;
    $base = rtrim($config['base_url'], '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

/**
 * Redirige vers une URL
 *
 * @param string $path Chemin relatif
 * @return never
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Charge et affiche une vue
 *
 * @param string $viewName Nom de la vue (ex: "postal-iut/dashboard")
 * @param array $data Données à passer à la vue
 * @return void
 */
function view(string $viewName, array $data = []): void
{
    // Rendre les variables disponibles dans la vue
    extract($data);

    // Charger la vue
    $viewPath = __DIR__ . "/../views/{$viewName}.php";

    if (!file_exists($viewPath)) {
        throw new \Exception("View not found: {$viewName} (searched in {$viewPath})");
    }

    require $viewPath;
}

/**
 * Génère une URL vers un asset (CSS, JS, image)
 *
 * @param string $path Chemin vers l'asset (ex: "css/style.css")
 * @return string URL complète
 */
function asset(string $path): string
{
    global $config;
    $base = rtrim($config['base_url'], '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

/**
 * Échappe une chaîne pour l'affichage HTML
 *
 * @param string|null $value
 * @return string
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Retourne l'utilisateur actuellement connecté
 *
 * @return \SAE\Auth\User|null
 */
function currentUser(): ?\SAE\Auth\User
{
    return $_SESSION['user'] ?? null;
}
