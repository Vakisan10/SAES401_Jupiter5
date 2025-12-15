<?php

namespace SAE\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, string $controller, string $method): void
    {
        $this->routes['GET'][$pattern] = [$controller, $method];
    }

    public function post(string $pattern, string $controller, string $method): void
    {
        $this->routes['POST'][$pattern] = [$controller, $method];
    }

    /**
     * Dispatch une requête vers le bon contrôleur
     *
     * @return array [controllerClass, methodName, params]
     * @throws \Exception si route non trouvée
     */
    public function dispatch(string $method, string $uri): array
    {
        if (!isset($this->routes[$method])) {
            throw new \Exception("Method {$method} not supported", 405);
        }

        // Nettoyer l'URI (enlever trailing slash)
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        // Chercher une correspondance exacte d'abord
        if (isset($this->routes[$method][$uri])) {
            [$controller, $methodName] = $this->routes[$method][$uri];
            return [$controller, $methodName, []];
        }

        // Sinon, chercher un pattern avec paramètres
        foreach ($this->routes[$method] as $pattern => $handler) {
            $params = $this->matchPattern($pattern, $uri);
            if ($params !== false) {
                [$controller, $methodName] = $handler;
                return [$controller, $methodName, $params];
            }
        }

        throw new \Exception("Route not found: {$method} {$uri}", 404);
    }

    /**
     * Vérifie si un URI correspond à un pattern et extrait les paramètres
     *
     * @param string $pattern Pattern de route (ex: "/postal/colis/details/:id")
     * @param string $uri URI à matcher (ex: "/postal/colis/details/123")
     * @return array|false Tableau de paramètres ou false si pas de match
     */
    private function matchPattern(string $pattern, string $uri)
    {
        // Convertir le pattern en regex
        // :id devient ([0-9]+)
        // :slug devient ([a-z0-9\-]+)
        // * devient (.*)
        $regex = preg_replace_callback('/:([a-z]+)/', function ($matches) {
            $param = $matches[1];
            if ($param === 'id') {
                return '([0-9]+)';
            }
            return '([a-zA-Z0-9\-_]+)';
        }, $pattern);

        $regex = str_replace('*', '(.*)', $regex);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            // Enlever le match complet
            array_shift($matches);
            return $matches;
        }

        return false;
    }

    /**
     * Retourne toutes les routes enregistrées (pour debug)
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
