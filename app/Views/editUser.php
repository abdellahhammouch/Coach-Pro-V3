<?php $title = "Modifier utilisateur"; ?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="max-w-3xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Modifier utilisateur #<?= e($user['id_user']) ?></h1>

  <form class="space-y-5 bg-white/5 border border-white/10 rounded-2xl p-6"
        method="POST" action="/users/update" novalidate>

    <?= csrf_field(); ?>
    <input type="hidden" name="id_user" value="<?= e($user['id_user']) ?>">

    <?php
      $val = function(string $k) use ($old, $user) {
        return $old[$k] ?? $user[$k] ?? '';
      };
    ?>

    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 text-slate-300">Nom</label>
        <input name="nom_user" value="<?= e($val('nom_user')) ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Prénom</label>
        <input name="prenom_user" value="<?= e($val('prenom_user')) ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      </div>
    </div>

    <div>
      <label class="block mb-1 text-slate-300">Email *</label>
      <input type="email" required name="email_user" value="<?= e($val('email_user')) ?>"
             class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
      <?php if (!empty($errors['email_user'])): ?>
        <p class="text-red-400 text-sm mt-1"><?= e($errors['email_user']) ?></p>
      <?php endif; ?>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 text-slate-300">Rôle *</label>
        <select required name="role_user"
                class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none">
          <option value="sportif" <?= ($val('role_user') === 'sportif') ? 'selected' : '' ?>>Sportif</option>
          <option value="coach" <?= ($val('role_user') === 'coach') ? 'selected' : '' ?>>Coach</option>
        </select>
        <?php if (!empty($errors['role_user'])): ?>
          <p class="text-red-400 text-sm mt-1"><?= e($errors['role_user']) ?></p>
        <?php endif; ?>
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Téléphone</label>
        <input name="phone_user" value="<?= e($val('phone_user')) ?>"
               class="w-full px-3 py-2 rounded-xl bg-black/30 border border-white/10 focus:outline-none" />
        <?php if (!empty($errors['phone_user'])): ?>
          <p class="text-red-400 text-sm mt-1"><?= e($errors['phone_user']) ?></p>
        <?php endif; ?>
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

<?php require __DIR__ . '/partials/footer.php'; ?>
