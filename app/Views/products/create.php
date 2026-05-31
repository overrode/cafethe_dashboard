<?php
$title = 'Dashboard - CafThé';
require __DIR__ . '/../layout/header.php';
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Ajouter un produit</h2>

    <p>
        <a href="/public/index.php?route=/products">Retour aux produits</a>
    </p>

    <form action="/public/index.php?route=/products/store" method="POST">
        <p>
            <label>Catégorie</label><br>
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars((string) $category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>SKU</label><br>
            <input type="text" name="sku" required>
        </p>

        <p>
            <label>Nom</label><br>
            <input type="text" name="name" required>
        </p>

        <p>
            <label>Description</label><br>
            <textarea name="description" required></textarea>
        </p>

        <p>
            <label>Type de vente</label><br>
            <select name="sale_type" required>
                <option value="poids">Poids</option>
                <option value="unite">Unité</option>
            </select>
        </p>

        <p>
            <label>Prix HT</label><br>
            <input type="number" name="price" step="0.01" min="0" required>
        </p>

        <p>
            <label>TVA</label><br>
            <select name="vat_rate" required>
                <option value="5.50">5.5%</option>
                <option value="20.00">20%</option>
            </select>
        </p>

        <p>
            <label>Stock</label><br>
            <input type="number" name="stock" step="0.01" min="0" required>
        </p>

        <p>
            <label>Image</label><br>
            <input type="text" name="image">
        </p>

        <p>
            <label>Origine</label><br>
            <input type="text" name="origin">
        </p>

        <p>
            <label>
                <input type="checkbox" name="is_active" checked>
                Produit actif
            </label>
        </p>

        <button type="submit">Enregistrer</button>
    </form>
<?php require __DIR__ . '/../layout/footer.php'; ?>