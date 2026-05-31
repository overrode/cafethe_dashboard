<?php
$title = 'Dashboard - CafThé';
require __DIR__ . '/../layout/header.php';
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Ventes</h2>

    <p>
        <a href="/public/index.php?route=/sales/create">Nouvelle vente</a>
    </p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Vendeur</th>
                <th>Client</th>
                <th>Paiement</th>
                <th>Total HT</th>
                <th>TVA</th>
                <th>Total TTC</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $sale['id']) ?></td>
                    <td><?= htmlspecialchars($sale['sale_date']) ?></td>
                    <td><?= htmlspecialchars($sale['user_name']) ?></td>
                    <td><?= htmlspecialchars((string) ($sale['client_name'] ?? 'Client non renseigné')) ?></td>
                    <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                    <td><?= htmlspecialchars((string) $sale['total_ht']) ?> €</td>
                    <td><?= htmlspecialchars((string) $sale['total_vat']) ?> €</td>
                    <td><?= htmlspecialchars((string) $sale['total_ttc']) ?> €</td>
                    <td><?= htmlspecialchars($sale['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php require __DIR__ . '/../layout/footer.php'; ?>