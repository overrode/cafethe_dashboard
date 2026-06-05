<?php
$title = 'Ajouter un utilisateur - CafThé';
require __DIR__ . '/../layout/header.php';
?>

<h2>Ajouter un utilisateur</h2>

<p>
    <a href="/public/index.php?route=/users">Retour aux utilisateurs</a>
</p>

<form action="/public/index.php?route=/users/store" method="POST">
    <p>
        <label>Nom</label><br>
        <input type="text" name="name" required>
    </p>

    <p>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </p>

    <p>
        <label>Mot de passe</label><br>
        <input type="password" name="password" required>
    </p>

    <p>
        <label>Rôle</label><br>
        <select name="role" required>
            <option value="vendeur">Vendeur</option>
            <option value="admin">Administrateur</option>
        </select>
    </p>

    <p>
        <label>
            <input type="checkbox" name="is_active" checked>
            Utilisateur actif
        </label>
    </p>

    <button type="submit">Enregistrer</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>