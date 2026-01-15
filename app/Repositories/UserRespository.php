<?php

require_once __DIR__ . '/BaseRepository.php';

class UserRespository extends BaseRepository
{
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id_user DESC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id_user = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email_user = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Crée un user et retourne son id_user (PostgreSQL => RETURNING)
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO users (nom_user, prenom_user, email_user, role_user, phone_user, password_user)
            VALUES (:nom, :prenom, :email, :role, :phone, :pass)
            RETURNING id_user
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nom'    => $data['nom_user'] ?? null,
            'prenom' => $data['prenom_user'] ?? null,
            'email'  => $data['email_user'],
            'role'   => $data['role_user'],     // 'coach' ou 'sportif'
            'phone'  => $data['phone_user'] ?? null,
            'pass'   => $data['password_user'], // hash
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE users
            SET nom_user = :nom,
                prenom_user = :prenom,
                email_user = :email,
                role_user = :role,
                phone_user = :phone
            WHERE id_user = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'     => $id,
            'nom'    => $data['nom_user'] ?? null,
            'prenom' => $data['prenom_user'] ?? null,
            'email'  => $data['email_user'],
            'role'   => $data['role_user'],
            'phone'  => $data['phone_user'] ?? null,
        ]);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $sql = "UPDATE users SET password_user = :pass WHERE id_user = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'pass' => $hash]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id_user = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>