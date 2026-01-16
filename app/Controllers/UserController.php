<?php

class UserController
{
    private PDO $pdo;
    private UserRespository $repo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
        $this->repo = new UserRespository($this->pdo);
    }

    public function index(): void
    {
        $users = $this->repo->findAll();
        require __DIR__ . '/../Views/users.php';
    }

    public function create(): void
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        require __DIR__ . '/../Views/addJoueur.php';
    }

    public function store(): void
    {
        csrf_verify();

        $data = [
            'nom_user' => trim($_POST['nom_user'] ?? ''),
            'prenom_user' => trim($_POST['prenom_user'] ?? ''),
            'email_user' => trim($_POST['email_user'] ?? ''),
            'role_user' => trim($_POST['role_user'] ?? ''),
            'phone_user' => trim($_POST['phone_user'] ?? ''),
            'password_user' => $_POST['password_user'] ?? '',
            'discipline_coach' => trim($_POST['discipline_coach'] ?? ''),
            'experiences_coach' => trim($_POST['experiences_coach'] ?? ''),
            'description_coach' => trim($_POST['description_coach'] ?? ''),
        ];

        $errors = [];

        if ($data['email_user'] === '' || !filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_user'] = "Email invalide";
        }
        if ($data['role_user'] === '') {
            $errors['role_user'] = "Rôle obligatoire";
        }
        if (strlen($data['password_user']) < 6) {
            $errors['password_user'] = "Mot de passe minimum 6 caractères";
        }
        if ($data['role_user'] === 'coach' && $data['discipline_coach'] === '') {
            $errors['discipline_coach'] = "Discipline obligatoire pour un coach";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect('/users/create');
        }

        $id = $this->repo->createUser($data);

        if ($data['role_user'] === 'coach') {
            $this->repo->createCoachInfos($id, $data);
        }

        flash('success', "Utilisateur ajouté avec succès");
        redirect('/users');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect('/users');
        }

        $user = $this->repo->findById($id);
        if (!$user) {
            redirect('/users');
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        require __DIR__ . '/../Views/editUser.php';
    }

    public function update(): void
    {
        csrf_verify();

        $id = (int)($_POST['id_user'] ?? 0);
        if ($id <= 0) {
            redirect('/users');
        }

        $data = [
            'nom_user' => trim($_POST['nom_user'] ?? ''),
            'prenom_user' => trim($_POST['prenom_user'] ?? ''),
            'email_user' => trim($_POST['email_user'] ?? ''),
            'role_user' => trim($_POST['role_user'] ?? ''),
            'phone_user' => trim($_POST['phone_user'] ?? ''),
        ];

        $errors = [];

        if ($data['email_user'] === '' || !filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_user'] = "Email invalide";
        }
        if ($data['role_user'] === '') {
            $errors['role_user'] = "Rôle obligatoire";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect('/users/edit?id=' . $id);
        }

        $this->repo->updateUser($id, $data);

        flash('success', "Utilisateur modifié avec succès");
        redirect('/users');
    }

    public function delete(): void
    {
        csrf_verify();

        $id = (int)($_POST['id_user'] ?? 0);
        if ($id > 0) {
            $this->repo->deleteUser($id);
            flash('success', "Utilisateur supprimé");
        }

        redirect('/users');
    }
}
