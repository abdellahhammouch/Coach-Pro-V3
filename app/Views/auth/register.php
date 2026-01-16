<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - SportCoach</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/styles/style.css">
</head>

<body>
<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="/" class="logo">
      <i class="fas fa-dumbbell"></i>
      <span>SportCoach</span>
    </a>
    <ul class="nav-menu" id="navMenu">
      <li><a href="/" class="nav-link"><i class="fas fa-home"></i> Accueil</a></li>
      <li><a href="/login" class="nav-link"><i class="fas fa-users"></i> Nos Coachs</a></li>
      <li><a href="/login" class="btn-secondary"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
      <li><a href="/register" class="btn-primary"><i class="fas fa-user-plus"></i> Inscription</a></li>
    </ul>
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
      <i class="fas fa-bars"></i>
    </button>
  </div>
</nav>

<div class="form-container" style="max-width: 600px;">
  <div class="form-header">
    <h2>Créer un compte</h2>
    <p>Rejoignez notre communauté sportive</p>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="error-message">
      <i class="fas fa-exclamation-triangle"></i>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= e($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="success-message">
      <i class="fas fa-check-circle"></i> <?= e($success) ?>
    </div>
  <?php endif; ?>

  <form id="registerForm" action="/register" method="post">
    <?= csrf_field(); ?>

    <div class="form-group">
      <label for="userType">Je m'inscris en tant que</label>
      <div class="input-group">
        <i class="fas fa-user-tag"></i>
        <select name="role" id="userType" class="form-control" required>
          <option value="">Sélectionnez votre rôle</option>
          <option value="sportif" <?= (($old['role'] ?? '') === 'sportif') ? 'selected' : '' ?>>Sportif</option>
          <option value="coach" <?= (($old['role'] ?? '') === 'coach') ? 'selected' : '' ?>>Coach</option>
        </select>
      </div>
      <span class="error-message" id="userTypeError">Veuillez sélectionner un type de compte</span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div class="form-group">
        <label for="firstName">Prénom</label>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="prenom" id="firstName" class="form-control"
                 placeholder="Votre prénom" required
                 value="<?= e($old['prenom'] ?? '') ?>">
        </div>
        <span class="error-message" id="firstNameError">Veuillez entrer un prénom valide(pas de chiffres,min 2 caractères)</span>
      </div>

      <div class="form-group">
        <label for="lastName">Nom</label>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="nom" id="lastName" class="form-control"
                 placeholder="Votre nom" required
                 value="<?= e($old['nom'] ?? '') ?>">
        </div>
        <span class="error-message" id="lastNameError">Veuillez entrer un nom valide(pas de chiffres,min 2 caractères)</span>
      </div>
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="email" class="form-control"
               placeholder="votre@email.com" required
               value="<?= e($old['email'] ?? '') ?>">
      </div>
      <span class="error-message" id="emailError">Veuillez entrer un email valide</span>
    </div>

    <div class="form-group">
      <label for="phone">Téléphone</label>
      <div class="input-group">
        <i class="fas fa-phone"></i>
        <input type="tel" name="phone" id="phone" class="form-control"
               placeholder="+212 6XX-XXXXXX" required
               value="<?= e($old['phone'] ?? '') ?>">
      </div>
      <span class="error-message" id="phoneError">Veuillez entrer un numero de telephone valide</span>
    </div>

    <div class="form-group">
      <label for="password">Mot de passe</label>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" class="form-control"
               placeholder="Min. 8 caractères" required>
      </div>
      <span class="error-message" id="passwordError">8 caractères min. (majuscule, minuscule, chiffres)</span>
    </div>

    <div class="form-group">
      <label for="confirmPassword">Confirmer le mot de passe</label>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
               placeholder="Confirmez votre mot de passe" required>
      </div>
      <span class="error-message" id="confirmPasswordError">Les mots de passe ne correspondent pas</span>
    </div>

    <!-- Coach specific fields -->
    <?php $showCoach = (($old['role'] ?? '') === 'coach'); ?>
    <div id="coachFields" style="display: <?= $showCoach ? 'block' : 'none' ?>;">
      <div class="form-group">
        <label><i class="fas fa-star"></i> Vos Spécialités</label>

        <div class="tag-input" id="tags"></div>

        <input type="hidden" name="disciplines" id="hiddenInput" required
               value="<?= e($old['disciplines'] ?? '') ?>">

        <p class="discipline-hint">
          <i class="fas fa-info-circle"></i>
          Cliquez sur les disciplines pour les sélectionner
        </p>

        <span class="error-message" id="disciplineError">Veuillez sélectionner au moins une discipline</span>

        <div class="choices">
          <span class="choice" data-value="Football"><i class="fas fa-futbol"></i> Football</span>
          <span class="choice" data-value="Tennis"><i class="fas fa-table-tennis"></i> Tennis</span>
          <span class="choice" data-value="Natation"><i class="fas fa-swimmer"></i> Natation</span>
          <span class="choice" data-value="Boxe"><i class="fas fa-fist-raised"></i> Boxe</span>
          <span class="choice" data-value="Preparation physique"><i class="fas fa-dumbbell"></i> Préparation physique</span>
          <span class="choice" data-value="Basketball"><i class="fas fa-basketball-ball"></i> Basketball</span>
          <span class="choice" data-value="Yoga"><i class="fas fa-spa"></i> Yoga</span>
        </div>
      </div>

      <div class="form-group">
        <label for="experience"><i class="fas fa-calendar-alt"></i> Années d'expérience</label>
        <div class="input-group">
          <i class="fas fa-medal"></i>
          <input type="number" name="experience" id="experience" class="form-control"
                 placeholder="Ex: 5" min="0" required
                 value="<?= e($old['experience'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="biographie"><i class="fas fa-pen"></i> Biographie</label>
        <textarea name="biographie" id="biographie" class="form-control" rows="4"
          placeholder="Parlez de votre expérience, votre approche et votre expertise..."
          style="padding: 12px; resize: vertical; border-radius: 8px; border: 2px solid #F5E6D3;"><?= e($old['biographie'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="prix"><i class="fas fa-money-bill"></i> Tarif par heure (DH)</label>
        <div class="input-group">
          <i class="fas fa-tag"></i>
          <input type="number" name="prix" id="prix" class="form-control"
                 placeholder="Ex: 100" min="50"
                 value="<?= e($old['prix'] ?? '') ?>">
        </div>
      </div>
    </div>

    <div style="margin-bottom: 20px;">
      <label style="display: flex; align-items: start; gap: 10px; cursor: pointer;">
        <input type="checkbox" id="terms" required style="margin-top: 4px;">
        <span style="font-size: 14px; color: var(--text-gray);">
          J'accepte les <a href="#" style="color: var(--primary-gold); font-weight: 600;">conditions d'utilisation</a>
          et la <a href="#" style="color: var(--primary-gold); font-weight: 600;">politique de confidentialité</a>
        </span>
      </label>
      <span class="error-message" id="termsError">Vous devez accepter les conditions</span>
    </div>

    <button type="submit" name="signup" class="btn-submit">
      <i class="fas fa-user-plus"></i> Créer mon compte
    </button>
  </form>

  <div class="form-footer">
    <p>Vous avez déjà un compte ? <a href="/login">Connectez-vous</a></p>
  </div>
</div>

<footer class="footer" style="margin-top: 50px;">
  <div class="footer-container">
    <div class="footer-section">
      <h3><i class="fas fa-dumbbell"></i> SportCoach</h3>
      <p>Votre plateforme de mise en relation avec les meilleurs coachs sportifs professionnels.</p>
      <div class="social-links">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
    <div class="footer-section">
      <h3>Navigation</h3>
      <ul class="footer-links">
        <li><a href="/">Accueil</a></li>
        <li><a href="/login">Nos Coachs</a></li>
        <li><a href="/login">Connexion</a></li>
        <li><a href="/register">Inscription</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Support</h3>
      <ul class="footer-links">
        <li><a href="#">Centre d'aide</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">Conditions d'utilisation</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Contact</h3>
      <ul class="footer-links">
        <li><i class="fas fa-envelope"></i> contact@sportcoach.com</li>
        <li><i class="fas fa-phone"></i> +212 5XX-XXXXXX</li>
        <li><i class="fas fa-map-marker-alt"></i> Casablanca, Maroc</li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2024 SportCoach. Tous droits réservés.</p>
  </div>
</footer>

<script src="/public/script/script.js"></script>
</body>
</html>
