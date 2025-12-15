<?php

namespace SAE\Controllers;

use SAE\Repositories\UserRepository;

class DevAuthController
{
    /**
     * Page de connexion en mode développement
     */
    public function loginForm(): void
    {
        $message = $_GET['error'] ?? null;

        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dev Login - SAE Colis</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
                .login-card {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    max-width: 400px;
                    width: 100%;
                }
                h1 {
                    margin: 0 0 10px 0;
                    color: #333;
                    font-size: 24px;
                }
                .dev-badge {
                    display: inline-block;
                    background: #ff6b6b;
                    color: white;
                    padding: 4px 12px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                p {
                    color: #666;
                    margin: 0 0 30px 0;
                    font-size: 14px;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                label {
                    display: block;
                    margin-bottom: 8px;
                    color: #333;
                    font-weight: 500;
                }
                input, select {
                    width: 100%;
                    padding: 12px;
                    border: 2px solid #e0e0e0;
                    border-radius: 6px;
                    font-size: 14px;
                    box-sizing: border-box;
                    transition: border-color 0.3s;
                }
                input:focus, select:focus {
                    outline: none;
                    border-color: #667eea;
                }
                button {
                    width: 100%;
                    padding: 12px;
                    background: #667eea;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.3s;
                }
                button:hover {
                    background: #5568d3;
                }
                .error {
                    background: #fee;
                    color: #c33;
                    padding: 12px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                    font-size: 14px;
                }
                .hint {
                    font-size: 12px;
                    color: #999;
                    margin-top: 8px;
                }
            </style>
        </head>
        <body>
            <div class="login-card">
                <h1>🔧 Connexion Développement</h1>
                <span class="dev-badge">MODE DEV</span>
                <p>Connexion sans CAS pour les tests locaux</p>

                <?php if ($message): ?>
                    <div class="error"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" action="/dev-login">
                    <div class="form-group">
                        <label for="uid_cas">UID CAS (identifiant utilisateur)</label>
                        <input
                            type="text"
                            id="uid_cas"
                            name="uid_cas"
                            placeholder="ex: p1234567"
                            required
                            value="testuser"
                        >
                        <div class="hint">Utilise un UID existant en BDD ou un nouveau sera créé</div>
                    </div>

                    <div class="form-group">
                        <label for="role">Rôle</label>
                        <select id="role" name="role">
                            <option value="acteur">Acteur (consultation)</option>
                            <option value="postal_iut" selected>Postal IUT (gestion colis)</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <div class="hint">Rôle utilisé si l'utilisateur n'existe pas en BDD</div>
                    </div>

                    <button type="submit">Se connecter</button>
                </form>

                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999;">
                    ⚠️ Cette page n'est disponible qu'en mode développement (APP_ENV=development)
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Traite la soumission du formulaire dev-login
     */
    public function loginSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dev-login');
            exit;
        }

        $uid = trim($_POST['uid_cas'] ?? '');
        $role = $_POST['role'] ?? 'acteur';

        if (empty($uid)) {
            header('Location: /dev-login?error=' . urlencode('UID CAS requis'));
            exit;
        }

        // Démarrer la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Chercher ou créer l'utilisateur
        $user = UserRepository::findByUidCas($uid);

        if (!$user) {
            // Créer un utilisateur de test
            $casAttributes = [
                'displayName' => "Test User ({$uid})",
                'mail' => "{$uid}@test.local",
                'cn' => "Test User"
            ];

            $user = UserRepository::create($uid, $casAttributes, $role);
        }

        // Stocker en session
        $_SESSION['dev_uid_cas'] = $uid;
        $_SESSION['user'] = $user;
        $_SESSION['authenticated'] = true;

        // Rediriger vers le dashboard
        header('Location: /postal/dashboard');
        exit;
    }
}
