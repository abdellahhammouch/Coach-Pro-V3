<?php

class AuthController
{
    private UserRespository $repo;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->repo = new UserRespository($pdo);
    }

    public function home(): void
    {
        // Pour le moment: si connecté => /users (on fera les dashboards après)
        if (is_logged_in()) {
            redirect('/users');
        }
        redirect('/login');
    }

    public function showLogin(): void
    {
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? ['email' => '', 'role' => ''];
        $success = flash('success');

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        csrf_verify();

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $role = trim((string)($_POST['role'] ?? ''));

        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if ($password === '') {
            $errors[] = "Mot de passe obligatoire.";
        }
        if (!in_array($role, ['coach', 'sportif'], true)) {
            $errors[] = "Veuillez sélectionner le type de compte.";
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', ['email' => $email, 'role' => $role]);
            redirect('/login');
        }

        $user = $this->repo->findByEmail($email);

        if (!$user || !password_verify($password, (string)$user['password_user'])) {
            flash('errors', ["Email ou mot de passe incorrect."]);
            flash('old', ['email' => $email, 'role' => $role]);
            redirect('/login');
        }

        if (($user['role_user'] ?? '') !== $role) {
            flash('errors', ["Type de compte incorrect (coach/sportif)."]);
            flash('old', ['email' => $email, 'role' => $role]);
            redirect('/login');
        }

        // Session
        $_SESSION['user_id'] = (int)$user['id_user'];
        $_SESSION['nom'] = $user['nom_user'] ?? '';
        $_SESSION['prenom'] = $user['prenom_user'] ?? '';
        $_SESSION['email'] = $user['email_user'] ?? '';
        $_SESSION['role'] = $user['role_user'] ?? '';

        redirect('/');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        redirect('/login');
    }
}
