<?php
$title = 'Ajouter un client - CafThé';
require __DIR__ . '/../layout/header.php';
?>

<h2>Ajouter un client</h2>

<p>
    <a href="/public/index.php?route=/clients">Retour aux clients</a>
</p>

<form action="/public/index.php?route=/clients/store" method="POST">
    <p>
        <label>Nom</label><br>
        <input type="text" name="name" required>
    </p>

    <p>
        <label>Email</label><br>
        <input type="email" name="email">
    </p>

    <p>
        <label>Téléphone</label><br>
        <input type="text" name="phone">
    </p>

    <p>
        <label>Adresse</label><br>
        <textarea name="address"></textarea>
    </p>

    <p>
        <label>Favoris</label><br>
        <textarea name="favorites"></textarea>
    </p>

    <p>
        <label>Panier abandonné</label><br>
        <textarea name="abandoned_cart"></textarea>
    </p>

    <button type="submit">Enregistrer</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>