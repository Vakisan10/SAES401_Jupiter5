<?php

class AuthMiddleware
{
    private CasAuthenticator $authenticator;

    public function __construct(CasAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Gère l'authentification CAS et retourne l'utilisateur connecté
     *
     * @return User
     */
    public function handle(): User
    {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si déjà authentifié en session, retourner l'utilisateur
        if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) {
            return $_SESSION['user'];
        }

        // MODE DEV : Bypass du CAS si APP_ENV=development
        $config = require __DIR__ . '/../../config/app.php';
        if ($config['env'] === 'development') {
            return $this->handleDevAuth();
        }

        // Sinon, forcer l'authentification CAS
        // Cette méthode redirige automatiquement vers le CAS si non authentifié
        $casUser = $this->authenticator->authenticate();

        // Charger ou créer l'utilisateur en base
        $user = UserRepository::findByUidCas($casUser->getLogin());

        if (!$user) {
            // Créer un nouvel utilisateur
            $user = $this->createUserFromCas($casUser);
        }

        // Stocker en session
        $_SESSION['user'] = $user;
        $_SESSION['authenticated'] = true;
        $_SESSION['uid_cas'] = $casUser->getLogin();

        return $user;
    }

    /**
     * Crée un nouvel utilisateur à partir des données CAS
     *
     * @param CasUser $casUser
     * @return User
     */
    private function createUserFromCas(CasUser $casUser): User
    {
        $config = require __DIR__ . '/../../config/app.php';

        // Déterminer le rôle
        $role = 'acteur'; // Rôle par défaut

        // Vérifier si l'utilisateur est admin
        if (in_array($casUser->getLogin(), $config['admin_uids'])) {
            $role = 'admin';
        }

        // Créer l'utilisateur en base
        return UserRepository::create(
            $casUser->getLogin(),
            $casUser->getAttributes(),
            $role
        );
    }

    /**
     * Gère l'authentification en mode développement (bypass CAS)
     *
     * @return User
     */
    private function handleDevAuth(): User
    {
        // Vérifier si on a un uid_cas en session dev
        if (isset($_SESSION['dev_uid_cas'])) {
            $user = UserRepository::findByUidCas($_SESSION['dev_uid_cas']);
            if ($user) {
                $_SESSION['user'] = $user;
                return $user;
            }
        }

        // Sinon, rediriger vers page de dev-login
        header('Location: /dev-login');
        exit;
    }

    /**
     * Détruit la session et déconnecte l'utilisateur
     *
     * @return void
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Déconnexion du CAS
        if (class_exists('\\phpCAS') && \phpCAS::isInitialized()) {
            \phpCAS::logout();
        }
    }
}
