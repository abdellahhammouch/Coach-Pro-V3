<?php $title = "Modifier utilisateur"; ?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<?php
  $val = function(string $k) use ($old, $user) {
    return $old[$k] ?? $user[$k] ?? '';
  };
?>

<div class="form-container" style="max-width: 650px;">
  <div class="form-header">
    <h2>Modifier utilisateur #<?= e($user['id_user']) ?></h2>
    <p>Modifier les informations de base</p>
  </div>

  <form method="POST" action="/users/update" novalidate>
    <?= csrf_field(); ?>
    <input type="hidden" name="id_user" value="<?= e($user['id_user']) ?>">

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div class="form-group">
        <label>Prénom</label>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input name="prenom_user" value="<?= e($val('prenom_user')) ?>" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label>Nom</label>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input name="nom_user" value="<?= e($val('nom_user')) ?>" class="form-control">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>Email *</label>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email_user" value="<?= e($val('email_user')) ?>" class="form-control" required>
      </div>
      <?php if (!empty($errors['email_user'])): ?>
        <span class="error-message show"><?= e($errors['email_user']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Rôle *</label>
      <div class="input-group">
        <i class="fas fa-user-tag"></i>
        <select name="role_user" class="form-control" required>
          <option value="sportif" <?= ($val('role_user') === 'sportif') ? 'selected' : '' ?>>Sportif</option>
          <option value="coach" <?= ($val('role_user') === 'coach') ? 'selected' : '' ?>>Coach</option>
        </select>
      </div>
      <?php if (!empty($errors['role_user'])): ?>
        <span class="error-message show"><?= e($errors['role_user']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Téléphone</label>
      <div class="input-group">
        <i class="fas fa-phone"></i>
        <input name="phone_user" value="<?= e($val('phone_user')) ?>" class="form-control">
      </div>
      <?php if (!empty($errors['phone_user'])): ?>
        <span class="error-message show"><?= e($errors['phone_user']) ?></span>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn-submit">
      <i class="fas fa-save"></i> Enregistrer
    </button>

    <div class="form-footer">
      <p><a href="/users">Retour à la liste</a></p>
    </div>
  </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
