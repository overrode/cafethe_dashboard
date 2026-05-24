<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits - CafThé</title>
</head>
<body>
    <h1>CafThé - Dashboard vendeur</h1>

    <nav>
        <a href="/public/">Accueil</a> |
        <a href="/public/products">Produits</a>
    </nav>

    <h2>Liste des produits</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Catégorie</th>
                <th>SKU</th>
                <th>Nom</th>
                <th>Type de vente</th>
                <th>Prix HT</th>
                <th>TVA</th>
                <th>Stock</th>
                <th>Actif</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $product['id']) ?></td>
                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                    <td><?= htmlspecialchars($product['sku']) ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['sale_type']) ?></td>
                    <td><?= htmlspecialchars((string) $product['price']) ?> €</td>
                    <td><?= htmlspecialchars((string) $product['vat_rate']) ?>%</td>
                    <td><?= htmlspecialchars((string) $product['stock']) ?></td>
                    <td><?= $product['is_active'] ? 'Oui' : 'Non' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
