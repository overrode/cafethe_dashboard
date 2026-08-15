<?php
$title = 'Dashboard - CafThé';
require BACKEND_HEADER_PATH;
?>

    <h1>CafThé - Dashboard vendeur</h1>
    <h2>Nouvelle vente</h2>

    <p>
        <a href="/public/index.php?route=/sales">Retour aux ventes</a>
    </p>

    <form
        action="/public/index.php?route=/sales/store"
        method="POST"
        x-data="saleForm"
    >
        <div
            x-show="newClientModalOpen"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="closeNewClientModal()"
            @click.self="closeNewClientModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                x-show="newClientModalOpen"
                x-transition
                class="w-full max-w-lg rounded-xl border border-white/20 bg-white p-6 text-black shadow-2xl"
                @click.stop
            >
                <h3 class="mb-6 text-xl font-semibold">
                    Nouveau client
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block">Nom *</label>

                        <input
                            type="text"
                            x-model="newClient.name"
                            class="w-full rounded border px-3 py-2"
                            required
                        >
                    </div>

                    <div>
                        <label class="mb-1 block">Email</label>

                        <input
                            type="email"
                            x-model="newClient.email"
                            class="w-full rounded border px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block">Téléphone</label>

                        <input
                            type="text"
                            x-model="newClient.phone"
                            class="w-full rounded border px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block">Adresse</label>

                        <textarea
                            x-model="newClient.address"
                            class="w-full rounded border px-3 py-2"
                            rows="3"
                        ></textarea>
                    </div>

                    <p
                        x-show="clientError"
                        x-text="clientError"
                        class="text-sm text-red-600"
                    ></p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="closeNewClientModal()"
                        :disabled="clientSaving"
                        class="rounded border px-4 py-2"
                    >
                        Annuler
                    </button>

                    <button
                        type="button"
                        @click="createClient()"
                        :disabled="clientSaving"
                        class="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
                    >
                        <span x-show="!clientSaving">
                            Créer le client
                        </span>

                        <span x-show="clientSaving">
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div>
            <h3>Client</h3>

            <p>
                <strong x-text="selectedClientName"></strong>
            </p>

            <input
                type="hidden"
                name="client_id"
                :value="selectedClientId"
            >

            <button
                type="button"
                @click="openClientModal()"
            >
                Choisir un client
            </button>
            <button
                type="button"
                @click="openNewClientModal()"
            >
                + Nouveau client
            </button>
            <div
                x-show="clientModalOpen"
                x-cloak
                @keydown.escape.window="closeClientModal()"
            >
                <div>
                    <h3>Choisir un client</h3>

                    <select x-ref="clientSelect">
                        <option value="">Client non renseigné</option>

                        <?php foreach ($clients as $client): ?>
                            <option
                                value="<?= htmlspecialchars((string) $client['id']) ?>"
                                data-name="<?= htmlspecialchars($client['name']) ?>"
                            >
                                <?= htmlspecialchars($client['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button
                        type="button"
                        @click="closeClientModal()"
                    >
                        Annuler
                    </button>

                    <button
                        type="button"
                        @click="selectClient($refs.clientSelect.selectedOptions[0])"
                    >
                        Sélectionner
                    </button>
                </div>
            </div>
        </div>
        <h3>Produits</h3>

        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix HT</th>
                    <th>TVA</th>
                    <th>Total TTC</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <template x-for="(item, index) in items" :key="item.productId">
                    <tr>
                        <td x-text="item.name"></td>

                        <td x-text="item.quantity"></td>

                        <td x-text="item.unitPrice.toFixed(2) + ' €'"></td>

                        <td x-text="item.vatRate + ' %'"></td>

                        <td x-text="itemTotalTtc(item).toFixed(2) + ' €'"></td>

                        <td>
                            <button
                                type="button"
                                @click="removeItem(index)"
                            >
                                Supprimer
                            </button>
                        </td>

                        <!-- Data actually sent to PHP -->
                        <input
                            type="hidden"
                            :name="`items[${index}][product_id]`"
                            :value="item.productId"
                        >

                        <input
                            type="hidden"
                            :name="`items[${index}][quantity]`"
                            :value="item.quantity"
                        >
                    </tr>
                </template>
            </tbody>
        </table>

        <p x-show="items.length === 0">
            Aucun produit ajouté.
        </p>

        <button
            type="button"
            @click="openProductModal()"
        >
            + Ajouter un produit
        </button>

        <div
            x-show="productModalOpen"
            x-cloak
            @keydown.escape.window="closeProductModal()"
        >
            <div>
                <h3>Ajouter un produit</h3>

                <p>
                    <label>Produit</label><br>

                    <select
                        x-model="selectedProductId"
                        x-ref="productSelect"
                    >
                        <option value="">Choisir un produit</option>

                        <?php foreach ($products as $product): ?>
                            <?php if ($product['is_active'] && (float) $product['stock'] > 0): ?>

                                <option
                                    value="<?= htmlspecialchars((string) $product['id']) ?>"
                                    data-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-price="<?= htmlspecialchars((string) $product['price']) ?>"
                                    data-vat="<?= htmlspecialchars((string) $product['vat_rate']) ?>"
                                    data-stock="<?= htmlspecialchars((string) $product['stock']) ?>"
                                >
                                    <?= htmlspecialchars($product['name']) ?>
                                    - Stock : <?= htmlspecialchars((string) $product['stock']) ?>
                                    - <?= htmlspecialchars((string) $product['price']) ?> € HT
                                </option>

                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label>Quantité</label><br>

                    <input
                        type="number"
                        x-model.number="selectedQuantity"
                        min="0.01"
                        step="0.01"
                    >
                </p>

                <button
                    type="button"
                    @click="closeProductModal()"
                >
                    Annuler
                </button>

                <button
                    type="button"
                    @click="addProduct($refs.productSelect.selectedOptions[0])"
                >
                    Ajouter
                </button>
            </div>
        </div>

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