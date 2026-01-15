<?php $title = "Liste des utilisateurs"; ?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Utilisateurs</h1>
    <a href="/users/create" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500">+ Ajouter</a>
  </div>

  <div class="overflow-x-auto rounded-2xl border border-white/10 bg-white/5">
    <table class="w-full text-sm">
      <thead class="text-slate-300">
        <tr class="border-b border-white/10">
          <th class="text-left p-4">ID</th>
          <th class="text-left p-4">Nom</th>
          <th class="text-left p-4">Email</th>
          <th class="text-left p-4">Rôle</th>
          <th class="text-left p-4">Infos</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($users as $u): ?>
        <tr class="border-b border-white/5 hover:bg-white/5">
          <td class="p-4"><?= e($u['id_user']) ?></td>
          <td class="p-4"><?= e(($u['nom_user'] ?? '') . ' ' . ($u['prenom_user'] ?? '')) ?></td>
          <td class="p-4"><?= e($u['email_user'] ?? '') ?></td>
          <td class="p-4">
            <span class="px-2 py-1 rounded-lg bg-white/10">
              <?= e($u['role_user'] ?? '') ?>
            </span>
          </td>
          <td class="p-4 text-slate-300">
            <?php if (($u['role_user'] ?? '') === 'coach'): ?>
              Discipline: <?= e($u['discipline_coach'] ?? '-') ?> |
              Exp: <?= e($u['experiences_coach'] ?? '-') ?>
            <?php else: ?>
              Sportif
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
