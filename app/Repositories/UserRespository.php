<?php

require_once __DIR__ . '/BaseRepository.php';

class UserRespository extends BaseRepository
{
    public function findAll(): array
    {
        $sql = "
            SELECT u.*,
                   c.discipline_coach,
                   c.experiences_coach,
                   s.id_user AS sportif_exists
            FROM users u
            LEFT JOIN coachs c ON c.id_user = u.id_user
            LEFT JOIN sportifs s ON s.id_user = u.id_user
            ORDER BY u.id_user DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email_user = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    /** PostgreSQL: RETURNING id_user */
    public function createUser(array $data): int
    {
        $sql = "
            INSERT INTO users (nom_user, prenom_user, email_user, role_user, phone_user, password_user)
            VALUES (:nom, :prenom, :email, :role, :phone, :pass)
            RETURNING id_user
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nom' => $data['nom_user'] ?? null,
            'prenom' => $data['prenom_user'] ?? null,
            'email' => $data['email_user'],
            'role' => $data['role_user'],
            'phone' => $data['phone_user'] ?? null,
            'pass' => $data['password_user'],
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function createCoach(int $userId, array $coach): void
    {
        $sql = "
            INSERT INTO coachs (id_user, discipline_coach, experiences_coach, description_coach)
            VALUES (:id, :discipline, :exp, :descr)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $userId,
            'discipline' => $coach['discipline_coach'] ?? null,
            'exp' => $coach['experiences_coach'] ?? null,
            'descr' => $coach['description_coach'] ?? null,
        ]);
    }

    public function createSportif(int $userId): void
    {
        $stmt = $this->db->prepare("INSERT INTO sportifs (id_user) VALUES (:id)");
        $stmt->execute(['id' => $userId]);
    }
}

?>