import React, {useEffect, useState} from 'react';

import {
    getCart,
    updateCartQuantity,
    removeFromCart,
    clearCart
} from '../cart.js';

export default function CartPage({imagesUrl}) {
    const [cart, setCart] = useState(getCart());

    useEffect(() => {
        const updateCart = () => {
            setCart(getCart());
        };

        window.addEventListener('cartUpdated', updateCart);

        return () => {
            window.removeEventListener('cartUpdated', updateCart);
        };
    }, []);

    const decreaseQuantity = (item) => {
        updateCartQuantity(
            item.id,
            Number(item.quantity) - 1
        );
    };

    const increaseQuantity = (item) => {
        updateCartQuantity(
            item.id,
            Number(item.quantity) + 1
        );
    };

    const total = cart.reduce(
        (sum, item) =>
            sum + Number(item.price) * Number(item.quantity),
        0
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
                            <img
                                src={`${imagesUrl}/${mainImage}`}
                                alt={item.name}
                                className="
                                    h-28 w-full
                                    rounded-2xl
                                    object-cover
                                    sm:w-28
                                "
                            />

                            <div className="min-w-0 flex-1">
                                <h2 className="text-xl font-bold">
                                    {item.name}
                                </h2>

                                <p className="mt-1 font-semibold text-neutral-600">
                                    {Number(item.price)
                                        .toFixed(2)
                                        .replace('.', ',')} €
                                </p>
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
                                    disabled={Number(item.quantity) <= 1}
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
                                    {(
                                        Number(item.price) *
                                        Number(item.quantity)
                                    )
                                        .toFixed(2)
                                        .replace('.', ',')} €
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
                        {cart.reduce(
                            (sum, item) =>
                                sum + Number(item.quantity),
                            0
                        )}
                    </strong>
                </div>

                <div
                    className="
                        mt-5 flex justify-between
                        border-t border-black/10
                        pt-5
                        text-xl
                    "
                >
                    <span>Total</span>

                    <strong>
                        {total.toFixed(2).replace('.', ',')} €
                    </strong>
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