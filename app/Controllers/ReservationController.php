<?php

class ReservationController
{
    public function reserve(): void
    {
        require_role('sportif');
        csrf_verify();

        $pdo = Database::getConnection();
        $sportifId = (int) $_SESSION['user_id'];
        $seanceId  = (int)($_POST['seance_id'] ?? 0);

        if ($seanceId <= 0) {
            flash('error', "Séance invalide.");
            redirect('/dashboard/sportif');
        }

        $seanceModel = new Seance($pdo);
        $resModel    = new Reservation($pdo);

        try {
            $pdo->beginTransaction();

            $seance = $seanceModel->getById($seanceId);

            if (!$seance) throw new Exception("Séance introuvable.");
            if (($seance['statut_seance'] ?? '') !== 'disponible') throw new Exception("Cette séance est déjà réservée.");

            $resModel->create($seanceId, $sportifId);
            $seanceModel->markReserved($seanceId);

            $pdo->commit();
            flash('success', "Réservation effectuée avec succès ✅");
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            flash('error', $e->getMessage());
        }

        redirect('/dashboard/sportif');
    }

    public function cancel(): void
    {
        require_role('sportif');
        csrf_verify();

        $pdo = Database::getConnection();
        $sportifId = (int) $_SESSION['user_id'];
        $reservation_id = (int)($_POST['reservation_id'] ?? 0);

        if ($reservation_id <= 0) {
            flash('error', "Réservation invalide.");
            redirect('/dashboard/sportif');
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT id_reservation, seance_id
                FROM reservations
                WHERE id_reservation = ? AND sportif_id = ? AND statut_reservation = 'active'
                LIMIT 1
            ");
            $stmt->execute([$reservation_id, $sportifId]);
            $res = $stmt->fetch();

            if (!$res) throw new Exception("Impossible d'annuler (réservation introuvable ou déjà annulée).");

            $seance_id = (int)$res['seance_id'];

            $upRes = $pdo->prepare("UPDATE reservations SET statut_reservation = 'annulee' WHERE id_reservation = ?");
            $upRes->execute([$reservation_id]);

            $upSeance = $pdo->prepare("UPDATE seances SET statut_seance = 'disponible' WHERE id_seance = ?");
            $upSeance->execute([$seance_id]);

            $pdo->commit();
            flash('success', "Réservation annulée avec succès.");
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            flash('error', "Erreur : " . $e->getMessage());
        }

        redirect('/dashboard/sportif');
    }
}
