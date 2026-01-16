<?php

class SeanceController
{
    public function store(): void
    {
        require_role('coach');
        csrf_verify();

        $pdo = Database::getConnection();
        $coach_id = (int) $_SESSION['user_id'];

        $date  = trim((string)($_POST['date'] ?? ''));
        $heure = trim((string)($_POST['heure'] ?? ''));
        $duree = (int)($_POST['duree'] ?? 0);

        if ($date === '' || $heure === '' || $duree <= 0) {
            flash('errors', ["Tous les champs sont obligatoires (durée > 0)."]);
            redirect('/dashboard/coach');
        }

        $seanceModel = new Seance($pdo);

        if ($seanceModel->create($coach_id, $date, $heure, $duree)) {
            flash('success', "Séance ajoutée avec succès.");
        } else {
            flash('errors', ["Erreur : impossible d'ajouter la séance."]);
        }

        redirect('/dashboard/coach');
    }

    public function delete(): void
    {
        require_role('coach');
        csrf_verify();

        $pdo = Database::getConnection();
        $coach_id = (int) $_SESSION['user_id'];
        $id_seance = (int)($_POST['seance_id'] ?? 0);

        if ($id_seance <= 0) {
            flash('errors', ["Séance invalide."]);
            redirect('/dashboard/coach');
        }

        // check statut
        $check = $pdo->prepare("SELECT statut_seance FROM seances WHERE id_seance = ? AND coach_id = ?");
        $check->execute([$id_seance, $coach_id]);
        $row = $check->fetch();

        if (!$row) {
            flash('errors', ["Séance introuvable."]);
            redirect('/dashboard/coach');
        }

        if (($row['statut_seance'] ?? '') !== 'disponible') {
            flash('errors', ["Impossible : cette séance est déjà réservée."]);
            redirect('/dashboard/coach');
        }

        $seanceModel = new Seance($pdo);

        if ($seanceModel->delete($id_seance, $coach_id)) {
            flash('success', "Séance supprimée.");
        } else {
            flash('errors', ["Suppression impossible."]);
        }

        redirect('/dashboard/coach');
    }

}
