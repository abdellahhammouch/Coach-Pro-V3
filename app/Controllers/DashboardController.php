<?php

class DashboardController
{
    public function index(): void
    {
        require_login();

        $role = $_SESSION['role'] ?? '';
        if ($role === 'coach') redirect('/dashboard/coach');
        if ($role === 'sportif') redirect('/dashboard/sportif');

        redirect('/login');
    }

    public function coach(): void
    {
        require_role('coach');

        $pdo = Database::getConnection();
        $coach_id = (int) $_SESSION['user_id'];

        // Profil coach
        $stmtCoach = $pdo->prepare("
            SELECT u.id_user, u.nom_user, u.prenom_user, u.email_user,
                   c.discipline_coach, c.experiences_coach, c.description_coach
            FROM users u
            JOIN coachs c ON c.id_user = u.id_user
            WHERE u.id_user = ?
            LIMIT 1
        ");
        $stmtCoach->execute([$coach_id]);
        $coach = $stmtCoach->fetch();

        if (!$coach) {
            echo "Profil coach introuvable dans la table coachs.";
            exit;
        }

        $coach_nom        = $coach["nom_user"];
        $coach_prenom     = $coach["prenom_user"];
        $coach_email      = $coach["email_user"];
        $coach_discipline = $coach["discipline_coach"];
        $coach_experience = $coach["experiences_coach"];
        $coach_desc       = $coach["description_coach"];

        // Flash messages (venant des actions POST)
        $errors = flash('errors') ?? [];
        if ($msg = flash('error')) $errors[] = $msg;
        $success = flash('success');

        // Stats séances
        $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = ?");
        $stmtTotal->execute([$coach_id]);
        $total_seances = (int)$stmtTotal->fetchColumn();

        $stmtDispo = $pdo->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = ? AND statut_seance = 'disponible'");
        $stmtDispo->execute([$coach_id]);
        $dispo_seances = (int)$stmtDispo->fetchColumn();

        $stmtRes = $pdo->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = ? AND statut_seance = 'reservee'");
        $stmtRes->execute([$coach_id]);
        $reservee_seances = (int)$stmtRes->fetchColumn();

        // Nb sportifs distincts (réservations actives)
        $stmtAth = $pdo->prepare("
            SELECT COUNT(DISTINCT r.sportif_id)
            FROM reservations r
            JOIN seances s ON s.id_seance = r.seance_id
            WHERE s.coach_id = ? AND r.statut_reservation = 'active'
        ");
        $stmtAth->execute([$coach_id]);
        $athletes_count = (int)$stmtAth->fetchColumn();

        // Mes séances
        $seanceModel = new Seance($pdo);
        $mySeances = $seanceModel->getByCoach($coach_id);

        // Réservations reçues
        $stmtReservations = $pdo->prepare("
            SELECT r.id_reservation, r.statut_reservation,
                   s.id_seance, s.date_seance, s.heure_seance, s.duree_senace, s.statut_seance,
                   u.nom_user AS sportif_nom, u.prenom_user AS sportif_prenom
            FROM reservations r
            JOIN seances s ON s.id_seance = r.seance_id
            JOIN users u ON u.id_user = r.sportif_id
            WHERE s.coach_id = ?
            ORDER BY s.date_seance DESC, s.heure_seance DESC
        ");
        $stmtReservations->execute([$coach_id]);
        $allReservations = $stmtReservations->fetchAll();
        $recentReservations = array_slice($allReservations, 0, 5);

        require __DIR__ . '/../Views/dashboard/dashboard_coach.php';
    }

    public function sportif(): void
    {
        require_role('sportif');

        $pdo = Database::getConnection();
        $sportifId = (int) $_SESSION['user_id'];

        $errors = flash('errors') ?? [];
        if ($msg = flash('error')) $errors[] = $msg;
        $success = flash('success');

        // Infos sportif
        $stmt = $pdo->prepare("
            SELECT u.*
            FROM users u
            INNER JOIN sportifs s ON s.id_user = u.id_user
            WHERE u.id_user = ?
            LIMIT 1
        ");
        $stmt->execute([$sportifId]);
        $sportif = $stmt->fetch();

        if (!$sportif) {
            redirect('/logout');
        }

        $sportif_nom    = $sportif['nom_user'];
        $sportif_prenom = $sportif['prenom_user'];
        $sportif_email  = $sportif['email_user'];
        $sportif_phone  = $sportif['phone_user'];

        // Stats
        $qTotal = $pdo->query("SELECT COUNT(*) AS total FROM coachs");
        $coaches_totaux = (int)($qTotal->fetch()['total'] ?? 0);

        $qAvail = $pdo->query("SELECT COUNT(*) AS total FROM seances WHERE statut_seance = 'disponible'");
        $seances_disponibles = (int)($qAvail->fetch()['total'] ?? 0);

        $qActive = $pdo->prepare("SELECT COUNT(*) AS total FROM reservations WHERE sportif_id = ? AND statut_reservation = 'active'");
        $qActive->execute([$sportifId]);
        $seances_reservees = (int)($qActive->fetch()['total'] ?? 0);

        $qCancel = $pdo->prepare("SELECT COUNT(*) AS total FROM reservations WHERE sportif_id = ? AND statut_reservation = 'annulee'");
        $qCancel->execute([$sportifId]);
        $seances_terminees = (int)($qCancel->fetch()['total'] ?? 0);

        // Réservations (récentes + toutes)
        $recent = $pdo->prepare("
            SELECT r.id_reservation, r.statut_reservation, s.id_seance,
                   s.date_seance, s.heure_seance, s.duree_senace,
                   u.nom_user AS coach_nom, u.prenom_user AS coach_prenom,
                   c.discipline_coach
            FROM reservations r
            INNER JOIN seances s ON s.id_seance = r.seance_id
            INNER JOIN coachs c ON c.id_user = s.coach_id
            INNER JOIN users u ON u.id_user = c.id_user
            WHERE r.sportif_id = ?
            ORDER BY s.date_seance DESC, s.heure_seance DESC
            LIMIT 3
        ");
        $recent->execute([$sportifId]);
        $recent_list = $recent->fetchAll();

        $all = $pdo->prepare("
            SELECT r.id_reservation, r.statut_reservation,
                   s.id_seance, s.date_seance, s.heure_seance, s.duree_senace,
                   u.nom_user AS coach_nom, u.prenom_user AS coach_prenom,
                   c.discipline_coach
            FROM reservations r
            INNER JOIN seances s ON s.id_seance = r.seance_id
            INNER JOIN coachs c ON c.id_user = s.coach_id
            INNER JOIN users u ON u.id_user = c.id_user
            WHERE r.sportif_id = ?
            ORDER BY s.date_seance DESC, s.heure_seance DESC
        ");
        $all->execute([$sportifId]);
        $all_list = $all->fetchAll();

        // Liste coachs
        $coachesStmt = $pdo->query("
            SELECT c.id_user AS id_coach,
                   u.nom_user AS coach_nom, u.prenom_user AS coach_prenom,
                   c.discipline_coach, c.experiences_coach, c.description_coach,
                   (SELECT COUNT(*) FROM seances s
                    WHERE s.coach_id = c.id_user AND s.statut_seance = 'disponible') AS seances_dispo
            FROM coachs c
            INNER JOIN users u ON u.id_user = c.id_user
            WHERE u.role_user = 'coach'
            ORDER BY u.nom_user ASC, u.prenom_user ASC
        ");
        $coaches = $coachesStmt->fetchAll();
        $seanceModel = new Seance($pdo);

        foreach ($coaches as &$coach) {
            $coachId = (int)$coach['id_coach'];

            $stmtTs = $pdo->prepare("
                SELECT COUNT(DISTINCT r.sportif_id)
                FROM reservations r
                JOIN seances s ON s.id_seance = r.seance_id
                WHERE s.coach_id = ? AND r.statut_reservation = 'active'
            ");
            $stmtTs->execute([$coachId]);
            $coach['totalSportifs'] = (int)$stmtTs->fetchColumn();

            $coach['dispos'] = $seanceModel->getAvailableByCoach($coachId);
        }
        unset($coach);

        // Mes coachs (réservations actives)
        $mycoaches = $pdo->prepare("
            SELECT c.id_user AS id_coach,
                   u.nom_user AS coach_nom, u.prenom_user AS coach_prenom,
                   c.discipline_coach,
                   COUNT(r.id_reservation) AS total_seances,
                   MIN(s.date_seance) AS since_date
            FROM reservations r
            INNER JOIN seances s ON s.id_seance = r.seance_id
            INNER JOIN coachs c ON c.id_user = s.coach_id
            INNER JOIN users u ON u.id_user = c.id_user
            WHERE r.sportif_id = ?
              AND r.statut_reservation = 'active'
            GROUP BY c.id_user, u.nom_user, u.prenom_user, c.discipline_coach
            ORDER BY since_date DESC
        ");
        $mycoaches->execute([$sportifId]);
        $mycoaches_list = $mycoaches->fetchAll();

        require __DIR__ . '/../Views/dashboard/dashboard_sportif.php';
    }
}
