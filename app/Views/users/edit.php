<?php
$title = 'Modifier un utilisateur - CafThé';
require __DIR__ . '/../layout/header.php';
?>

<h2>Modifier un utilisateur</h2>

<p>
    <a href="/public/index.php?route=/users">Retour aux utilisateurs</a>
</p>

<form action="/public/index.php?route=/users/update" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $user['id']) ?>">

    <p>
        <label>Nom</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
    </p>

    <p>
        <label>Email</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </p>

    <p>
        <label>Nouveau mot de passe</label><br>
        <input type="password" name="password">
        <small>Laisser vide pour conserver le mot de passe actuel.</small>
    </p>

    <p>
        <label>Rôle</label><br>
        <select name="role" required>
            <option value="vendeur" <?= $user['role'] === 'vendeur' ? 'selected' : '' ?>>Vendeur</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select>
    </p>

    <p>
        <label>
            <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?>>
            Utilisateur actif
        </label>
    </p>

    <button type="submit">Mettre à jour</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>