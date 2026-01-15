<?php $title = "Ajouter un utilisateur"; ?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="max-w-3xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Ajouter un utilisateur</h1>

  <form class="space-y-5 bg-white/5 border border-white/10 rounded-2xl p-6"
        method="POST" action="/users/store" novalidate>
    <?= csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 text-slate-300">Nom</label>
        <input name="nom_user" value="<?= e($old['nom_user'] ?? '') ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Prénom</label>
        <input name="prenom_user" value="<?= e($old['prenom_user'] ?? '') ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      </div>
    </div>

    <div>
      <label class="block mb-1 text-slate-300">Email *</label>
      <input type="email" required name="email_user" value="<?= e($old['email_user'] ?? '') ?>"
             class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      <?php if (!empty($errors['email_user'])): ?>
        <p class="text-red-400 text-sm mt-1"><?= e($errors['email_user']) ?></p>
      <?php endif; ?>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 text-slate-300">Rôle *</label>
        <select required name="role_user" id="role_user"
                class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none">
          <option value="">-- choisir --</option>
          <option value="sportif" <?= (($old['role_user'] ?? '') === 'sportif') ? 'selected' : '' ?>>Sportif</option>
          <option value="coach" <?= (($old['role_user'] ?? '') === 'coach') ? 'selected' : '' ?>>Coach</option>
        </select>
        <?php if (!empty($errors['role_user'])): ?>
          <p class="text-red-400 text-sm mt-1"><?= e($errors['role_user']) ?></p>
        <?php endif; ?>
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Téléphone</label>
        <input name="phone_user" value="<?= e($old['phone_user'] ?? '') ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
        <?php if (!empty($errors['phone_user'])): ?>
          <p class="text-red-400 text-sm mt-1"><?= e($errors['phone_user']) ?></p>
        <?php endif; ?>
      </div>

    </div>

    <div>
      <label class="block mb-1 text-slate-300">Mot de passe * (min 6)</label>
      <input type="password" required minlength="6" name="password_user"
             class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      <?php if (!empty($errors['password_user'])): ?>
        <p class="text-red-400 text-sm mt-1"><?= e($errors['password_user']) ?></p>
      <?php endif; ?>
    </div>

    <div id="coach_box" class="hidden space-y-4 border-t border-white/10 pt-5">
      <h2 class="font-semibold">Infos Coach</h2>

      <div>
        <label class="block mb-1 text-slate-300">Discipline *</label>
        <input name="discipline_coach" value="<?= e($old['discipline_coach'] ?? '') ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
        <?php if (!empty($errors['discipline_coach'])): ?>
          <p class="text-red-400 text-sm mt-1"><?= e($errors['discipline_coach']) ?></p>
        <?php endif; ?>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 text-slate-300">Expérience (années)</label>
          <input type="number" min="0" name="experiences_coach" value="<?= e($old['experiences_coach'] ?? '') ?>"
                 class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
        </div>
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Description</label>
        <textarea name="description_coach" rows="4"
                  class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none"><?= e($old['description_coach'] ?? '') ?></textarea>
      </div>
    </div>

    <div class="flex gap-3">
      <button class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500">
        Enregistrer
      </button>
      <a href="/users" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15">
        Annuler
      </a>
    </div>
  </form>
</main>

<script>
  const role = document.getElementById('role_user');
  const coachBox = document.getElementById('coach_box');

  function toggleCoach() {
    coachBox.classList.toggle('hidden', role.value !== 'coach');
  }

  role.addEventListener('change', toggleCoach);
  toggleCoach();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
