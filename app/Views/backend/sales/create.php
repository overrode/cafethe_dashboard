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
    >
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-lg rounded-xl border border-white/20 bg-white p-6 text-black shadow-2xl"
            >
                <h3 class="mb-6 text-xl font-semibold">
                    Nouveau client
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block">Nom *</label>

                        <input
                            type="text"
                            class="w-full rounded border px-3 py-2"
                            required
                        >
                    </div>

                    <div>
                        <label class="mb-1 block">Email</label>

                        <input
                            type="email"
                            class="w-full rounded border px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block">Téléphone</label>

                        <input
                            type="text"
                            class="w-full rounded border px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block">Adresse</label>

                        <textarea
                            class="w-full rounded border px-3 py-2"
                            rows="3"
                        ></textarea>
                    </div>

                    <p
                            class="text-sm text-red-600"
                    ></p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded border px-4 py-2"
                    >
                        Annuler
                    </button>

                    <button
                        type="button"
                        class="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
                    >
                        <span>
                            Créer le client
                        </span>

                        <span>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div>
            <h3>Client</h3>

            <p>
                <strong></strong>
            </p>

            <input
                type="hidden"
                name="client_id"
            >

            <button
                type="button"
            >
                Choisir un client
            </button>
            <button
                type="button"
            >
                + Nouveau client
            </button>
            <div
            >
                <div>
                    <h3>Choisir un client</h3>

                    <select>
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
                    >
                        Annuler
                    </button>

                    <button
                        type="button"
                    >
                        Sélectionner
                    </button>
                </div>
            </div>
        </div>
        <h3>Produits</h3>

        <div
        >
            <div>
                <h3>Ajouter un produit</h3>

                <p>
                    <label>Produit</label><br>

            <!-- TODO: Product select -->
                </p>

                <p>
                    <label>Quantité</label><br>

                    <input
                    >
                </p>

                <button
                    type="button"
                >
                    Annuler
                </button>

                <button
                    type="button"
                >
                    Ajouter
                </button>
            </div>
        </div>

            <hr>

            <p>
                Total HT :
                <strong></strong>
            </p>

            <p>
                TVA :
                <strong></strong>
            </p>

            <p>
                Total TTC :
                <strong ></strong>
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