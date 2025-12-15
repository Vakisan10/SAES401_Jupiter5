<?php

namespace SAE\Auth;

class AuthorizationMiddleware
{
    /**
     * Vérifie si l'utilisateur a la permission d'accéder à une route
     *
     * @param string $route Route demandée (ex: "postal/colis/details/123")
     * @param User $user Utilisateur connecté
     * @return bool
     */
    public static function check(string $route, User $user): bool
    {
        // Charger les permissions
        $permissions = require __DIR__ . '/../../config/permissions.php';

        // Récupérer les permissions du rôle de l'utilisateur
        $userPermissions = $permissions[$user->getRole()] ?? [];

        // Nettoyer la route (enlever le slash de début)
        $route = ltrim($route, '/');

        // Vérifier chaque pattern de permission
        foreach ($userPermissions as $pattern) {
            if (self::matchRoute($route, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si une route correspond à un pattern de permission
     *
     * @param string $route Route à vérifier
     * @param string $pattern Pattern de permission (ex: "postal/*" ou "postal/colis/details/*")
     * @return bool
     */
    private static function matchRoute(string $route, string $pattern): bool
    {
        // Convertir le pattern en regex
        // Remplacer * par .* pour matcher n'importe quoi
        $regex = str_replace(['*', '/'], ['.*', '\\/'], $pattern);
        $regex = '#^' . $regex . '$#';

        return preg_match($regex, $route) === 1;
    }

    /**
     * Lance une erreur 403 si l'utilisateur n'a pas accès
     *
     * @param string $route
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public static function authorize(string $route, User $user): void
    {
        if (!self::check($route, $user)) {
            http_response_code(403);
            die("Accès refusé. Vous n'avez pas les permissions pour accéder à cette page.");
        }
    }
}
