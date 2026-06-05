<?php
$title = 'Utilisateurs - CafThé';
require __DIR__ . '/../layout/header.php';
?>

<h2>Gestion des utilisateurs</h2>

<p>
    <a class="button" href="/public/index.php?route=/users/create">Ajouter un utilisateur</a>
</p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Actif</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars((string) $user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= $user['is_active'] ? 'Oui' : 'Non' ?></td>
                <td>
                    <a href="/public/index.php?route=/users/edit&id=<?= htmlspecialchars((string) $user['id']) ?>">
                        Modifier
                    </a>

                    <?php if ($user['is_active'] && (int) $user['id'] !== (int) $_SESSION['user']['id']): ?>
                        |
                        <a href="/public/index.php?route=/users/deactivate&id=<?= htmlspecialchars((string) $user['id']) ?>"
                           onclick="return confirm('Désactiver cet utilisateur ?');">
                            Désactiver
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../layout/footer.php'; ?>