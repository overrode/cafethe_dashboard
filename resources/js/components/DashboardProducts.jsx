import React, {useState} from 'react';

// Empty values used when creating a product.
const emptyProduct = {
    id: null,
    category_id: '',
    sku: '',
    name: '',
    description: '',
    sale_type: '',
    price: '',
    vat_rate: '',
    stock: '',
    image: '',
    origin: '',
    is_active: 1,
};

export default function DashboardProducts({
                                              products,
                                              categories,
                                          }) {

    // Search products by name, SKU or category.
    const [search, setSearch] = useState('');

    // Local list lets React update without reloading.
    const [productList, setProductList] = useState(products);

    // Same modal handles creation and editing.
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(false);
    const [product, setProduct] = useState(emptyProduct);

    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');

    // Product waiting for activation/deactivation confirmation.
    const [statusProduct, setStatusProduct] = useState(null);


    // Open an empty creation modal.
    const openCreateModal = () => {
        setProduct({
            ...emptyProduct,
            category_id: categories[0]?.id ?? '',
        });

        setEditing(false);
        setError('');
        setModalOpen(true);
    };


    // Open the selected product in edit mode.
    const openEditModal = selectedProduct => {
        setProduct({
            id: selectedProduct.id,
            category_id: selectedProduct.category_id ?? '',
            sku: selectedProduct.sku ?? '',
            name: selectedProduct.name ?? '',
            description: selectedProduct.description ?? '',
            sale_type: selectedProduct.sale_type ?? '',
            price: selectedProduct.price ?? '',
            vat_rate: selectedProduct.vat_rate ?? '',
            stock: selectedProduct.stock ?? '',
            image: selectedProduct.image ?? '',
            origin: selectedProduct.origin ?? '',
            is_active: Number(selectedProduct.is_active),
        });

        setEditing(true);
        setError('');
        setModalOpen(true);
    };


    // Synchronize normal inputs with React state.
    const updateField = event => {
        const {name, value} = event.target;

        setProduct(currentProduct => ({
            ...currentProduct,
            [name]: value,
        }));
    };


    // Create or update the product.
    const saveProduct = async () => {
        if (
            !product.category_id
            || !product.sku.trim()
            || !product.name.trim()
            || !product.sale_type.trim()
        ) {
            setError(
                'Veuillez remplir les champs obligatoires.'
            );

            return;
        }

        setSaving(true);
        setError('');

        // Build the POST body expected by PHP.
        const formData = new FormData();

        if (editing) {
            formData.append('id', product.id);
        }

        formData.append(
            'category_id',
            product.category_id
        );

        formData.append('sku', product.sku);
        formData.append('name', product.name);
        formData.append(
            'description',
            product.description
        );
        formData.append(
            'sale_type',
            product.sale_type
        );
        formData.append('price', product.price);
        formData.append(
            'vat_rate',
            product.vat_rate
        );
        formData.append('stock', product.stock);
        formData.append('image', product.image);
        formData.append('origin', product.origin);

        formData.append(
            'is_active',
            String(product.is_active)
        );

        // Choose create or update endpoint.
        const route = editing
            ? '/dashboard/products/update-json'
            : '/dashboard/products/store-json';

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
                    || 'Impossible d’enregistrer le produit.'
                );
            }

            // Update the existing table row.
            if (editing) {
                setProductList(currentProducts => (
                    currentProducts.map(currentProduct => (
                        String(currentProduct.id)
                        === String(data.product.id)
                            ? data.product
                            : currentProduct
                    ))
                ));

            } else {
                // Add the new product at the top.
                setProductList(currentProducts => (
                    [
                        data.product,
                        ...currentProducts,
                    ]
                ));
            }

            setModalOpen(false);

        } catch (error) {
            setError(error.message);

        } finally {
            setSaving(false);
        }
    };


    // Activate or deactivate a product.
    const changeProductStatus = async () => {
        if (!statusProduct) {
            return;
        }

        const newStatus =
            Number(statusProduct.is_active) ? 0 : 1;

        const formData = new FormData();

        formData.append('id', statusProduct.id);
        formData.append(
            'is_active',
            String(newStatus)
        );

        try {
            const response = await fetch(
                '/public/index.php?route=/dashboard/products/set-active-json',
                {
                    method: 'POST',
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.error
                    || 'Impossible de modifier le statut.'
                );
            }

            // Update the status locally.
            setProductList(currentProducts => (
                currentProducts.map(currentProduct => (
                    String(currentProduct.id)
                    === String(data.id)
                        ? {
                            ...currentProduct,
                            is_active: data.is_active,
                        }
                        : currentProduct
                ))
            ));

            setStatusProduct(null);

        } catch (error) {
            setError(error.message);
            setStatusProduct(null);
        }
    };


    // Build a usable product image URL.
    const getImageUrl = image => {
        if (!image) {
            return null;
        }

        if (
            image.startsWith('http://')
            || image.startsWith('https://')
            || image.startsWith('/')
        ) {
            return image;
        }

        return `/public/assets/images/products/${image}`;
    };

    // Filter the table without reloading the page.
    const filteredProducts = productList.filter(product => {
        const query = search.trim().toLowerCase();

        if (!query) {
            return true;
        }

        return (
            product.name?.toLowerCase().includes(query)
            || product.sku?.toLowerCase().includes(query)
            || product.category_name?.toLowerCase().includes(query)
        );
    });

    return (
        <>
            {/* PAGE ACTIONS */}
            <div
                className="
                    mb-6 flex items-center
                    justify-between gap-4
                "
            >
                <p className="text-neutral-500">
                    {productList.length} produit
                    {productList.length !== 1 ? 's' : ''}
                </p>

                <input
                    type="search"
                    value={search}
                    onChange={event => setSearch(event.target.value)}
                    placeholder="Rechercher un produit..."
                    className="
                        w-full max-w-sm
                        rounded-full
                        border border-black/10
                        bg-white/60
                        px-4 py-3
                        outline-none
                        backdrop-blur-xl
                        focus:border-black/30
                    "
                />

                <button
                    type="button"
                    onClick={openCreateModal}
                    className="
                        rounded-full bg-black
                        px-5 py-3
                        font-bold text-white
                        transition
                        hover:-translate-y-0.5
                    "
                >
                    + Nouveau produit
                </button>
            </div>


            {/* PRODUCTS TABLE */}
            {/* PRODUCTS TABLE */}
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
                            {/* Product identifiers. */}
                            <th className="px-5 py-4">
                                ID
                            </th>

                            <th className="px-5 py-4">
                                SKU
                            </th>

                            <th className="px-5 py-4">
                                Produit
                            </th>

                            <th className="px-5 py-4">
                                Catégorie
                            </th>

                            <th className="px-5 py-4">
                                Type
                            </th>

                            <th className="px-5 py-4 text-right">
                                Prix HT
                            </th>

                            <th className="px-5 py-4 text-right">
                                TVA
                            </th>

                            <th className="px-5 py-4 text-right">
                                Stock
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
                        {filteredProducts.map(listProduct => {
                            const imageUrl = getImageUrl(
                                listProduct.image
                            );

                            return (
                                <tr
                                    key={listProduct.id}
                                    className="
                                transition
                                hover:bg-white/40
                            "
                                >
                                    {/* Product ID. */}
                                    <td className="px-5 py-4 font-bold text-neutral-500">
                                        #{listProduct.id}
                                    </td>

                                    {/* Product SKU. */}
                                    <td className="px-5 py-4">
                                <span
                                    className="
                                        whitespace-nowrap
                                        rounded-lg
                                        bg-black/5
                                        px-2 py-1
                                        font-mono
                                        text-xs font-bold
                                    "
                                >
                                    {listProduct.sku}
                                </span>
                                    </td>

                                    {/* Product name and image. */}
                                    <td className="px-5 py-4">
                                        <div className="flex items-center gap-3">
                                            <div
                                                className="
                                            flex h-12 w-12
                                            shrink-0
                                            items-center
                                            justify-center
                                            overflow-hidden
                                            rounded-2xl
                                            bg-black/5
                                            font-black
                                        "
                                            >
                                                {imageUrl ? (
                                                    <img
                                                        src={imageUrl}
                                                        alt=""
                                                        className="
                                                    h-full w-full
                                                    object-cover
                                                "
                                                    />
                                                ) : (
                                                    listProduct.name
                                                        ?.charAt(0)
                                                        .toUpperCase()
                                                )}
                                            </div>

                                            <div>
                                                <p className="font-black">
                                                    {listProduct.name}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {/* Category. */}
                                    <td className="px-5 py-4">
                                        {listProduct.category_name}
                                    </td>

                                    {/* Friendly sale type. */}
                                    <td className="px-5 py-4">
                                <span
                                    className="
                                        inline-flex
                                        rounded-full
                                        bg-black/5
                                        px-3 py-1
                                        text-xs font-bold
                                    "
                                >
                                    {listProduct.sale_type === 'poids'
                                        ? 'Au poids'
                                        : 'À l’unité'
                                    }
                                </span>
                                    </td>

                                    {/* Price. */}
                                    <td className="px-5 py-4 text-right font-bold">
                                        {Number(
                                            listProduct.price
                                        ).toFixed(2)} €
                                    </td>

                                    {/* VAT. */}
                                    <td className="px-5 py-4 text-right">
                                        {Number(
                                            listProduct.vat_rate
                                        )} %
                                    </td>

                                    {/* Highlight products that are running low on stock. */}
                                    <td className="px-5 py-4 text-right">
                                        <span
                                            className={`
                                                inline-flex rounded-full
                                                px-3 py-1
                                                text-sm font-black
                                                ${
                                                    Number(listProduct.stock) <= 5
                                                        ? 'bg-red-100 text-red-700'
                                                        : 'bg-black/5 text-black'
                                                }
                                            `}
                                        >
                                            {Number(listProduct.stock)}
                                        </span>
                                    </td>

                                    {/* Active status. */}
                                    <td className="px-5 py-4">
                                <span
                                    className={`
                                        inline-flex
                                        rounded-full
                                        px-3 py-1
                                        text-xs font-bold
                                        ${
                                        Number(listProduct.is_active)
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-700'
                                    }
                                    `}
                                >
                                    {Number(listProduct.is_active)
                                        ? 'Actif'
                                        : 'Inactif'
                                    }
                                </span>
                                    </td>

                                    {/* Product actions. */}
                                    <td className="px-5 py-4">
                                        <div className="flex gap-2">
                                            <button
                                                type="button"
                                                onClick={() => (
                                                    openEditModal(
                                                        listProduct
                                                    )
                                                )}
                                                className="
                                            rounded-full
                                            border border-black/20
                                            px-4 py-2
                                            text-sm font-bold
                                            transition
                                            hover:bg-black
                                            hover:text-white
                                        "
                                            >
                                                Modifier
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() => (
                                                    setStatusProduct(
                                                        listProduct
                                                    )
                                                )}
                                                className={`
                                            rounded-full
                                            border
                                            px-4 py-2
                                            text-sm font-bold
                                            ${
                                                    Number(listProduct.is_active)
                                                        ? 'border-red-200 text-red-700'
                                                        : 'border-green-200 text-green-700'
                                                }
                                        `}
                                            >
                                                {Number(listProduct.is_active)
                                                    ? 'Désactiver'
                                                    : 'Activer'
                                                }
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            );
                        })}
                        </tbody>
                    </table>
                </div>
            </div>


            {/* CREATE / EDIT MODAL */}
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
                            my-8 w-full max-w-3xl
                            rounded-[32px]
                            border border-white/50
                            bg-white/85
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
                                        mt-2 text-3xl
                                        font-black
                                        tracking-[-0.04em]
                                    "
                                >
                                    {editing
                                        ? 'Modifier le produit'
                                        : 'Nouveau produit'
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


                        {/* Main product fields. */}
                        <div
                            className="
                                mt-7 grid gap-4
                                md:grid-cols-2
                            "
                        >
                            <Input
                                label="Nom *"
                                name="name"
                                value={product.name}
                                onChange={updateField}
                            />

                            <Input
                                label="SKU *"
                                name="sku"
                                value={product.sku}
                                onChange={updateField}
                            />

                            <div>
                                <label className="mb-1 block text-sm font-bold">
                                    Catégorie *
                                </label>

                                <select
                                    name="category_id"
                                    value={product.category_id}
                                    onChange={updateField}
                                    className="
                                        w-full rounded-2xl
                                        border border-black/10
                                        bg-white/70
                                        px-4 py-3
                                    "
                                >
                                    {categories.map(category => (
                                        <option
                                            key={category.id}
                                            value={category.id}
                                        >
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Sale type controls how quantities are handled. */}
                            <div>
                                <label className="mb-1 block text-sm font-bold">
                                    Type de vente *
                                </label>

                                <select
                                    name="sale_type"
                                    value={product.sale_type}
                                    onChange={updateField}
                                    className="
                                        w-full rounded-2xl
                                        border border-black/10
                                        bg-white/70
                                        px-4 py-3
                                        outline-none
                                        transition
                                        focus:border-black/40
                                        focus:bg-white
                                    "
                                >
                                    <option value="">
                                        Choisir un type
                                    </option>

                                    <option value="poids">
                                        Au poids
                                    </option>

                                    <option value="unite">
                                        À l'unité
                                    </option>
                                </select>
                            </div>

                            <Input
                                label="Prix HT *"
                                name="price"
                                type="number"
                                step="0.01"
                                min="0"
                                value={product.price}
                                onChange={updateField}
                            />

                            <Input
                                label="TVA (%) *"
                                name="vat_rate"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                value={product.vat_rate}
                                onChange={updateField}
                            />

                            <Input
                                label="Stock *"
                                name="stock"
                                type="number"
                                step="0.01"
                                min="0"
                                value={product.stock}
                                onChange={updateField}
                            />

                            <Input
                                label="Origine"
                                name="origin"
                                value={product.origin}
                                onChange={updateField}
                            />

                            <Input
                                label="Image"
                                name="image"
                                value={product.image}
                                onChange={updateField}
                            />

                            {/* Preview the selected product image. */}
                            {product.image && (
                                <div className="md:col-span-2">
                                    <p className="mb-2 text-sm font-bold">
                                        Aperçu
                                    </p>

                                    <div
                                        className="
                                            h-40 w-40
                                            overflow-hidden
                                            rounded-3xl
                                            border border-black/10
                                            bg-black/5
                                        "
                                    >
                                        <img
                                            src={getImageUrl(product.image)}
                                            alt={product.name || 'Produit'}
                                            className="
                                                h-full w-full
                                                object-cover
                                            "
                                        />
                                    </div>
                                </div>
                            )}
                        </div>


                        {/* Product description. */}
                        <div className="mt-4">
                            <label className="mb-1 block text-sm font-bold">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                value={product.description}
                                onChange={updateField}
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


                        {/* Active status. */}
                        <label
                            className="
                                mt-4 flex items-center
                                justify-between
                                rounded-2xl
                                border border-black/10
                                bg-white/60
                                px-4 py-3
                            "
                        >
                            <div>
                                <p className="font-bold">
                                    Produit actif
                                </p>

                                <p className="text-xs text-neutral-500">
                                    Visible et disponible à la vente.
                                </p>
                            </div>

                            <input
                                type="checkbox"
                                checked={
                                    Number(product.is_active) === 1
                                }
                                onChange={event => (
                                    setProduct(currentProduct => ({
                                        ...currentProduct,
                                        is_active:
                                            event.target.checked
                                                ? 1
                                                : 0,
                                    }))
                                )}
                            />
                        </label>


                        {/* API errors. */}
                        {error && (
                            <div
                                className="
                                    mt-5 rounded-2xl
                                    bg-red-50
                                    px-4 py-3
                                    text-sm font-semibold
                                    text-red-700
                                "
                            >
                                {error}
                            </div>
                        )}


                        {/* Modal actions. */}
                        <div className="mt-7 flex justify-end gap-3">
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
                                disabled={saving}
                                onClick={saveProduct}
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
                                        : 'Créer le produit'
                                }
                            </button>
                        </div>
                    </div>
                </div>
            )}


            {/* STATUS CONFIRMATION */}
            {statusProduct && (
                <div
                    className="
                        fixed inset-0 z-[110]
                        flex items-center
                        justify-center
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
                            {Number(statusProduct.is_active)
                                ? 'Désactiver le produit ?'
                                : 'Activer le produit ?'
                            }
                        </h2>

                        <p className="mt-3 text-neutral-600">
                            {statusProduct.name}
                        </p>

                        <div className="mt-7 flex justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setStatusProduct(null)}
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
                                onClick={changeProductStatus}
                                className="
                                    rounded-full
                                    bg-black
                                    px-5 py-3
                                    font-bold text-white
                                "
                            >
                                Confirmer
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}


// Reusable input used throughout the modal.
function Input({
                   label,
                   name,
                   type = 'text',
                   value,
                   onChange,
                   ...props
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
                {...props}
                className="
                    w-full rounded-2xl
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