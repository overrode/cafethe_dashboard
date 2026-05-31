<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle vente - CafThé</title>
</head>
<body>
    <h1>Nouvelle vente</h1>

    <p>
        <a href="/public/index.php?route=/sales">Retour aux ventes</a>
    </p>

    <form action="/public/index.php?route=/sales/store" method="POST">
        <p>
            <label>Client</label><br>
            <select name="client_id">
                <option value="">Client non renseigné</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= htmlspecialchars((string) $client['id']) ?>">
                        <?= htmlspecialchars($client['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>Produit</label><br>
            <select name="product_id" required>
                <?php foreach ($products as $product): ?>
                    <?php if ($product['is_active'] && (float) $product['stock'] > 0): ?>
                        <option value="<?= htmlspecialchars((string) $product['id']) ?>">
                            <?= htmlspecialchars($product['name']) ?>
                            - Stock: <?= htmlspecialchars((string) $product['stock']) ?>
                            - Prix HT: <?= htmlspecialchars((string) $product['price']) ?> €
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>Quantité</label><br>
            <input type="number" name="quantity" step="0.01" min="0.01" required>
        </p>

        <p>
            <label>Moyen de paiement</label><br>
            <select name="payment_method" required>
                <option value="cb">CB</option>
                <option value="especes">Espèces</option>
                <option value="cheque">Chèque</option>
            </select>
        </p>

        <button type="submit">Enregistrer la vente</button>
    </form>
</body>
</html>