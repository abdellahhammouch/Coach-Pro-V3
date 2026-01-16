<?php $title = "Ajouter un utilisateur"; ?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="form-container" style="max-width: 650px;">
  <div class="form-header">
    <h2>Ajouter un utilisateur</h2>
    <p>Créer un compte coach ou sportif</p>
  </div>

  <form method="POST" action="/users/store" novalidate>
    <?= csrf_field(); ?>

    <div class="form-group">
      <label for="userType">Rôle *</label>
      <div class="input-group">
        <i class="fas fa-user-tag"></i>
        <select name="role_user" id="userType" class="form-control" required>
          <option value="">Sélectionnez</option>
          <option value="sportif" <?= (($old['role_user'] ?? '') === 'sportif') ? 'selected' : '' ?>>Sportif</option>
          <option value="coach" <?= (($old['role_user'] ?? '') === 'coach') ? 'selected' : '' ?>>Coach</option>
        </select>
      </div>
      <?php if (!empty($errors['role_user'])): ?>
        <span class="error-message show"><?= e($errors['role_user']) ?></span>
      <?php endif; ?>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div class="form-group">
        <label>Prénom</label>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input name="prenom_user" value="<?= e($old['prenom_user'] ?? '') ?>" class="form-control" placeholder="Prénom">
        </div>
      </div>

      <div class="form-group">
        <label>Nom</label>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input name="nom_user" value="<?= e($old['nom_user'] ?? '') ?>" class="form-control" placeholder="Nom">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>Email *</label>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email_user" value="<?= e($old['email_user'] ?? '') ?>" class="form-control" required placeholder="email@exemple.com">
      </div>
      <?php if (!empty($errors['email_user'])): ?>
        <span class="error-message show"><?= e($errors['email_user']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Téléphone</label>
      <div class="input-group">
        <i class="fas fa-phone"></i>
        <input name="phone_user" value="<?= e($old['phone_user'] ?? '') ?>" class="form-control" placeholder="06XXXXXXXX">
      </div>
      <?php if (!empty($errors['phone_user'])): ?>
        <span class="error-message show"><?= e($errors['phone_user']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Mot de passe *</label>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password_user" class="form-control" required placeholder="Min 6 caractères">
      </div>
      <?php if (!empty($errors['password_user'])): ?>
        <span class="error-message show"><?= e($errors['password_user']) ?></span>
      <?php endif; ?>
    </div>

    <?php $showCoach = (($old['role_user'] ?? '') === 'coach'); ?>
    <div id="coachFields" style="display: <?= $showCoach ? 'block' : 'none' ?>;">
      <div class="form-group">
        <label>Discipline (coach) *</label>
        <div class="input-group">
          <i class="fas fa-dumbbell"></i>
          <input name="discipline_coach" value="<?= e($old['discipline_coach'] ?? '') ?>" class="form-control" placeholder="Ex: Football">
        </div>
        <?php if (!empty($errors['discipline_coach'])): ?>
          <span class="error-message show"><?= e($errors['discipline_coach']) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Expérience (années)</label>
        <div class="input-group">
          <i class="fas fa-medal"></i>
          <input type="number" min="0" name="experiences_coach" value="<?= e($old['experiences_coach'] ?? '') ?>" class="form-control" placeholder="Ex: 5">
        </div>
        <?php if (!empty($errors['experiences_coach'])): ?>
          <span class="error-message show"><?= e($errors['experiences_coach']) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description_coach" rows="4" class="form-control" style="padding: 12px; resize: vertical;"><?= e($old['description_coach'] ?? '') ?></textarea>
      </div>
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
