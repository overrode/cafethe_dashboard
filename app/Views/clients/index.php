<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clients - CafThé</title>
</head>
<body>
    <h1>Clients</h1>

    <nav>
        <a href="/public/index.php?route=/products">Produits</a> |
        <a href="/public/index.php?route=/clients">Clients</a>
    </nav>

    <p>
        <a href="/public/index.php?route=/clients/create">Ajouter un client</a>
    </p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Favoris</th>
                <th>Panier abandonné</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $client['id']) ?></td>
                    <td><?= htmlspecialchars($client['name']) ?></td>
                    <td><?= htmlspecialchars((string) $client['email']) ?></td>
                    <td><?= htmlspecialchars((string) $client['phone']) ?></td>
                    <td><?= htmlspecialchars((string) $client['favorites']) ?></td>
                    <td><?= htmlspecialchars((string) $client['abandoned_cart']) ?></td>
                    <td>
                        <a href="/public/index.php?route=/clients/edit&id=<?= htmlspecialchars((string) $client['id']) ?>">
                            Modifier
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>