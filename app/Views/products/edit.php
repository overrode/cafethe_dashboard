<?php
$title = 'Dashboard - CafThé';
require __DIR__ . '/../layout/header.php';
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Modifier un produit</h2>

    <p>
        <a href="/public/index.php?route=/products">Retour aux produits</a>
    </p>

    <form action="/public/index.php?route=/products/update" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $product['id']) ?>">

        <p>
            <label>Catégorie</label><br>
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars((string) $category['id']) ?>"
                        <?= (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>SKU</label><br>
            <input type="text" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required>
        </p>

        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </p>

        <p>
            <label>Description</label><br>
            <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
        </p>

        <p>
            <label>Type de vente</label><br>
            <select name="sale_type" required>
                <option value="poids" <?= $product['sale_type'] === 'poids' ? 'selected' : '' ?>>Poids</option>
                <option value="unite" <?= $product['sale_type'] === 'unite' ? 'selected' : '' ?>>Unité</option>
            </select>
        </p>

        <p>
            <label>Prix HT</label><br>
            <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars((string) $product['price']) ?>" required>
        </p>

        <p>
            <label>TVA</label><br>
            <select name="vat_rate" required>
                <option value="5.50" <?= (float) $product['vat_rate'] === 5.5 ? 'selected' : '' ?>>5.5%</option>
                <option value="20.00" <?= (float) $product['vat_rate'] === 20.0 ? 'selected' : '' ?>>20%</option>
            </select>
        </p>

        <p>
            <label>Stock</label><br>
            <input type="number" name="stock" step="0.01" min="0" value="<?= htmlspecialchars((string) $product['stock']) ?>" required>
        </p>

        <p>
            <label>Image</label><br>
            <input type="text" name="image" value="<?= htmlspecialchars((string) $product['image']) ?>">
        </p>

        <p>
            <label>Origine</label><br>
            <input type="text" name="origin" value="<?= htmlspecialchars((string) $product['origin']) ?>">
        </p>

        <p>
            <label>
                <input type="checkbox" name="is_active" <?= $product['is_active'] ? 'checked' : '' ?>>
                Produit actif
            </label>
        </p>

        <button type="submit">Mettre à jour</button>
    </form>

<?php require __DIR__ . '/../layout/footer.php'; ?>