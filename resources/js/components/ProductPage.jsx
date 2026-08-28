import React, {useState} from 'react';
import {addToCart} from '../cart.js';

export default function ProductPage({product, imagesUrl, weightStep}) {
    const images = product.image
        ? product.image.split(';').filter(Boolean)
        : [];

    const [activeImage, setActiveImage] = useState(
        images[0] ?? 'placeholder.jpg'
    );

    const isWeighted = product.sale_type === 'poids';
    const quantityStep = isWeighted ? weightStep : 1;
    const minimumQuantity = isWeighted ? weightStep : 1;
    const [quantity, setQuantity] = useState(minimumQuantity);
    const vatRate = Number(product.vat_rate);
    const priceHt = Number(product.price);
    const priceTtc = priceHt * (1 + vatRate / 100);
    // Weighted quantity is stored in grams,
    // but the product price is per kilogram.
    const quantityForPrice = isWeighted
        ? quantity / 1000
        : quantity;
    const totalTtc = priceTtc * quantityForPrice;

    const decreaseQuantity = () => {
        setQuantity(current =>
            Math.max(
                minimumQuantity,
                current - quantityStep
            )
        );
    };

    const increaseQuantity = () => {
        setQuantity(current =>
            Math.min(
                Number(product.stock),
                current + quantityStep
            )
        );
    };

    const [added, setAdded] = useState(false);

    const handleAddToCart = () => {
        addToCart(product, quantity);

        setAdded(true);

        setTimeout(() => {
            setAdded(false);
        }, 1500);
    };

    return (
        <section
            className="
                mt-16 grid grid-cols-1 gap-10
                rounded-[38px]
                border border-white/70
                bg-white/40
                p-6
                shadow-[0_25px_70px_rgba(0,0,0,0.12)]
                backdrop-blur-3xl
                md:grid-cols-2
                md:p-10
            "
        >
            <div>
                <div className="overflow-hidden rounded-[30px] bg-black/5">
                    <img
                        src={`${imagesUrl}/${activeImage}`}
                        alt={product.name}
                        className="
                            h-[500px] w-full
                            object-cover
                            transition-all duration-300
                        "
                    />
                </div>

                {images.length > 1 && (
                    <div className="mt-4 flex gap-3">
                        {images.map(image => (
                            <button
                                key={image}
                                type="button"
                                onClick={() => setActiveImage(image)}
                                className={`
                                    h-20 w-20 overflow-hidden
                                    rounded-2xl
                                    border
                                    transition-all duration-200
                                    hover:scale-105
                                    ${
                                        activeImage === image
                                            ? 'border-black scale-105'
                                            : 'border-white/70'
                                    }
                                `}
                            >
                                <img
                                    src={`${imagesUrl}/${image}`}
                                    alt=""
                                    className="h-full w-full object-cover"
                                />
                            </button>
                        ))}
                    </div>
                )}
            </div>

            <div className="flex flex-col">
                <p className="text-sm font-bold uppercase tracking-[0.14em] text-neutral-500">
                    {product.category_name}
                </p>

                <h1 className="mt-3 text-5xl font-black tracking-[-0.06em] text-black">
                    {product.name}
                </h1>

                {product.origin && (
                    <p className="mt-4 text-sm font-semibold text-neutral-500">
                        Origine : {product.origin}
                    </p>
                )}

                <p className="mt-8 text-lg leading-8 text-neutral-600">
                    {product.description}
                </p>

                <div className="mt-8">
                    <strong className="text-3xl font-black">
                        {priceTtc
                            .toFixed(2)
                            .replace('.', ',')} €
                        {isWeighted ? ' / kg TTC' : ' TTC'}
                    </strong>

                    <p className="mt-1 text-sm text-neutral-500">
                        TVA {vatRate
                        .toFixed(1)
                        .replace('.', ',')} %
                    </p>
                </div>

                <p className="mt-4 text-sm font-semibold text-neutral-500">
                    Stock : {Number(product.stock)}
                    {isWeighted ? ' g' : ' unités'}

                </p>

                <div className="mt-auto pt-10">
                    <div className="mt-8">
                        <p className="mb-3 font-semibold">
                            {product.sale_type === 'poids'
                                ? 'Poids'
                                : 'Quantité'}
                        </p>

                        <p className="mb-3 font-semibold">
                            Total : {totalTtc
                                .toFixed(2)
                                .replace('.', ',')} € TTC
                        </p>

                        <div
                            className="
                                inline-flex items-center gap-5
                                rounded-full
                                border border-white/70
                                bg-white/40
                                px-3 py-2
                                shadow-md
                                backdrop-blur-xl
                            "
                        >
                            <button
                                type="button"
                                onClick={decreaseQuantity}
                                disabled={quantity <= 1}
                                className="
                                    flex h-10 w-10 items-center justify-center
                                    rounded-full bg-black
                                    text-xl font-bold text-white
                                    transition
                                    disabled:cursor-not-allowed
                                    disabled:opacity-30
                                "
                            >
                                −
                            </button>

                            <span className="min-w-8 text-center text-xl font-bold">
                                {quantity}
                                {isWeighted ? ' g' : ''}
                            </span>

                            <button
                                type="button"
                                onClick={increaseQuantity}
                                disabled={quantity >= Number(product.stock)}
                                className="
                                    flex h-10 w-10 items-center justify-center
                                    rounded-full bg-black
                                    text-xl font-bold text-white
                                    transition
                                    disabled:cursor-not-allowed
                                    disabled:opacity-30
                                "
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={handleAddToCart}
                        className="
                            mt-8 w-full rounded-full
                            bg-black
                            px-6 py-4
                            text-lg font-bold text-white
                            transition
                            hover:-translate-y-0.5
                        "
                    >
                        {added ? 'Ajouté au panier ✓' : 'Ajouter au panier'}
                    </button>
                </div>
            </div>
        </section>
    );
}