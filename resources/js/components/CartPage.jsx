import React, {useEffect, useState} from 'react';

import {
    getCart,
    updateCartQuantity,
    removeFromCart,
    clearCart
} from '../cart.js';

export default function CartPage({imagesUrl, weightStep}) {
    const [cart, setCart] = useState(getCart());

    // Calculate TTC price.
    const getTtcPrice = item => {
        const priceHt = Number(item.price);
        const vatRate = Number(item.vat_rate ?? 0);

        return priceHt * (1 + vatRate / 100);
    };

    // Calculate cart TTC total.
    const totalTtc = cart.reduce(
        (sum, item) => (
            sum
            + getTtcPrice(item) * Number(item.quantity)
        ),
        0
    );

    useEffect(() => {
        const updateCart = () => {
            setCart(getCart());
        };

        window.addEventListener('cartUpdated', updateCart);

        return () => {
            window.removeEventListener('cartUpdated', updateCart);
        };
    }, []);

    // Calculate trusted display totals for one cart line.
    const getItemTotals = item => {
        const priceHt = Number(item.price);
        const vatRate = Number(item.vat_rate ?? 0);
        const quantity = Number(item.quantity);

        const quantityForPrice =
            item.sale_type === 'poids'
                ? quantity / 1000
                : quantity;

        const unitVat = priceHt * (vatRate / 100);
        const unitTtc = priceHt + unitVat;

        const lineHt = priceHt * quantityForPrice;
        const lineVat = lineHt * (vatRate / 100);
        const lineTtc = lineHt + lineVat;

        return {
            unitTtc,
            lineHt,
            lineVat,
            lineTtc,
        };
    };

    const decreaseQuantity = (item) => {
        const step = item.sale_type === 'poids' ? weightStep : 1;

        updateCartQuantity(
            item.id,
            Number(item.quantity) - step
        );
    };

    const increaseQuantity = (item) => {
        const step = item.sale_type === 'poids' ? weightStep : 1;

        updateCartQuantity(
            item.id,
            Number(item.quantity) + step
        );
    };

    // Calculate complete cart totals.
    const totals = cart.reduce(
        (result, item) => {
            const itemTotals = getItemTotals(item);

            result.ht += itemTotals.lineHt;
            result.vat += itemTotals.lineVat;
            result.ttc += itemTotals.lineTtc;

            return result;
        },
        {
            ht: 0,
            vat: 0,
            ttc: 0,
        }
    );

    if (cart.length === 0) {
        return (
            <div
                className="
                    rounded-[30px]
                    border border-white/70
                    bg-white/40
                    p-10 text-center
                    shadow-lg
                    backdrop-blur-2xl
                "
            >
                <h2 className="text-3xl font-black">
                    Votre panier est vide
                </h2>

                <p className="mt-3 text-neutral-600">
                    Ajoutez quelques produits pour commencer.
                </p>

                <a
                    href="/public/index.php?route=/products"
                    className="
                        mt-8 inline-block
                        rounded-full bg-black
                        px-6 py-3
                        font-bold text-white
                        transition
                        hover:-translate-y-0.5
                    "
                >
                    Voir les produits
                </a>
            </div>
        );
    }

    return (
        <div className="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div className="space-y-4">
                {cart.map(item => {
                    const mainImage = item.image
                        ? item.image.split(';')[0]
                        : 'placeholder.jpg';

                    const itemTotals = getItemTotals(item);

                    return (
                        <article
                            key={item.id}
                            className="
                                flex flex-col gap-5
                                rounded-[28px]
                                border border-white/70
                                bg-white/40
                                p-5
                                shadow-md
                                backdrop-blur-2xl
                                sm:flex-row
                                sm:items-center
                            "
                        >
                            <a
                                href={`/public/index.php?route=/product&id=${item.id}`}
                                className="shrink-0"
                            >
                                <img
                                    src={`${imagesUrl}/${mainImage}`}
                                    alt={item.name}
                                    className="
                                        h-28 w-full
                                        rounded-2xl
                                        object-cover
                                        transition
                                        hover:opacity-80
                                        sm:w-28
                                    "
                                />
                            </a>

                            <div className="min-w-0 flex-1">
                                <h2 className="text-xl font-bold">
                                    <a
                                        href={`/public/index.php?route=/product&id=${item.id}`}
                                        className="hover:underline"
                                    >
                                        {item.name}
                                    </a>
                                </h2>

                                <div className="mt-1">
                                    <p className="font-semibold text-neutral-700">
                                        {itemTotals.unitTtc
                                            .toFixed(2)
                                            .replace('.', ',')} €
                                        {item.sale_type === 'poids'
                                            ? ' / kg TTC'
                                            : ' TTC'}
                                    </p>

                                    <p className="text-sm text-neutral-500">
                                        TVA {Number(item.vat_rate)
                                            .toFixed(1)
                                            .replace('.', ',')
                                            } %
                                        {' : '}
                                        {itemTotals.lineVat
                                            .toFixed(2)
                                            .replace('.', ',')
                                        } €
                                    </p>
                                </div>
                            </div>

                            <div
                                className="
                                    inline-flex items-center gap-3
                                    rounded-full
                                    border border-white/70
                                    bg-white/50
                                    px-2 py-2
                                "
                            >
                                <button
                                    type="button"
                                    onClick={() => decreaseQuantity(item)}
                                    disabled={
                                        Number(item.quantity) <= (item.sale_type === 'poids' ? weightStep : 1)
                                    }
                                    className="
                                        flex h-9 w-9
                                        items-center justify-center
                                        rounded-full
                                        bg-black
                                        font-bold text-white
                                        disabled:opacity-30
                                    "
                                >
                                    −
                                </button>

                                <span className="min-w-8 text-center font-bold">
                                    {item.quantity}
                                    {item.sale_type === 'poids' ? ' g' : ''}
                                </span>

                                <button
                                    type="button"
                                    onClick={() => increaseQuantity(item)}
                                    disabled={
                                        Number(item.quantity) >=
                                        Number(item.stock)
                                    }
                                    className="
                                        flex h-9 w-9
                                        items-center justify-center
                                        rounded-full
                                        bg-black
                                        font-bold text-white
                                        disabled:opacity-30
                                    "
                                >
                                    +
                                </button>
                            </div>

                            <div className="min-w-24 text-right">
                                <strong className="text-lg">
                                    {itemTotals.lineTtc
                                        .toFixed(2)
                                        .replace('.', ',')
                                    } €
                                </strong>

                                <button
                                    type="button"
                                    onClick={() => removeFromCart(item.id)}
                                    className="
                                        mt-2 block w-full
                                        text-sm font-semibold
                                        text-neutral-500
                                        hover:text-black
                                    "
                                >
                                    Supprimer
                                </button>
                            </div>
                        </article>
                    );
                })}
            </div>

            <aside
                className="
                    h-fit
                    rounded-[30px]
                    border border-white/70
                    bg-white/40
                    p-7
                    shadow-lg
                    backdrop-blur-2xl
                "
            >
                <h2 className="text-2xl font-black">
                    Récapitulatif
                </h2>

                <div className="mt-6 flex justify-between">
                    <span>Articles</span>

                    <strong>
                        {cart.length}
                    </strong>
                </div>

                <div className="mt-5 space-y-3 border-t border-black/10 pt-5">
                    <div className="flex justify-between">
                        <span className="text-neutral-500">
                            Total HT
                        </span>

                        <strong>
                            {totals.ht
                                .toFixed(2)
                                .replace('.', ',')} €
                        </strong>
                    </div>

                    <div className="flex justify-between">
                        <span className="text-neutral-500">
                            TVA
                        </span>

                        <strong>
                            {totals.vat
                                .toFixed(2)
                                .replace('.', ',')} €
                        </strong>
                    </div>

                    <div
                        className="
                            flex justify-between
                            border-t border-black/10
                            pt-4 text-xl
                        "
                    >
                        <span className="font-bold">
                            Total TTC
                        </span>

                        <strong>
                            {totals.ttc
                                .toFixed(2)
                                .replace('.', ',')} €
                        </strong>
                    </div>
                </div>

                <form
                    method="POST"
                    action="/public/index.php?route=/checkout"
                >
                    <input
                        type="hidden"
                        name="items"
                        value={JSON.stringify(
                            cart.map(item => ({
                                product_id: item.id,
                                quantity: item.quantity
                            }))
                        )}
                    />

                    <button
                        type="submit"
                        className="
                            mt-8 w-full
                            rounded-full
                            bg-black
                            px-6 py-4
                            font-bold text-white
                            transition
                            hover:-translate-y-0.5
                        "
                    >
                        Commander
                    </button>
                </form>

                <button
                    type="button"
                    onClick={clearCart}
                    className="
                        mt-4 w-full
                        text-sm font-semibold
                        text-neutral-500
                        hover:text-black
                    "
                >
                    Vider le panier
                </button>
            </aside>
        </div>
    );
}