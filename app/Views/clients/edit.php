<?php
$title = 'Dashboard - CafThé';
require __DIR__ . '/../layout/header.php';
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Modifier un client</h2>

    <p>
        <a href="/public/index.php?route=/clients">Retour aux clients</a>
    </p>

    <form action="/public/index.php?route=/clients/update" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $client['id']) ?>">

        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>" required>
        </p>

        <p>
            <label>Email</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars((string) $client['email']) ?>">
        </p>

        <p>
            <label>Téléphone</label><br>
            <input type="text" name="phone" value="<?= htmlspecialchars((string) $client['phone']) ?>">
        </p>

        <p>
            <label>Adresse</label><br>
            <textarea name="address"><?= htmlspecialchars((string) $client['address']) ?></textarea>
        </p>

        <p>
            <label>Favoris</label><br>
            <textarea name="favorites"><?= htmlspecialchars((string) $client['favorites']) ?></textarea>
        </p>

        <p>
            <label>Panier abandonné</label><br>
            <textarea name="abandoned_cart"><?= htmlspecialchars((string) $client['abandoned_cart']) ?></textarea>
        </p>

        <button type="submit">Mettre à jour</button>
    </form>

<?php require __DIR__ . '/../layout/footer.php'; ?>