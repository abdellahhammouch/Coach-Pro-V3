<?php
require_once __DIR__ . '/BaseRepository.php';

class ReservationRepository extends BaseRepository
{
    public function create(int $seanceId, int $sportifId): bool
    {
        $sql = "INSERT INTO reservations (seance_id, sportif_id, statut_reservation)
                VALUES (:seance, :sportif, 'active')";
        $st = $this->db->prepare($sql);
        return $st->execute(['seance' => $seanceId, 'sportif' => $sportifId]);
    }

    public function getByCoach(int $coachId): array
    {
        $sql = "SELECT r.id_reservation, r.statut_reservation,
                       s.id_seance, s.date_seance, s.heure_seance, s.duree_senace, s.statut_seance,
                       u.nom_user AS sportif_nom, u.prenom_user AS sportif_prenom
                FROM reservations r
                JOIN seances s ON s.id_seance = r.seance_id
                JOIN users u ON u.id_user = r.sportif_id
                WHERE s.coach_id = :id
                ORDER BY s.date_seance DESC, s.heure_seance DESC";
        $st = $this->db->prepare($sql);
        $st->execute(['id' => $coachId]);
        return $st->fetchAll();
    }

    public function getActiveBySportif(int $sportifId): array
    {
        $sql = "SELECT r.id_reservation,
                       s.id_seance, s.date_seance, s.heure_seance, s.duree_senace, s.statut_seance,
                       u.nom_user AS coach_nom, u.prenom_user AS coach_prenom
                FROM reservations r
                JOIN seances s ON s.id_seance = r.seance_id
                JOIN users u ON u.id_user = s.coach_id
                WHERE r.sportif_id = :id AND r.statut_reservation = 'active'
                ORDER BY s.date_seance DESC, s.heure_seance DESC";
        $st = $this->db->prepare($sql);
        $st->execute(['id' => $sportifId]);
        return $st->fetchAll();
    }

    public function findActiveReservation(int $reservationId, int $sportifId): ?array
    {
        $sql = "SELECT id_reservation, seance_id
                FROM reservations
                WHERE id_reservation = :rid
                  AND sportif_id = :sid
                  AND statut_reservation = 'active'
                LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute(['rid' => $reservationId, 'sid' => $sportifId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function cancel(int $reservationId): bool
    {
        $st = $this->db->prepare("UPDATE reservations SET statut_reservation = 'annulee' WHERE id_reservation = :id");
        return $st->execute(['id' => $reservationId]);
    }
}
