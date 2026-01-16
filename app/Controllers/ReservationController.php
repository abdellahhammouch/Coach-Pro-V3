<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/helpers.php';

require_once __DIR__ . '/../Repositories/SeanceRepository.php';
require_once __DIR__ . '/../Repositories/ReservationRepository.php';

class ReservationController
{
    private PDO $pdo;
    private SeanceRepository $seanceRepo;
    private ReservationRepository $resRepo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
        $this->seanceRepo = new SeanceRepository($this->pdo);
        $this->resRepo = new ReservationRepository($this->pdo);
    }

    public function reserve(): void
    {
        require_role('sportif');
        csrf_verify();

        $sportif_id = (int)$_SESSION['user_id'];
        $seanceId = (int)($_POST['seance_id'] ?? 0);

        if ($seanceId <= 0) {
            flash('errors', ["Séance invalide."]);
            redirect('/dashboard/sportif');
        }

        try {
            $this->pdo->beginTransaction();

            $seance = $this->seanceRepo->getById($seanceId);
            if (!$seance) throw new Exception("Séance introuvable.");
            if (($seance['statut_seance'] ?? '') !== 'disponible') throw new Exception("Cette séance est déjà réservée.");

            $this->resRepo->create($seanceId, $sportif_id);
            $this->seanceRepo->markReserved($seanceId);

            $this->pdo->commit();
            flash('success', "Réservation effectuée avec succès ✅");
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            flash('errors', [$e->getMessage()]);
        }

        redirect('/dashboard/sportif');
    }

    public function cancel(): void
    {
        require_role('sportif');
        csrf_verify();

        $sportif_id = (int)$_SESSION['user_id'];
        $reservation_id = (int)($_POST['reservation_id'] ?? 0);

        if ($reservation_id <= 0) {
            flash('errors', ["Réservation invalide."]);
            redirect('/dashboard/sportif');
        }

        try {
            $this->pdo->beginTransaction();

            $res = $this->resRepo->findActiveReservation($reservation_id, $sportif_id);
            if (!$res) throw new Exception("Impossible d'annuler (introuvable ou déjà annulée).");

            $seance_id = (int)$res['seance_id'];

            $this->resRepo->cancel($reservation_id);
            $this->seanceRepo->markDisponible($seance_id);

            $this->pdo->commit();
            flash('success', "Réservation annulée avec succès.");
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            flash('errors', ["Erreur : " . $e->getMessage()]);
        }

        redirect('/dashboard/sportif');
    }
}
        