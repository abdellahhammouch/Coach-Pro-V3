<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/helpers.php';

require_once __DIR__ . '/../Repositories/CoachRepository.php';
require_once __DIR__ . '/../Repositories/SeanceRepository.php';
require_once __DIR__ . '/../Repositories/ReservationRepository.php';

class DashboardController
{
    private PDO $pdo;
    private CoachRepository $coachRepo;
    private SeanceRepository $seanceRepo;
    private ReservationRepository $reservationRepo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
        $this->coachRepo = new CoachRepository($this->pdo);
        $this->seanceRepo = new SeanceRepository($this->pdo);
        $this->reservationRepo = new ReservationRepository($this->pdo);
    }

    // /dashboard ou /
    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            require __DIR__ . '/../Views/home.php';
            return;
        }

        $role = $_SESSION['role'] ?? '';
        if ($role === 'coach') {
            redirect('/dashboard/coach');
        }
        if ($role === 'sportif') {
            redirect('/dashboard/sportif');
        }

        redirect('/login');
    }

    public function coach(): void
    {
        require_login();
        require_role('coach');

        $userId = (int)$_SESSION['user_id'];

        // Profil coach (join users + coachs)
        $coach = $this->coachRepo->findByUserId($userId);
        if (!$coach) {
            flash('error', "Profil coach introuvable.");
            redirect('/logout');
        }

        $coachId = (int)$coach['id_coach'];

        // Stats & data
        $total_seances = $this->seanceRepo->countByCoach($coachId);
        $seances_disponibles = $this->seanceRepo->countByCoachAndStatus($coachId, 'disponible');
        $seances_reservees = $this->seanceRepo->countByCoachAndStatus($coachId, 'reservee');

        $mySeances = $this->seanceRepo->getByCoach($coachId);

        $recentReservations = $this->reservationRepo->recentByCoach($coachId, 3);
        $allReservations = $this->reservationRepo->allByCoach($coachId);

        // Ces variables doivent matcher ce que ta view utilise
        require __DIR__ . '/../Views/dashboard/dashboard_coach.php';
    }

    public function sportif(): void
    {
        require_login();
        require_role('sportif');

        $userId = (int)$_SESSION['user_id'];

        // id sportif
        $stmt = $this->pdo->prepare("SELECT id_sportif FROM sportifs WHERE id_user = ?");
        $stmt->execute([$userId]);
        $sportifId = (int)$stmt->fetchColumn();

        if ($sportifId <= 0) {
            flash('error', "Profil sportif introuvable.");
            redirect('/logout');
        }

        // Stats
        $coaches_totaux = $this->coachRepo->countAll();
        $seances_disponibles = $this->seanceRepo->countAllAvailable();
        $seances_reservees = $this->reservationRepo->countBySportifAndStatus($sportifId, 'active');
        $seances_terminees = $this->reservationRepo->countBySportifAndStatus($sportifId, 'annulee');

        // Lists
        $recent_list = $this->reservationRepo->recentBySportif($sportifId, 3);
        $all_list = $this->reservationRepo->allBySportif($sportifId);

        // Liste des coachs
        $coaches = $this->coachRepo->allPublic();

        // disponibilités par coach (simple & clair)
        $coachDisponibilites = [];
        foreach ($coaches as $c) {
            $cid = (int)$c['id_coach'];
            $coachDisponibilites[$cid] = $this->seanceRepo->getAvailableByCoach($cid);
        }

        require __DIR__ . '/../Views/dashboard/dashboard_sportif.php';
    }
}
