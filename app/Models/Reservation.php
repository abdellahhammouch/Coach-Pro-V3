<?php

class Reservation
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $seanceId, int $sportifId): bool
    {
        $sql = "INSERT INTO reservations (seance_id, sportif_id, statut_reservation)
                VALUES (?, ?, 'active')";
        return $this->pdo->prepare($sql)->execute([$seanceId, $sportifId]);
    }
}
