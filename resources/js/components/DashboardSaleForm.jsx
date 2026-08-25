import React, {useMemo, useState} from 'react';

export default function DashboardSaleForm({
                                              clients,
                                              products,
                                          }) {
    const [clientOptions, setClientOptions] = useState(clients);
    const [clientId, setClientId] = useState('');

    const [clientModalOpen, setClientModalOpen] = useState(false);
    const [clientSaving, setClientSaving] = useState(false);
    const [clientError, setClientError] = useState('');

    const [newClient, setNewClient] = useState({
        name: '',
        email: '',
        phone: '',
        address: '',
    });

    const [items, setItems] = useState([]);

    const [productModalOpen, setProductModalOpen] = useState(false);
    const [selectedProductId, setSelectedProductId] = useState('');
    const [selectedQuantity, setSelectedQuantity] = useState(1);

    const [paymentMethod, setPaymentMethod] = useState('cb');
    const [paymentReceived, setPaymentReceived] = useState(true);

    const [deliveryMethod, setDeliveryMethod] = useState('magasin');
    const [immediateHandover, setImmediateHandover] = useState(true);


    /*
     * Available payment methods depend on delivery method.
     */
    const paymentMethods = [
        {
            value: 'cb',
            label: 'Carte bancaire',
        },
        {
            value: 'virement',
            label: 'Virement',
        },
        {
            value: 'especes',
            label: 'Espèces',
            pickupOnly: true,
        },
        {
            value: 'cheque',
            label: 'Chèque',
            pickupOnly: true,
        },
    ];

    const availablePaymentMethods = paymentMethods.filter(
        method => (
            deliveryMethod === 'magasin'
            || !method.pickupOnly
        )
    );


    /*
     * Add a product to the sale.
     */
    const addProduct = () => {
        const product = products.find(
            product => String(product.id) === String(selectedProductId)
        );

        const quantity = Number(selectedQuantity);

        if (!product || quantity <= 0) {
            return;
        }

        const stock = Number(product.stock);

        setItems(currentItems => {
            const existingItem = currentItems.find(
                item => String(item.product_id) === String(product.id)
            );

            if (existingItem) {
                const newQuantity = existingItem.quantity + quantity;

                if (newQuantity > stock) {
                    return currentItems;
                }

                return currentItems.map(item => (
                    String(item.product_id) === String(product.id)
                        ? {
                            ...item,
                            quantity: newQuantity,
                        }
                        : item
                ));
            }

            if (quantity > stock) {
                return currentItems;
            }

            return [
                ...currentItems,
                {
                    product_id: product.id,
                    name: product.name,
                    quantity,
                    price: Number(product.price),
                    vat_rate: Number(product.vat_rate),
                    stock,
                },
            ];
        });

        setSelectedProductId('');
        setSelectedQuantity(1);
        setProductModalOpen(false);
    };


    /*
     * Update quantity without exceeding database stock
     * displayed when the page was loaded.
     *
     * Sale::create() will still verify real stock again.
     */
    const updateQuantity = (productId, quantity) => {
        const newQuantity = Number(quantity);

        if (newQuantity <= 0) {
            return;
        }

        setItems(currentItems => (
            currentItems.map(item => (
                String(item.product_id) === String(productId)
                    ? {
                        ...item,
                        quantity: Math.min(
                            newQuantity,
                            item.stock
                        ),
                    }
                    : item
            ))
        ));
    };


    const removeItem = productId => {
        setItems(currentItems => (
            currentItems.filter(
                item => String(item.product_id) !== String(productId)
            )
        ));
    };


    /*
     * Display-only totals.
     *
     * The backend recalculates all totals from MySQL before saving.
     */
    const totals = useMemo(() => {
        return items.reduce(
            (totals, item) => {
                const lineHt = item.price * item.quantity;

                const lineVat =
                    lineHt * (item.vat_rate / 100);

                totals.ht += lineHt;
                totals.vat += lineVat;
                totals.ttc += lineHt + lineVat;

                return totals;
            },
            {
                ht: 0,
                vat: 0,
                ttc: 0,
            }
        );
    }, [items]);


    /*
     * Changing to delivery can invalidate cash or cheque.
     */
    const changeDeliveryMethod = method => {
        setDeliveryMethod(method);

        if (
            method === 'livraison'
            && ['especes', 'cheque'].includes(paymentMethod)
        ) {
            setPaymentMethod('cb');
        }

        if (method === 'livraison') {
            setImmediateHandover(false);
        }
    };


    const createClient = async () => {
        if (!newClient.name.trim()) {
            setClientError('Le nom du client est obligatoire.');
            return;
        }

        setClientSaving(true);
        setClientError('');

        const formData = new FormData();

        formData.append('name', newClient.name);
        formData.append('email', newClient.email);
        formData.append('phone', newClient.phone);
        formData.append('address', newClient.address);

        try {
            const response = await fetch(
                '/public/index.php?route=/dashboard/clients/store-json',
                {
                    method: 'POST',
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.error || 'Impossible de créer le client.'
                );
            }

            const createdClient = data.client;


            /*
             * Add the newly-created client to the local select
             * without reloading the page.
             */
            setClientOptions(currentClients => (
                [...currentClients, createdClient].sort(
                    (clientA, clientB) => (
                        clientA.name.localeCompare(
                            clientB.name,
                            'fr'
                        )
                    )
                )
            ));


            /*
             * Automatically select the client we just created.
             */
            setClientId(String(createdClient.id));


            /*
             * Reset and close the modal.
             */
            setNewClient({
                name: '',
                email: '',
                phone: '',
                address: '',
            });

            setClientModalOpen(false);

        } catch (error) {
            setClientError(error.message);

        } finally {
            setClientSaving(false);
        }
    };

    return (
        <form
            method="POST"
            action="/public/index.php?route=/dashboard/sales/store"
            className="space-y-8"
        >

            {/* CLIENT */}
            <section className="rounded-3xl border border-black/10 bg-white p-6">
                <h2 className="text-xl font-black">
                    Client
                </h2>

                <select
                    name="client_id"
                    value={clientId}
                    onChange={event => setClientId(event.target.value)}
                    className="mt-4 w-full rounded-xl border border-black/20 px-4 py-3"
                >
                    <option value="">
                        Client non renseigné
                    </option>

                    {clientOptions.map(client => (
                        <option
                            key={client.id}
                            value={client.id}
                        >
                            {client.name}
                        </option>
                    ))}
                </select>

                <div className="mt-4">
                    <button
                        type="button"
                        onClick={() => {
                            setClientError('');
                            setClientModalOpen(true);
                        }}
                        className="
                            rounded-full
                            border border-black/20
                            px-4 py-2
                            text-sm font-bold
                            transition
                            hover:bg-black hover:text-white
                        "
                    >
                        + Nouveau client
                    </button>
                </div>
            </section>


            {/* PRODUCTS */}
            <section className="rounded-3xl border border-black/10 bg-white p-6">
                <div className="flex items-center justify-between gap-4">
                    <h2 className="text-xl font-black">
                        Produits
                    </h2>

                    <button
                        type="button"
                        onClick={() => setProductModalOpen(true)}
                        className="rounded-full bg-black px-4 py-2 font-bold text-white"
                    >
                        + Ajouter un produit
                    </button>
                </div>

                {items.length === 0 ? (
                    <p className="mt-6 text-neutral-500">
                        Aucun produit ajouté.
                    </p>
                ) : (
                    <div className="mt-6 space-y-3">
                        {items.map((item, index) => (
                            <div
                                key={item.product_id}
                                className="
                                    flex flex-wrap items-center
                                    justify-between gap-4
                                    rounded-2xl
                                    bg-neutral-100
                                    p-4
                                "
                            >
                                <div>
                                    <div className="font-bold">
                                        {item.name}
                                    </div>

                                    <div className="text-sm text-neutral-500">
                                        {item.price.toFixed(2)} € HT
                                        {' · '}
                                        TVA {item.vat_rate} %
                                    </div>
                                </div>

                                <div className="flex items-center gap-3">
                                    <input
                                        type="number"
                                        min="0.01"
                                        max={item.stock}
                                        step="0.01"
                                        value={item.quantity}
                                        onChange={event => (
                                            updateQuantity(
                                                item.product_id,
                                                event.target.value
                                            )
                                        )}
                                        className="w-24 rounded-xl border border-black/20 px-3 py-2"
                                    />

                                    <button
                                        type="button"
                                        onClick={() => removeItem(item.product_id)}
                                        className="font-bold text-red-600"
                                    >
                                        Supprimer
                                    </button>
                                </div>

                                <input
                                    type="hidden"
                                    name={`items[${index}][product_id]`}
                                    value={item.product_id}
                                />

                                <input
                                    type="hidden"
                                    name={`items[${index}][quantity]`}
                                    value={item.quantity}
                                />
                            </div>
                        ))}
                    </div>
                )}


                <div className="mt-8 border-t border-black/10 pt-6">
                    <div className="flex justify-between">
                        <span>Total HT</span>

                        <strong>
                            {totals.ht.toFixed(2)} €
                        </strong>
                    </div>

                    <div className="mt-2 flex justify-between">
                        <span>TVA</span>

                        <strong>
                            {totals.vat.toFixed(2)} €
                        </strong>
                    </div>

                    <div className="mt-4 flex justify-between text-xl">
                        <span className="font-black">
                            Total TTC
                        </span>

                        <strong>
                            {totals.ttc.toFixed(2)} €
                        </strong>
                    </div>
                </div>
            </section>


            {/* PAYMENT */}
            <section className="rounded-3xl border border-black/10 bg-white p-6">
                <h2 className="text-xl font-black">
                    Paiement
                </h2>

                <p className="mt-5 font-semibold">
                    Moyen de paiement
                </p>

                <div className="mt-3 flex flex-wrap gap-3">
                    {availablePaymentMethods.map(method => (
                        <label
                            key={method.value}
                            className={`
                                cursor-pointer rounded-full
                                border px-4 py-2
                                font-semibold
                                ${
                                paymentMethod === method.value
                                    ? 'border-black bg-black text-white'
                                    : 'border-black/20'
                            }
                            `}
                        >
                            <input
                                type="radio"
                                name="payment_method"
                                value={method.value}
                                checked={paymentMethod === method.value}
                                onChange={() => setPaymentMethod(method.value)}
                                className="sr-only"
                            />

                            {method.label}
                        </label>
                    ))}
                </div>


                <p className="mt-6 font-semibold">
                    Paiement reçu ?
                </p>

                <div className="mt-3 flex gap-3">
                    <label
                        className={`
                            cursor-pointer rounded-full
                            border px-5 py-2 font-bold
                            ${
                            paymentReceived
                                ? 'border-black bg-black text-white'
                                : 'border-black/20'
                        }
                        `}
                    >
                        <input
                            type="radio"
                            name="payment_received"
                            value="1"
                            checked={paymentReceived}
                            onChange={() => setPaymentReceived(true)}
                            className="sr-only"
                        />

                        Oui
                    </label>

                    <label
                        className={`
                            cursor-pointer rounded-full
                            border px-5 py-2 font-bold
                            ${
                            !paymentReceived
                                ? 'border-black bg-black text-white'
                                : 'border-black/20'
                        }
                        `}
                    >
                        <input
                            type="radio"
                            name="payment_received"
                            value="0"
                            checked={!paymentReceived}
                            onChange={() => setPaymentReceived(false)}
                            className="sr-only"
                        />

                        Non
                    </label>
                </div>
            </section>


            {/* DELIVERY */}
            <section className="rounded-3xl border border-black/10 bg-white p-6">
                <h2 className="text-xl font-black">
                    Remise de la commande
                </h2>

                <div className="mt-5 flex flex-wrap gap-3">
                    <label
                        className={`
                            cursor-pointer rounded-full
                            border px-5 py-3 font-bold
                            ${
                            deliveryMethod === 'magasin'
                                ? 'border-black bg-black text-white'
                                : 'border-black/20'
                        }
                        `}
                    >
                        <input
                            type="radio"
                            name="delivery_method"
                            value="magasin"
                            checked={deliveryMethod === 'magasin'}
                            onChange={() => changeDeliveryMethod('magasin')}
                            className="sr-only"
                        />

                        Retrait magasin
                    </label>

                    <label
                        className={`
                            cursor-pointer rounded-full
                            border px-5 py-3 font-bold
                            ${
                            deliveryMethod === 'livraison'
                                ? 'border-black bg-black text-white'
                                : 'border-black/20'
                        }
                        `}
                    >
                        <input
                            type="radio"
                            name="delivery_method"
                            value="livraison"
                            checked={deliveryMethod === 'livraison'}
                            onChange={() => changeDeliveryMethod('livraison')}
                            className="sr-only"
                        />

                        Livraison
                    </label>
                </div>


                {deliveryMethod === 'magasin' && (
                    <>
                        <p className="mt-6 font-semibold">
                            Le client prend les produits maintenant ?
                        </p>

                        <div className="mt-3 flex gap-3">
                            <label
                                className={`
                                    cursor-pointer rounded-full
                                    border px-5 py-2 font-bold
                                    ${
                                    immediateHandover
                                        ? 'border-black bg-black text-white'
                                        : 'border-black/20'
                                }
                                `}
                            >
                                <input
                                    type="radio"
                                    name="immediate_handover"
                                    value="1"
                                    checked={immediateHandover}
                                    onChange={() => setImmediateHandover(true)}
                                    className="sr-only"
                                />

                                Oui
                            </label>

                            <label
                                className={`
                                    cursor-pointer rounded-full
                                    border px-5 py-2 font-bold
                                    ${
                                    !immediateHandover
                                        ? 'border-black bg-black text-white'
                                        : 'border-black/20'
                                }
                                `}
                            >
                                <input
                                    type="radio"
                                    name="immediate_handover"
                                    value="0"
                                    checked={!immediateHandover}
                                    onChange={() => setImmediateHandover(false)}
                                    className="sr-only"
                                />

                                Non, retrait plus tard
                            </label>
                        </div>
                    </>
                )}
            </section>


            <button
                type="submit"
                disabled={items.length === 0}
                className="
                    w-full rounded-full
                    bg-black
                    px-6 py-4
                    text-lg font-black text-white
                    disabled:opacity-30
                "
            >
                Enregistrer la vente
            </button>


            {/* PRODUCT MODAL */}
            {productModalOpen && (
                <div
                    className="
                        fixed inset-0 z-50
                        flex items-center justify-center
                        bg-black/60 p-4
                        backdrop-blur-sm
                    "
                >
                    <div className="w-full max-w-lg rounded-3xl bg-white p-6">
                        <h2 className="text-xl font-black">
                            Ajouter un produit
                        </h2>

                        <select
                            value={selectedProductId}
                            onChange={event => setSelectedProductId(event.target.value)}
                            className="mt-5 w-full rounded-xl border border-black/20 px-4 py-3"
                        >
                            <option value="">
                                Choisir un produit
                            </option>

                            {products.map(product => (
                                <option
                                    key={product.id}
                                    value={product.id}
                                >
                                    {product.name}
                                    {' — '}
                                    {Number(product.stock)} en stock
                                </option>
                            ))}
                        </select>

                        <input
                            type="number"
                            min="0.01"
                            step="0.01"
                            value={selectedQuantity}
                            onChange={event => setSelectedQuantity(event.target.value)}
                            className="mt-4 w-full rounded-xl border border-black/20 px-4 py-3"
                            placeholder="Quantité"
                        />

                        <div className="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setProductModalOpen(false)}
                                className="rounded-full border border-black/20 px-4 py-2 font-bold"
                            >
                                Annuler
                            </button>

                            <button
                                type="button"
                                onClick={addProduct}
                                disabled={!selectedProductId}
                                className="
                                    rounded-full
                                    bg-black
                                    px-4 py-2
                                    font-bold text-white
                                    disabled:opacity-30
                                "
                            >
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            )}
            {/* NEW CLIENT MODAL */}
            {clientModalOpen && (
                <div
                    className="
                        fixed inset-0 z-50
                        flex items-center justify-center
                        bg-black/60 p-4
                        backdrop-blur-sm
                    "
                >
                    <div className="w-full max-w-lg rounded-3xl bg-white p-6">
                        <h2 className="text-xl font-black">
                            Nouveau client
                        </h2>

                        <div className="mt-6 space-y-4">
                            <div>
                                <label className="mb-1 block text-sm font-bold">
                                    Nom *
                                </label>

                                <input
                                    type="text"
                                    value={newClient.name}
                                    onChange={event => (
                                        setNewClient({
                                            ...newClient,
                                            name: event.target.value,
                                        })
                                    )}
                                    className="
                                        w-full rounded-xl
                                        border border-black/20
                                        px-4 py-3
                                    "
                                />
                            </div>


                            <div>
                                <label className="mb-1 block text-sm font-bold">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    value={newClient.email}
                                    onChange={event => (
                                        setNewClient({
                                            ...newClient,
                                            email: event.target.value,
                                        })
                                    )}
                                    className="
                                        w-full rounded-xl
                                        border border-black/20
                                        px-4 py-3
                                    "
                                />
                            </div>


                            <div>
                                <label className="mb-1 block text-sm font-bold">
                                    Téléphone
                                </label>

                                <input
                                    type="text"
                                    value={newClient.phone}
                                    onChange={event => (
                                        setNewClient({
                                            ...newClient,
                                            phone: event.target.value,
                                        })
                                    )}
                                    className="
                                        w-full rounded-xl
                                        border border-black/20
                                        px-4 py-3
                                    "
                                />
                            </div>


                            <div>
                                <label className="mb-1 block text-sm font-bold">
                                    Adresse
                                </label>

                                <textarea
                                    rows="3"
                                    value={newClient.address}
                                    onChange={event => (
                                        setNewClient({
                                            ...newClient,
                                            address: event.target.value,
                                        })
                                    )}
                                    className="
                                        w-full rounded-xl
                                        border border-black/20
                                        px-4 py-3
                                    "
                                />
                            </div>


                            {clientError && (
                                <p className="text-sm font-semibold text-red-600">
                                    {clientError}
                                </p>
                            )}
                        </div>


                        <div className="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                disabled={clientSaving}
                                onClick={() => setClientModalOpen(false)}
                                className="
                                    rounded-full
                                    border border-black/20
                                    px-4 py-2
                                    font-bold
                                "
                            >
                                Annuler
                            </button>

                            <button
                                type="button"
                                disabled={
                                    clientSaving
                                    || !newClient.name.trim()
                                }
                                onClick={createClient}
                                className="
                                    rounded-full
                                    bg-black
                                    px-5 py-2
                                    font-bold text-white
                                    disabled:opacity-30
                                "
                            >
                                {clientSaving
                                    ? 'Enregistrement...'
                                    : 'Créer le client'
                                }
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </form>
    );
}