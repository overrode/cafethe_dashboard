import React, {useState} from 'react';

const emptyClient = {
    id: null,
    name: '',
    email: '',
    phone: '',
    address: '',
    favorites: '',
    abandoned_cart: '',
};

export default function DashboardClients({
    clients,
}) {
    const [clientList, setClientList] = useState(clients);

    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(false);

    const [client, setClient] = useState(emptyClient);

    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');


    const openCreateModal = () => {
        setClient(emptyClient);
        setEditing(false);
        setError('');
        setModalOpen(true);
    };


    const openEditModal = selectedClient => {
        setClient({
            id: selectedClient.id,
            name: selectedClient.name ?? '',
            email: selectedClient.email ?? '',
            phone: selectedClient.phone ?? '',
            address: selectedClient.address ?? '',
            favorites: selectedClient.favorites ?? '',
            abandoned_cart:
                selectedClient.abandoned_cart ?? '',
        });

        setEditing(true);
        setError('');
        setModalOpen(true);
    };


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
        formData.append('address', client.address);
        formData.append('favorites', client.favorites);
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

            if (editing) {
                setClientList(currentClients => (
                    currentClients.map(currentClient => (
                        String(currentClient.id)
                        === String(data.client.id)
                            ? data.client
                            : currentClient
                    ))
                ));

            } else {
                setClientList(currentClients => (
                    [...currentClients, data.client].sort(
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
                                {clientList.map(client => (
                                    <tr
                                        key={client.id}
                                        className="
                                            transition
                                            hover:bg-white/40
                                        "
                                    >
                                        <td className="px-5 py-4">
                                            <div className="font-black">
                                                {client.name}
                                            </div>

                                            <div
                                                className="
                                                    mt-1
                                                    text-xs
                                                    text-neutral-400
                                                "
                                            >
                                                #{client.id}
                                            </div>
                                        </td>


                                        <td className="px-5 py-4">
                                            <div>
                                                {client.email || '—'}
                                            </div>

                                            <div
                                                className="
                                                    mt-1
                                                    text-sm
                                                    text-neutral-500
                                                "
                                            >
                                                {client.phone || '—'}
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
                                            {client.address || '—'}
                                        </td>


                                        <td className="px-5 py-4">
                                            {client.favorites || '—'}
                                        </td>


                                        <td className="px-5 py-4">
                                            {client.abandoned_cart || '—'}
                                        </td>


                                        <td className="px-5 py-4">
                                            <button
                                                type="button"
                                                onClick={() => (
                                                    openEditModal(client)
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


            {modalOpen && (
                <div
                    className="
                        fixed inset-0 z-[100]
                        flex items-center
                        justify-center
                        bg-black/60
                        p-4
                        backdrop-blur-xl
                    "
                >
                    <div
                        className="
                            w-full max-w-xl
                            rounded-[32px]
                            border border-white/50
                            bg-white/80
                            p-7
                            shadow-[0_30px_100px_rgba(0,0,0,0.35)]
                            backdrop-blur-3xl
                        "
                    >
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

                            <div>
                                <label
                                    className="
                                        mb-1 block
                                        text-sm font-bold
                                    "
                                >
                                    Adresse
                                </label>

                                <textarea
                                    name="address"
                                    rows="3"
                                    value={client.address}
                                    onChange={updateField}
                                    className="
                                        w-full
                                        rounded-2xl
                                        border border-black/10
                                        bg-white/70
                                        px-4 py-3
                                        outline-none
                                        focus:border-black/40
                                        focus:bg-white
                                    "
                                />
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