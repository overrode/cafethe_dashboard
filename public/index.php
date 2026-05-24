<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getConnection();

$stmt = $db->query('SELECT id, sku, name, price, vat_rate, stock FROM products');
$products = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CafThé - Dashboard vendeur</title>
</head>
<body>
    <h1>CafThé - Dashboard vendeur</h1>

    <h2>Produits</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Nom</th>
                <th>Prix HT</th>
                <th>TVA</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $product['id']) ?></td>
                    <td><?= htmlspecialchars($product['sku']) ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars((string) $product['price']) ?> €</td>
                    <td><?= htmlspecialchars((string) $product['vat_rate']) ?>%</td>
                    <td><?= htmlspecialchars((string) $product['stock']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
