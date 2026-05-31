<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - CafThé</title>
</head>
<body>
    <h1>Dashboard vendeur</h1>

    <nav>
        <a href="/public/index.php?route=/dashboard">Dashboard</a> |
        <a href="/public/index.php?route=/products">Produits</a> |
        <a href="/public/index.php?route=/clients">Clients</a> |
        <a href="/public/index.php?route=/sales">Ventes</a>
    </nav>

    <h2>Indicateurs clés</h2>

    <table border="1" cellpadding="8">
        <tbody>
            <tr>
                <th>Nombre de ventes</th>
                <td><?= htmlspecialchars((string) $stats['sales_count']) ?></td>
            </tr>
            <tr>
                <th>Chiffre d'affaires TTC</th>
                <td><?= number_format((float) $stats['revenue'], 2, ',', ' ') ?> €</td>
            </tr>
            <tr>
                <th>Panier moyen TTC</th>
                <td><?= number_format((float) $stats['average_basket'], 2, ',', ' ') ?> €</td>
            </tr>
            <tr>
                <th>Nombre de clients</th>
                <td><?= htmlspecialchars((string) $stats['clients_count']) ?></td>
            </tr>
            <tr>
                <th>Produits actifs</th>
                <td><?= htmlspecialchars((string) $stats['active_products_count']) ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Produits en stock faible</h2>

    <?php if (empty($lowStockProducts)): ?>
        <p>Aucun produit en stock faible.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Catégorie</th>
                    <th>SKU</th>
                    <th>Nom</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockProducts as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $product['id']) ?></td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td><?= htmlspecialchars($product['sku']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars((string) $product['stock']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>