<?php

class UserRespository extends BaseRepository
{
    public function findAll(): array
    {
        // On récupère user + infos coach si role coach
        $sql = "
            SELECT 
              u.id_user, u.nom_user, u.prenom_user, u.email_user, u.role_user, u.phone_user,
              c.discipline_coach, c.experiences_coach, c.description_coach
            FROM users u
            LEFT JOIN coachs c ON c.id_user = u.id_user
            ORDER BY u.id_user DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function createUser(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (nom_user, prenom_user, email_user, role_user, phone_user, password_user)
            VALUES (:nom, :prenom, :email, :role, :phone, :password)
            RETURNING id_user
        ");
        $stmt->execute([
            'nom' => $data['nom_user'] ?? null,
            'prenom' => $data['prenom_user'] ?? null,
            'email' => $data['email_user'],
            'role' => $data['role_user'],
            'phone' => $data['phone_user'] ?? null,
            'password' => password_hash($data['password_user'], PASSWORD_DEFAULT),
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function createCoachInfos(int $userId, array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO coachs (id_user, discipline_coach, experiences_coach, description_coach)
            VALUES (:id_user, :discipline, :exp, :description)
        ");
        $stmt->execute([
            'id_user' => $userId,
            'discipline' => $data['discipline_coach'] ?? null,
            'exp' => $data['experiences_coach'] ?? null,
            'description' => $data['description_coach'] ?? null,
        ]);
    }

    public function updateUser(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users SET 
              nom_user = :nom,
              prenom_user = :prenom,
              email_user = :email,
              role_user = :role,
              phone_user = :phone
            WHERE id_user = :id
        ");
        $stmt->execute([
            'nom' => $data['nom_user'] ?? null,
            'prenom' => $data['prenom_user'] ?? null,
            'email' => $data['email_user'],
            'role' => $data['role_user'],
            'phone' => $data['phone_user'] ?? null,
            'id' => $id,
        ]);
    }

    public function deleteUser(int $id): void
    {
        // si coach a des infos dans coachs -> supprimer avant (FK)
        $stmt = $this->pdo->prepare("DELETE FROM coachs WHERE id_user = :id");
        $stmt->execute(['id' => $id]);

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
    }
}
