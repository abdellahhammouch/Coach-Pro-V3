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
    public function showRegister(): void
    {
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [
            'role' => '',
            'prenom' => '',
            'nom' => '',
            'email' => '',
            'phone' => '',
            'disciplines' => '',
            'experience' => '',
            'biographie' => '',
            'prix' => '',
        ];
        $success = flash('success');

        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void
    {
        csrf_verify();

        $role = trim((string)($_POST['role'] ?? ''));
        $prenom = trim((string)($_POST['prenom'] ?? ''));
        $nom = trim((string)($_POST['nom'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirmPassword'] ?? '');

        // Coach fields (mêmes names que la V2)
        $disciplines = trim((string)($_POST['disciplines'] ?? '')); // hidden input rempli par JS
        $experience = ($_POST['experience'] ?? '');
        $biographie = trim((string)($_POST['biographie'] ?? ''));
        $prix = trim((string)($_POST['prix'] ?? '')); // on le garde pour le design, on l’ignore côté DB si colonne n’existe pas

        $errors = [];

        if (!in_array($role, ['coach', 'sportif'], true)) $errors[] = "Rôle invalide (coach ou sportif).";
        if ($prenom === '') $errors[] = "Prénom obligatoire.";
        if ($nom === '') $errors[] = "Nom obligatoire.";
        if ($email === '') $errors[] = "Email obligatoire.";
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";

        if ($phone === '') $errors[] = "Téléphone obligatoire.";
        if ($password === '') $errors[] = "Mot de passe obligatoire.";
        if ($password !== $confirmPassword) $errors[] = "Les mots de passe ne correspondent pas.";
        if (strlen($password) < 6) $errors[] = "Mot de passe minimum 6 caractères.";

        // si coach -> disciplines + expérience obligatoires (comme la V2)
        if ($role === 'coach') {
            if ($disciplines === '') $errors[] = "Veuillez sélectionner au moins une discipline.";
            if ($experience === '' || !is_numeric($experience)) $errors[] = "Années d'expérience invalide.";
        } else {
            // si sportif -> on ignore les champs coach
            $disciplines = null;
            $experience = null;
            $biographie = null;
        }

        $old = [
            'role' => $role,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'phone' => $phone,
            'disciplines' => (string)($disciplines ?? ''),
            'experience' => (string)($experience ?? ''),
            'biographie' => (string)($biographie ?? ''),
            'prix' => $prix,
        ];

        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', $old);
            redirect('/register');
        }

        // Email déjà utilisé ?
        if ($this->repo->findByEmail($email)) {
            flash('errors', ["Cet email est déjà utilisé."]);
            flash('old', $old);
            redirect('/register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();

            $userId = $this->repo->createUser([
                'nom_user' => $nom,
                'prenom_user' => $prenom,
                'email_user' => $email,
                'role_user' => $role,
                'phone_user' => $phone,
                'password_user' => $hash,
            ]);

            if ($role === 'coach') {
                $this->repo->createCoach($userId, [
                    'discipline_coach' => $disciplines,
                    'experiences_coach' => (int)$experience,
                    'description_coach' => $biographie,
                ]);
            } else {
                $this->repo->createSportif($userId);
            }

            $pdo->commit();

            flash('success', "Inscription réussie. Connecte-toi maintenant.");
            redirect('/login');

        } catch (Throwable $e) {
            if (Database::getConnection()->inTransaction()) {
                Database::getConnection()->rollBack();
            }
            flash('errors', ["Erreur base de données : " . $e->getMessage()]);
            flash('old', $old);
            redirect('/register');
        }
    }

}
