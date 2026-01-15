<?php

class UserController
{
    private UserRespository $repo;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->repo = new UserRespository($pdo);
    }

    public function index(): void
    {
        $users = $this->repo->findAll();
        require __DIR__ . '/../Views/users.php';
    }

    public function create(): void
    {
        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];
        require __DIR__ . '/../Views/addJoueur.php';
    }

    public function store(): void
    {
        $nom = trim($_POST['nom_user'] ?? '');
        $prenom = trim($_POST['prenom_user'] ?? '');
        $email = trim($_POST['email_user'] ?? '');
        $role = trim($_POST['role_user'] ?? '');
        $phone = trim($_POST['phone_user'] ?? '');
        $password = (string)($_POST['password_user'] ?? '');

        $discipline = trim($_POST['discipline_coach'] ?? '');
        $experience = trim($_POST['experiences_coach'] ?? '');
        $description = trim($_POST['description_coach'] ?? '');

        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_user'] = "Email invalide.";
        }

        if (!in_array($role, ['coach', 'sportif'], true)) {
            $errors['role_user'] = "Rôle invalide.";
        }

        if (strlen($password) < 6) {
            $errors['password_user'] = "Mot de passe trop court (min 6).";
        }

        if ($role === 'coach' && $discipline === '') {
            $errors['discipline_coach'] = "Discipline obligatoire pour un coach.";
        }

        if ($this->repo->findByEmail($email)) {
            $errors['email_user'] = "Cet email existe déjà.";
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect('/users/create');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

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
                'discipline_coach' => $discipline,
                'experiences_coach' => $experience !== '' ? (int)$experience : null,
                'description_coach' => $description,
            ]);
        } else {
            $this->repo->createSportif($userId);
        }

        redirect('/users');
    }
}

?>