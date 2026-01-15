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
        csrf_verify();

        $nom = trim((string)($_POST['nom_user'] ?? ''));
        $prenom = trim((string)($_POST['prenom_user'] ?? ''));
        $email = trim((string)($_POST['email_user'] ?? ''));
        $role = trim((string)($_POST['role_user'] ?? ''));
        $phone = trim((string)($_POST['phone_user'] ?? ''));
        $password = (string)($_POST['password_user'] ?? '');

        $discipline = trim((string)($_POST['discipline_coach'] ?? ''));
        $experience = trim((string)($_POST['experiences_coach'] ?? ''));
        $description = trim((string)($_POST['description_coach'] ?? ''));

        $errors = [];

        // Email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_user'] = "Email invalide.";
        }

        // Role
        if (!in_array($role, ['coach', 'sportif'], true)) {
            $errors['role_user'] = "Rôle invalide.";
        }

        // Password
        if (strlen($password) < 6) {
            $errors['password_user'] = "Mot de passe trop court (min 6).";
        }

        // Phone (optionnel mais propre)
        if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) {
            $errors['phone_user'] = "Téléphone invalide (10 chiffres).";
        }

        // Coach required fields
        if ($role === 'coach' && $discipline === '') {
            $errors['discipline_coach'] = "Discipline obligatoire pour un coach.";
        }

        // Experience
        if ($experience !== '' && (!ctype_digit($experience) || (int)$experience < 0)) {
            $errors['experiences_coach'] = "Expérience invalide.";
        }

        // Unique email
        if ($email !== '' && $this->repo->findByEmail($email)) {
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

        flash('success', 'Utilisateur ajouté avec succès ✅');
        redirect('/users');
    }
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Bad Request";
            return;
        }

        $user = $this->repo->findById($id);
        if (!$user) {
            http_response_code(404);
            echo "User not found";
            return;
        }

        $errors = flash('errors') ?? [];
        $old = flash('old') ?? [];

        require __DIR__ . '/../Views/editUser.php';
    }

    public function update(): void
    {
        csrf_verify();

        $id = (int)($_POST['id_user'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Bad Request";
            return;
        }

        $nom = trim((string)($_POST['nom_user'] ?? ''));
        $prenom = trim((string)($_POST['prenom_user'] ?? ''));
        $email = trim((string)($_POST['email_user'] ?? ''));
        $role = trim((string)($_POST['role_user'] ?? ''));
        $phone = trim((string)($_POST['phone_user'] ?? ''));

        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_user'] = "Email invalide.";
        }

        if (!in_array($role, ['coach', 'sportif'], true)) {
            $errors['role_user'] = "Rôle invalide.";
        }

        if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) {
            $errors['phone_user'] = "Téléphone invalide (10 chiffres).";
        }

        // email unique (sauf lui-même)
        $existing = $this->repo->findByEmail($email);
        if ($existing && (int)$existing['id_user'] !== $id) {
            $errors['email_user'] = "Cet email existe déjà.";
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect('/users/edit?id=' . $id);
        }

        $this->repo->updateUser($id, [
            'nom_user' => $nom,
            'prenom_user' => $prenom,
            'email_user' => $email,
            'role_user' => $role,
            'phone_user' => $phone,
        ]);

        flash('success', 'Utilisateur modifié ✅');
        redirect('/users');
    }

    public function delete(): void
    {
        csrf_verify();

        $id = (int)($_POST['id_user'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Bad Request";
            return;
        }

        $this->repo->deleteUser($id);

        flash('success', 'Utilisateur supprimé ✅');
        redirect('/users');
    }
}

?>