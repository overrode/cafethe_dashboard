<?php
$title = 'Dashboard - CafThé';
require __DIR__ . '/../layout/header.php';
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Liste des produits</h2>
<p>
    <a href="/public/index.php?route=/products/create">Ajouter un produit</a>
</p>
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
                <th>Actions</th>
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
                    <td>
                        <a href="/public/index.php?route=/products/edit&id=<?= htmlspecialchars((string) $product['id']) ?>">
                            Modifier
                        </a>
                        <?php if ($product['is_active']): ?>
                            |
                            <a href="/public/index.php?route=/products/deactivate&id=<?= htmlspecialchars((string) $product['id']) ?>"
                               onclick="return confirm('Désactiver ce produit ?');">
                                Désactiver
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php require __DIR__ . '/../layout/footer.php'; ?>
