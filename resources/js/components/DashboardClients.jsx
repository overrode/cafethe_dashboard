import React, {useState} from 'react';

const emptyAddress = {
    address: '',
    postal_code: '',
    city: '',
};

const emptyClient = {
    id: null,
    name: '',
    email: '',
    phone: '',
    address: emptyAddress,
    favorites: '',
    abandoned_cart: '',
};


// Normalize old and new address formats.
const normalizeAddress = value => {
    if (!value) {
        return {...emptyAddress};
    }

    if (typeof value === 'object') {
        return {
            address: value.address ?? '',
            postal_code: value.postal_code ?? '',
            city: value.city ?? '',
        };
    }

    if (typeof value === 'string') {
        try {
            const parsed = JSON.parse(value);

            if (parsed && typeof parsed === 'object') {
                return {
                    address: parsed.address ?? '',
                    postal_code: parsed.postal_code ?? '',
                    city: parsed.city ?? '',
                };
            }
        } catch {
            return {
                ...emptyAddress,
                address: value,
            };
        }
    }

    return {...emptyAddress};
};


// Display the address nicely in the table.
const formatAddress = value => {
    const address = normalizeAddress(value);

    const cityLine = [
        address.postal_code,
        address.city,
    ]
        .filter(Boolean)
        .join(' ');

    return [
        address.address,
        cityLine,
    ]
        .filter(Boolean)
        .join(', ');
};


export default function DashboardClients({
    clients,
}) {
    const [clientList, setClientList] = useState(clients);

    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(false);

    const [client, setClient] = useState({
        ...emptyClient,
        address: {...emptyAddress},
    });

    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');


    // Open creation modal.
    const openCreateModal = () => {
        setClient({
            ...emptyClient,
            address: {...emptyAddress},
        });

        setEditing(false);
        setError('');
        setModalOpen(true);
    };


    // Open selected client.
    const openEditModal = selectedClient => {
        setClient({
            id: selectedClient.id,
            name: selectedClient.name ?? '',
            email: selectedClient.email ?? '',
            phone: selectedClient.phone ?? '',
            address: normalizeAddress(
                selectedClient.address
            ),
            favorites: selectedClient.favorites ?? '',
            abandoned_cart:
                selectedClient.abandoned_cart ?? '',
        });

        setEditing(true);
        setError('');
        setModalOpen(true);
    };


    // Update normal fields.
    const updateField = event => {
        const {
            name,
            value,
        } = event.target;

        setClient(currentClient => ({
            ...currentClient,
            [name]: value,
        }));
    };


    // Update address fields.
    const updateAddressField = event => {
        const {
            name,
            value,
        } = event.target;

        setClient(currentClient => ({
            ...currentClient,
            address: {
                ...currentClient.address,
                [name]: value,
            },
        }));
    };


    // Create or update client.
    const saveClient = async () => {
        if (!client.name.trim()) {
            setError(
                'Le nom du client est obligatoire.'
            );

            return;
        }

        setSaving(true);
        setError('');

        const formData = new FormData();

        if (editing) {
            formData.append('id', client.id);
        }

        formData.append('name', client.name);
        formData.append('email', client.email);
        formData.append('phone', client.phone);

        // Send address fields separately.
        formData.append(
            'address',
            client.address.address
        );

        formData.append(
            'postal_code',
            client.address.postal_code
        );

        formData.append(
            'city',
            client.address.city
        );

        formData.append(
            'favorites',
            client.favorites
        );

        formData.append(
            'abandoned_cart',
            client.abandoned_cart
        );

        const route = editing
            ? '/dashboard/clients/update-json'
            : '/dashboard/clients/store-json';

        try {
            const response = await fetch(
                `/public/index.php?route=${route}`,
                {
                    method: 'POST',
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.error
                    || 'Impossible d’enregistrer le client.'
                );
            }

            // Normalize returned address.
            const savedClient = {
                ...data.client,
                address: normalizeAddress(
                    data.client.address
                ),
            };

            if (editing) {
                setClientList(currentClients => (
                    currentClients.map(currentClient => (
                        String(currentClient.id)
                        === String(savedClient.id)
                            ? savedClient
                            : currentClient
                    ))
                ));

            } else {
                setClientList(currentClients => (
                    [
                        ...currentClients,
                        savedClient,
                    ].sort(
                        (clientA, clientB) => (
                            clientA.name.localeCompare(
                                clientB.name,
                                'fr'
                            )
                        )
                    )
                ));
            }

            setModalOpen(false);

        } catch (error) {
            setError(error.message);

        } finally {
            setSaving(false);
        }
    };


    return (
        <>
            {/* Page actions. */}
            <div
                className="
                    mb-6
                    flex items-center
                    justify-between gap-4
                "
            >
                <p className="text-neutral-500">
                    {clientList.length} client
                    {clientList.length > 1 ? 's' : ''}
                </p>

                <button
                    type="button"
                    onClick={openCreateModal}
                    className="
                        rounded-full
                        bg-black
                        px-5 py-3
                        font-bold text-white
                        transition
                        hover:-translate-y-0.5
                    "
                >
                    + Nouveau client
                </button>
            </div>


            {/* Clients table. */}
            <div
                className="
                    overflow-hidden
                    rounded-[28px]
                    border border-white/70
                    bg-white/40
                    shadow-[0_18px_45px_rgba(0,0,0,0.12)]
                    backdrop-blur-2xl
                "
            >
                {clientList.length === 0 ? (
                    <div className="p-10 text-center">
                        <p className="text-neutral-500">
                            Aucun client.
                        </p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead
                                className="
                                    border-b
                                    border-black/10
                                "
                            >
                                <tr
                                    className="
                                        text-xs
                                        uppercase
                                        tracking-wide
                                        text-neutral-500
                                    "
                                >
                                    <th className="px-5 py-4">
                                        Client
                                    </th>

                                    <th className="px-5 py-4">
                                        Contact
                                    </th>

                                    <th className="px-5 py-4">
                                        Adresse
                                    </th>

                                    <th className="px-5 py-4">
                                        Favoris
                                    </th>

                                    <th className="px-5 py-4">
                                        Panier abandonné
                                    </th>

                                    <th className="px-5 py-4">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                className="
                                    divide-y
                                    divide-black/5
                                "
                            >
                                {clientList.map(listClient => (
                                    <tr
                                        key={listClient.id}
                                        className="
                                            transition
                                            hover:bg-white/40
                                        "
                                    >
                                        <td className="px-5 py-4">
                                            <div className="font-black">
                                                {listClient.name}
                                            </div>

                                            <div
                                                className="
                                                    mt-1
                                                    text-xs
                                                    text-neutral-400
                                                "
                                            >
                                                #{listClient.id}
                                            </div>
                                        </td>


                                        <td className="px-5 py-4">
                                            <div>
                                                {listClient.email || '—'}
                                            </div>

                                            <div
                                                className="
                                                    mt-1
                                                    text-sm
                                                    text-neutral-500
                                                "
                                            >
                                                {listClient.phone || '—'}
                                            </div>
                                        </td>


                                        <td
                                            className="
                                                max-w-xs
                                                px-5 py-4
                                                text-sm
                                                text-neutral-600
                                            "
                                        >
                                            {formatAddress(
                                                listClient.address
                                            ) || '—'}
                                        </td>


                                        <td className="px-5 py-4">
                                            {listClient.favorites || '—'}
                                        </td>


                                        <td className="px-5 py-4">
                                            {listClient.abandoned_cart || '—'}
                                        </td>


                                        <td className="px-5 py-4">
                                            <button
                                                type="button"
                                                onClick={() => (
                                                    openEditModal(
                                                        listClient
                                                    )
                                                )}
                                                className="
                                                    rounded-full
                                                    border
                                                    border-black/20
                                                    px-4 py-2
                                                    text-sm
                                                    font-bold
                                                    transition
                                                    hover:bg-black
                                                    hover:text-white
                                                "
                                            >
                                                Modifier
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>


            {/* Create / edit modal. */}
            {modalOpen && (
                <div
                    className="
                        fixed inset-0 z-[100]
                        flex items-center
                        justify-center
                        overflow-y-auto
                        bg-black/60
                        p-4
                        backdrop-blur-xl
                    "
                >
                    <div
                        className="
                            my-8
                            w-full max-w-xl
                            rounded-[32px]
                            border border-white/50
                            bg-white/80
                            p-7
                            shadow-[0_30px_100px_rgba(0,0,0,0.35)]
                            backdrop-blur-3xl
                        "
                    >
                        {/* Modal heading. */}
                        <div
                            className="
                                flex items-start
                                justify-between gap-4
                            "
                        >
                            <div>
                                <p
                                    className="
                                        text-xs font-black
                                        uppercase
                                        tracking-[0.2em]
                                        text-neutral-500
                                    "
                                >
                                    CafThé
                                </p>

                                <h2
                                    className="
                                        mt-2
                                        text-3xl font-black
                                        tracking-[-0.04em]
                                    "
                                >
                                    {editing
                                        ? 'Modifier le client'
                                        : 'Nouveau client'
                                    }
                                </h2>
                            </div>

                            <button
                                type="button"
                                disabled={saving}
                                onClick={() => setModalOpen(false)}
                                className="
                                    flex h-10 w-10
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-black/5
                                    text-xl
                                    hover:bg-black
                                    hover:text-white
                                "
                            >
                                ×
                            </button>
                        </div>


                        {/* Client fields. */}
                        <div className="mt-7 space-y-4">
                            <Input
                                label="Nom *"
                                name="name"
                                value={client.name}
                                onChange={updateField}
                            />

                            <Input
                                label="Email"
                                name="email"
                                type="email"
                                value={client.email}
                                onChange={updateField}
                            />

                            <Input
                                label="Téléphone"
                                name="phone"
                                value={client.phone}
                                onChange={updateField}
                            />


                            {/* Address. */}
                            <div
                                className="
                                    rounded-3xl
                                    border border-black/10
                                    bg-white/40
                                    p-4
                                "
                            >
                                <p className="mb-4 font-black">
                                    Adresse
                                </p>

                                <div className="space-y-4">
                                    <Input
                                        label="Adresse"
                                        name="address"
                                        value={
                                            client.address.address
                                        }
                                        onChange={
                                            updateAddressField
                                        }
                                    />

                                    <div
                                        className="
                                            grid gap-4
                                            md:grid-cols-2
                                        "
                                    >
                                        <Input
                                            label="Code postal"
                                            name="postal_code"
                                            value={
                                                client.address
                                                    .postal_code
                                            }
                                            onChange={
                                                updateAddressField
                                            }
                                        />

                                        <Input
                                            label="Ville"
                                            name="city"
                                            value={
                                                client.address.city
                                            }
                                            onChange={
                                                updateAddressField
                                            }
                                        />
                                    </div>
                                </div>
                            </div>


                            <Input
                                label="Favoris"
                                name="favorites"
                                value={client.favorites}
                                onChange={updateField}
                            />

                            <Input
                                label="Panier abandonné"
                                name="abandoned_cart"
                                value={client.abandoned_cart}
                                onChange={updateField}
                            />


                            {/* API error. */}
                            {error && (
                                <div
                                    className="
                                        rounded-2xl
                                        bg-red-50
                                        px-4 py-3
                                        text-sm
                                        font-semibold
                                        text-red-700
                                    "
                                >
                                    {error}
                                </div>
                            )}
                        </div>


                        {/* Modal actions. */}
                        <div
                            className="
                                mt-7
                                flex justify-end gap-3
                            "
                        >
                            <button
                                type="button"
                                disabled={saving}
                                onClick={() => setModalOpen(false)}
                                className="
                                    rounded-full
                                    border border-black/20
                                    px-5 py-3
                                    font-bold
                                "
                            >
                                Annuler
                            </button>

                            <button
                                type="button"
                                disabled={
                                    saving
                                    || !client.name.trim()
                                }
                                onClick={saveClient}
                                className="
                                    rounded-full
                                    bg-black
                                    px-5 py-3
                                    font-bold text-white
                                    disabled:opacity-30
                                "
                            >
                                {saving
                                    ? 'Enregistrement...'
                                    : editing
                                        ? 'Enregistrer'
                                        : 'Créer le client'
                                }
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}


// Reusable form input.
function Input({
    label,
    name,
    type = 'text',
    value,
    onChange,
}) {
    return (
        <div>
            <label
                className="
                    mb-1 block
                    text-sm font-bold
                "
            >
                {label}
            </label>

            <input
                type={type}
                name={name}
                value={value}
                onChange={onChange}
                className="
                    w-full
                    rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    transition
                    focus:border-black/40
                    focus:bg-white
                "
            />
        </div>
    );
}