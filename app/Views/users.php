<?php $title = "Liste des utilisateurs"; ?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div style="margin-top: 100px; max-width: 1200px; margin-left:auto; margin-right:auto; padding: 0 20px;">
  <?php if ($msg = flash('success')): ?>
    <div class="success-message">
      <i class="fas fa-check-circle"></i> <?= e($msg) ?>
    </div>
  <?php endif; ?>

  <div class="table-container">
    <div class="table-header">
      <h2>Utilisateurs</h2>
      <a href="/users/create" class="btn-primary">
        <i class="fas fa-user-plus"></i> Ajouter
      </a>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom complet</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Infos</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= e($u['id_user']) ?></td>
          <td><?= e(($u['prenom_user'] ?? '') . ' ' . ($u['nom_user'] ?? '')) ?></td>
          <td><?= e($u['email_user'] ?? '') ?></td>
          <td><span class="tag"><?= e($u['role_user'] ?? '') ?></span></td>

          <td>
            <?php if (($u['role_user'] ?? '') === 'coach'): ?>
              <span class="tag">Discipline: <?= e($u['discipline_coach'] ?? '-') ?></span>
              <span class="tag">Exp: <?= e($u['experiences_coach'] ?? '-') ?></span>
            <?php else: ?>
              <span class="tag">Sportif</span>
            <?php endif; ?>
          </td>

          <td>
            <div class="action-buttons">
              <a href="/users/edit?id=<?= e($u['id_user']) ?>"
                 class="btn-secondary" style="padding: 6px 15px; font-size: 13px;">
                <i class="fas fa-pen"></i> Modifier
              </a>

              <form method="POST" action="/users/delete" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_user" value="<?= e($u['id_user']) ?>">
                <button class="btn-reject" type="submit">
                  <i class="fas fa-trash"></i> Supprimer
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
