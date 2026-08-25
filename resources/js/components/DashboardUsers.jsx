import React, {useState} from 'react';

const emptyUser = {
    id: null,
    name: '',
    email: '',
    password: '',
    role: 'vendeur',
    is_active: 1,
};

export default function DashboardUsers({
    users,
    currentUserId,
}) {
    const [userList, setUserList] = useState(users);

    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(false);

    const [user, setUser] = useState(emptyUser);

    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');

    const [deactivateUser, setDeactivateUser] = useState(null);


    const openCreateModal = () => {
        setUser(emptyUser);
        setEditing(false);
        setError('');
        setModalOpen(true);
    };


    const openEditModal = selectedUser => {
        setUser({
            id: selectedUser.id,
            name: selectedUser.name ?? '',
            email: selectedUser.email ?? '',
            password: '',
            role: selectedUser.role ?? 'vendeur',
            is_active: Number(selectedUser.is_active),
        });

        setEditing(true);
        setError('');
        setModalOpen(true);
    };


    const updateField = event => {
        const {name, value} = event.target;

        setUser(currentUser => ({
            ...currentUser,
            [name]: value,
        }));
    };


    const saveUser = async () => {
        if (
            !user.name.trim()
            || !user.email.trim()
            || (!editing && !user.password)
        ) {
            setError(
                'Veuillez remplir les champs obligatoires.'
            );

            return;
        }

        setSaving(true);
        setError('');

        const formData = new FormData();

        if (editing) {
            formData.append('id', user.id);
        }

        formData.append('name', user.name);
        formData.append('email', user.email);
        formData.append('password', user.password);
        formData.append('role', user.role);
        formData.append(
            'is_active',
            String(user.is_active)
        );

        const route = editing
            ? '/dashboard/users/update-json'
            : '/dashboard/users/store-json';

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
                    || 'Impossible d’enregistrer l’utilisateur.'
                );
            }

            if (editing) {
                setUserList(currentUsers => (
                    currentUsers.map(currentUser => (
                        String(currentUser.id)
                        === String(data.user.id)
                            ? data.user
                            : currentUser
                    ))
                ));

            } else {
                setUserList(currentUsers => (
                    [...currentUsers, data.user].sort(
                        (userA, userB) => (
                            userA.name.localeCompare(
                                userB.name,
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


    const confirmDeactivate = async () => {
        if (!deactivateUser) {
            return;
        }

        const formData = new FormData();

        formData.append(
            'id',
            deactivateUser.id
        );

        try {
            const response = await fetch(
                '/public/index.php?route=/dashboard/users/deactivate-json',
                {
                    method: 'POST',
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.error
                    || 'Impossible de désactiver l’utilisateur.'
                );
            }

            setUserList(currentUsers => (
                currentUsers.map(currentUser => (
                    String(currentUser.id)
                    === String(data.id)
                        ? {
                            ...currentUser,
                            is_active: 0,
                        }
                        : currentUser
                ))
            ));

            setDeactivateUser(null);

        } catch (error) {
            setError(error.message);
            setDeactivateUser(null);
        }
    };


    return (
        <>
            <div className="mb-6 flex items-center justify-between gap-4">
                <p className="text-neutral-500">
                    {userList.length} utilisateur
                    {userList.length > 1 ? 's' : ''}
                </p>

                <button
                    type="button"
                    onClick={openCreateModal}
                    className="
                        rounded-full bg-black
                        px-5 py-3
                        font-bold text-white
                    "
                >
                    + Nouvel utilisateur
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
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className="border-b border-black/10">
                            <tr
                                className="
                                    text-xs uppercase
                                    tracking-wide
                                    text-neutral-500
                                "
                            >
                                <th className="px-5 py-4">
                                    Utilisateur
                                </th>

                                <th className="px-5 py-4">
                                    Email
                                </th>

                                <th className="px-5 py-4">
                                    Rôle
                                </th>

                                <th className="px-5 py-4">
                                    Statut
                                </th>

                                <th className="px-5 py-4">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-black/5">
                            {userList.map(listUser => {
                                const isCurrentUser =
                                    Number(listUser.id)
                                    === Number(currentUserId);

                                return (
                                    <tr
                                        key={listUser.id}
                                        className="
                                            transition
                                            hover:bg-white/40
                                        "
                                    >
                                        <td className="px-5 py-4">
                                            <div className="font-black">
                                                {listUser.name}
                                            </div>

                                            <div className="mt-1 text-xs text-neutral-400">
                                                #{listUser.id}

                                                {isCurrentUser && (
                                                    <>
                                                        {' · '}
                                                        Vous
                                                    </>
                                                )}
                                            </div>
                                        </td>

                                        <td className="px-5 py-4">
                                            {listUser.email}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className="
                                                    inline-flex
                                                    rounded-full
                                                    bg-neutral-100
                                                    px-3 py-1
                                                    text-xs font-bold
                                                "
                                            >
                                                {listUser.role === 'admin'
                                                    ? 'Administrateur'
                                                    : 'Vendeur'
                                                }
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={`
                                                    inline-flex
                                                    rounded-full
                                                    px-3 py-1
                                                    text-xs font-bold
                                                    ${
                                                        Number(listUser.is_active)
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-red-100 text-red-700'
                                                    }
                                                `}
                                            >
                                                {Number(listUser.is_active)
                                                    ? 'Actif'
                                                    : 'Inactif'
                                                }
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => (
                                                        openEditModal(listUser)
                                                    )}
                                                    className="
                                                        rounded-full
                                                        border border-black/20
                                                        px-4 py-2
                                                        text-sm font-bold
                                                        hover:bg-black
                                                        hover:text-white
                                                    "
                                                >
                                                    Modifier
                                                </button>

                                                {Number(listUser.is_active)
                                                    && !isCurrentUser && (
                                                    <button
                                                        type="button"
                                                        onClick={() => (
                                                            setDeactivateUser(
                                                                listUser
                                                            )
                                                        )}
                                                        className="
                                                            rounded-full
                                                            border border-red-200
                                                            px-4 py-2
                                                            text-sm font-bold
                                                            text-red-700
                                                        "
                                                    >
                                                        Désactiver
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>


            {modalOpen && (
                <UserModal
                    user={user}
                    setUser={setUser}
                    editing={editing}
                    saving={saving}
                    error={error}
                    currentUserId={currentUserId}
                    updateField={updateField}
                    saveUser={saveUser}
                    close={() => setModalOpen(false)}
                />
            )}


            {deactivateUser && (
                <div
                    className="
                        fixed inset-0 z-[110]
                        flex items-center justify-center
                        bg-black/60 p-4
                        backdrop-blur-xl
                    "
                >
                    <div
                        className="
                            w-full max-w-md
                            rounded-[32px]
                            bg-white/90
                            p-7
                            shadow-2xl
                            backdrop-blur-3xl
                        "
                    >
                        <h2 className="text-2xl font-black">
                            Désactiver l’utilisateur ?
                        </h2>

                        <p className="mt-3 text-neutral-600">
                            {deactivateUser.name} ne pourra plus
                            se connecter au dashboard.
                        </p>

                        <div className="mt-7 flex justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setDeactivateUser(null)}
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
                                onClick={confirmDeactivate}
                                className="
                                    rounded-full
                                    bg-red-600
                                    px-5 py-3
                                    font-bold text-white
                                "
                            >
                                Désactiver
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}


function UserModal({
    user,
    setUser,
    editing,
    saving,
    error,
    currentUserId,
    updateField,
    saveUser,
    close,
}) {
    const isCurrentUser =
        Number(user.id) === Number(currentUserId);

    return (
        <div
            className="
                fixed inset-0 z-[100]
                flex items-center justify-center
                bg-black/60 p-4
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
                <h2
                    className="
                        text-3xl font-black
                        tracking-[-0.04em]
                    "
                >
                    {editing
                        ? 'Modifier l’utilisateur'
                        : 'Nouvel utilisateur'
                    }
                </h2>

                <div className="mt-7 space-y-4">
                    <Input
                        label="Nom *"
                        name="name"
                        value={user.name}
                        onChange={updateField}
                    />

                    <Input
                        label="Email *"
                        type="email"
                        name="email"
                        value={user.email}
                        onChange={updateField}
                    />

                    <Input
                        label={
                            editing
                                ? 'Nouveau mot de passe'
                                : 'Mot de passe *'
                        }
                        type="password"
                        name="password"
                        value={user.password}
                        onChange={updateField}
                    />

                    <div>
                        <label className="mb-1 block text-sm font-bold">
                            Rôle
                        </label>

                        <select
                            name="role"
                            value={user.role}
                            disabled={isCurrentUser}
                            onChange={updateField}
                            className="
                                w-full rounded-2xl
                                border border-black/10
                                bg-white/70
                                px-4 py-3
                                disabled:opacity-50
                            "
                        >
                            <option value="vendeur">
                                Vendeur
                            </option>

                            <option value="admin">
                                Administrateur
                            </option>
                        </select>
                    </div>


                    <label
                        className="
                            flex items-center
                            justify-between
                            rounded-2xl
                            border border-black/10
                            bg-white/60
                            px-4 py-3
                        "
                    >
                        <span className="font-bold">
                            Utilisateur actif
                        </span>

                        <input
                            type="checkbox"
                            checked={Number(user.is_active) === 1}
                            disabled={isCurrentUser}
                            onChange={event => (
                                setUser(currentUser => ({
                                    ...currentUser,
                                    is_active:
                                        event.target.checked ? 1 : 0,
                                }))
                            )}
                        />
                    </label>


                    {error && (
                        <div
                            className="
                                rounded-2xl
                                bg-red-50
                                px-4 py-3
                                text-sm font-semibold
                                text-red-700
                            "
                        >
                            {error}
                        </div>
                    )}
                </div>


                <div className="mt-7 flex justify-end gap-3">
                    <button
                        type="button"
                        disabled={saving}
                        onClick={close}
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
                        disabled={saving}
                        onClick={saveUser}
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
                            : 'Enregistrer'
                        }
                    </button>
                </div>
            </div>
        </div>
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
            <label className="mb-1 block text-sm font-bold">
                {label}
            </label>

            <input
                type={type}
                name={name}
                value={value}
                onChange={onChange}
                className="
                    w-full rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    focus:border-black/40
                    focus:bg-white
                "
            />
        </div>
    );
}