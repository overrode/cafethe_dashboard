<?php
$title = 'Dashboard - CafThé';
require BACKEND_HEADER_PATH;
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Nouvelle vente</h2>

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

        <div x-data="saleForm">

            <template x-for="(item, index) in items" :key="index">
                <div>
                    <p>
                        <label>Produit</label><br>

                        <select
                            :name="`items[${index}][product_id]`"
                            x-model="item.productId"
                            @change="updateProduct(index, $event.target.selectedOptions[0])"
                            required
                        >
                            <option value="">Choisir un produit</option>

                            <?php foreach ($products as $product): ?>
                                <?php if ($product['is_active'] && (float) $product['stock'] > 0): ?>
                                    <option
                                        value="<?= htmlspecialchars((string) $product['id']) ?>"
                                        data-price="<?= htmlspecialchars((string) $product['price']) ?>"
                                        data-vat="<?= htmlspecialchars((string) $product['vat_rate']) ?>"
                                    >
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

                        <input
                            type="number"
                            :name="`items[${index}][quantity]`"
                            x-model="item.quantity"
                            step="0.01"
                            min="0.01"
                            required
                        >
                    </p>

                    <button
                        type="button"
                        @click="removeItem(index)"
                        x-show="items.length > 1"
                    >
                        Supprimer
                    </button>
                </div>
            </template>

            <button
                type="button"
                @click="addItem()"
            >
                Ajouter un produit
            </button>

            <hr>

            <p>
                Total HT :
                <strong x-text="totalHt.toFixed(2) + ' €'"></strong>
            </p>

            <p>
                TVA :
                <strong x-text="totalVat.toFixed(2) + ' €'"></strong>
            </p>

            <p>
                Total TTC :
                <strong x-text="totalTtc.toFixed(2) + ' €'"></strong>
            </p>

        </div>

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

<?php require BACKEND_FOOTER_PATH; ?>