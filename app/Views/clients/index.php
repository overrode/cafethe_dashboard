<?php
$title = 'Dashboard - CafThé';
require __DIR__ . '/../layout/header.php';
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Clients</h2>

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

<?php require __DIR__ . '/../layout/footer.php'; ?>