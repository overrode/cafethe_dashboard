<?php
$title = 'Connexion - CafThé';
require __DIR__ . '/../layout/header.php';
?>

<h2>Connexion</h2>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/public/index.php?route=/login" method="POST">
    <p>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </p>

    <p>
        <label>Mot de passe</label><br>
        <input type="password" name="password" required>
    </p>

    <button type="submit">Se connecter</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>