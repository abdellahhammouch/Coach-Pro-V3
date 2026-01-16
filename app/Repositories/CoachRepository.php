<?php
require_once __DIR__ . '/BaseRepository.php';

class CoachRepository extends BaseRepository
{
    public function findCoachProfile(int $coachId): ?array
    {
        $sql = "SELECT u.id_user, u.nom_user, u.prenom_user, u.email_user,
                       c.discipline_coach, c.experiences_coach, c.description_coach
                FROM users u
                JOIN coachs c ON c.id_user = u.id_user
                WHERE u.id_user = :id
                LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute(['id' => $coachId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function listCoaches(): array
    {
        $sql = "SELECT u.id_user, u.nom_user, u.prenom_user,
                       c.discipline_coach, c.experiences_coach, c.description_coach
                FROM users u
                JOIN coachs c ON c.id_user = u.id_user
                ORDER BY u.nom_user ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function countTotalSeances(int $coachId): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = :id");
        $st->execute(['id' => $coachId]);
        return (int)$st->fetchColumn();
    }

    public function countDispoSeances(int $coachId): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = :id AND statut_seance = 'disponible'");
        $st->execute(['id' => $coachId]);
        return (int)$st->fetchColumn();
    }

    public function countReserveeSeances(int $coachId): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM seances WHERE coach_id = :id AND statut_seance = 'reservee'");
        $st->execute(['id' => $coachId]);
        return (int)$st->fetchColumn();
    }

    public function countDistinctSportifs(int $coachId): int
    {
        $sql = "SELECT COUNT(DISTINCT r.sportif_id)
                FROM reservations r
                JOIN seances s ON s.id_seance = r.seance_id
                WHERE s.coach_id = :id AND r.statut_reservation = 'active'";
        $st = $this->db->prepare($sql);
        $st->execute(['id' => $coachId]);
        return (int)$st->fetchColumn();
    }
    public function findByUserId(int $userId): ?array
    {
        $sql = "SELECT c.id_coach, c.discipline_coach, c.experience_coach, c.description_coach,
                    u.id_user, u.nom_user, u.prenom_user, u.email_user, u.phone_user, u.role_user
                FROM coachs c
                JOIN users u ON u.id_user = c.id_user
                WHERE u.id_user = :uid
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM coachs");
        return (int)$stmt->fetchColumn();
    }

    public function allPublic(): array
    {
        $sql = "SELECT c.id_coach, c.discipline_coach, c.experience_coach, c.description_coach,
                    u.nom_user, u.prenom_user, u.email_user, u.phone_user
                FROM coachs c
                JOIN users u ON u.id_user = c.id_user
                ORDER BY u.nom_user ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function updateProfile(int $userId, string $phone, string $discipline, int $experience, string $description): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt1 = $this->pdo->prepare("UPDATE users SET phone_user = :phone WHERE id_user = :uid");
            $stmt1->execute(['phone' => $phone, 'uid' => $userId]);

            $stmt2 = $this->pdo->prepare("
                UPDATE coachs
                SET discipline_coach = :d, experience_coach = :e, description_coach = :desc
                WHERE id_user = :uid
            ");
            $stmt2->execute([
                'd' => $discipline,
                'e' => $experience,
                'desc' => $description,
                'uid' => $userId
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

}
