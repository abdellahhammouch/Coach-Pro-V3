<?php
require_once __DIR__ . '/BaseRepository.php';

class SeanceRepository extends BaseRepository
{
    public function create(int $coachId, string $date, string $heure, int $duree): bool
    {
        $sql = "INSERT INTO seances (coach_id, date_seance, heure_seance, duree_senace, statut_seance)
                VALUES (:coach, :date, :heure, :duree, 'disponible')";
        $st = $this->db->prepare($sql);
        return $st->execute([
            'coach' => $coachId,
            'date'  => $date,
            'heure' => $heure,
            'duree' => $duree,
        ]);
    }

    public function getByCoach(int $coachId): array
    {
        $st = $this->db->prepare("SELECT * FROM seances WHERE coach_id = :id ORDER BY date_seance DESC, heure_seance DESC");
        $st->execute(['id' => $coachId]);
        return $st->fetchAll();
    }

    public function getById(int $seanceId): ?array
    {
        $st = $this->db->prepare("SELECT * FROM seances WHERE id_seance = :id LIMIT 1");
        $st->execute(['id' => $seanceId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function deleteIfDisponible(int $seanceId, int $coachId): bool
    {
        $sql = "DELETE FROM seances
                WHERE id_seance = :id AND coach_id = :coach AND statut_seance = 'disponible'";
        $st = $this->db->prepare($sql);
        return $st->execute(['id' => $seanceId, 'coach' => $coachId]);
    }

    public function markReserved(int $seanceId): bool
    {
        $st = $this->db->prepare("UPDATE seances SET statut_seance = 'reservee' WHERE id_seance = :id");
        return $st->execute(['id' => $seanceId]);
    }

    public function markDisponible(int $seanceId): bool
    {
        $st = $this->db->prepare("UPDATE seances SET statut_seance = 'disponible' WHERE id_seance = :id");
        return $st->execute(['id' => $seanceId]);
    }

    public function getAvailableByCoach(int $coachId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM seances WHERE coach_id = ? AND statut_seance = 'disponible' ORDER BY date_seance ASC, heure_seance ASC");
        $stmt->execute([$coachId]);
        return $stmt->fetchAll();
    }

    public function countAllAvailable(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM seances WHERE statut_seance = 'disponible'");
        return (int)$stmt->fetchColumn();
    }

    public function countByCoach(int $coachId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = ?");
        $stmt->execute([$coachId]);
        return (int)$stmt->fetchColumn();
    }

    public function countByCoachAndStatus(int $coachId, string $status): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = ? AND statut_seance = ?");
        $stmt->execute([$coachId, $status]);
        return (int)$stmt->fetchColumn();
    }

}
