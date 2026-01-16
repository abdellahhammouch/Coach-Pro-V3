<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../Repositories/CoachRepository.php';

class CoachController
{
    private PDO $pdo;
    private CoachRepository $coachRepo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
        $this->coachRepo = new CoachRepository($this->pdo);
    }

    public function updateProfile(): void
    {
        require_login();
        require_role('coach');
        csrf_verify();

        $userId = (int)$_SESSION['user_id'];

        $phone = trim($_POST['phone_user'] ?? '');
        $discipline = trim($_POST['discipline_coach'] ?? '');
        $experience = (int)($_POST['experience_coach'] ?? 0);
        $description = trim($_POST['description_coach'] ?? '');

        if ($discipline === '' || $experience < 0) {
            flash('error', "Veuillez remplir correctement le profil.");
            redirect('/dashboard/coach');
        }

        $ok = $this->coachRepo->updateProfile($userId, $phone, $discipline, $experience, $description);
        flash($ok ? 'success' : 'error', $ok ? "Profil mis à jour." : "Erreur lors de la mise à jour.");
        redirect('/dashboard/coach');
    }
}
