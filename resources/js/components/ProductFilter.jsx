import React, {useState} from 'react';

export default function ProductFilter({products, categories, imagesUrl}) {
    const [activeCategory, setActiveCategory] = useState('all');
    const [visible, setVisible] = useState(true);

    const filteredProducts = activeCategory === 'all'
        ? products
        : products.filter(
            product => String(product.category_id) === String(activeCategory)
        );

    const changeCategory = (categoryId) => {
        if (String(categoryId) === String(activeCategory)) {
            return;
        }

        setVisible(false);

        setTimeout(() => {
            setActiveCategory(categoryId);
            setVisible(true);
        }, 150);
    };

    const buttonClass = (categoryId) => {
        const isActive = String(activeCategory) === String(categoryId);

        return `
            rounded-full border
            px-5 py-2.5
            font-semibold
            shadow-md backdrop-blur-xl
            transition-all duration-300
            hover:-translate-y-0.5
            ${
                isActive
                    ? 'border-black bg-black text-white'
                    : 'border-white/70 bg-white/40 text-black hover:bg-white/70'
            }
        `;
    };

    return (
        <div>
            <div className="mb-8 flex flex-wrap gap-3">
                <button
                    type="button"
                    onClick={() => changeCategory('all')}
                    className={buttonClass('all')}
                >
                    Tous
                </button>

                {Object.entries(categories).map(([categoryId, categoryName]) => (
                    <button
                        key={categoryId}
                        type="button"
                        onClick={() => changeCategory(categoryId)}
                        className={buttonClass(categoryId)}
                    >
                        {categoryName}
                    </button>
                ))}
            </div>

            <div
                className={`
                    grid grid-cols-1 gap-6
                    transition-all duration-300 ease-out
                    md:grid-cols-2
                    lg:grid-cols-3
                    ${
                        visible
                            ? 'scale-100 opacity-100 blur-0'
                            : 'scale-[0.98] opacity-0 blur-sm'
                    }
                `}
            >
                {filteredProducts.map(product => {
                    const mainImage = product.image
                        ? product.image.split(';')[0]
                        : null;

                    return (
                        <article
                            key={product.id}
                            className="
                                flex flex-col overflow-hidden
                                rounded-[28px]
                                border border-white/70
                                bg-white/40
                                shadow-[0_18px_45px_rgba(0,0,0,0.12)]
                                backdrop-blur-2xl
                                transition-all duration-300
                                hover:-translate-y-2
                                hover:shadow-[0_28px_60px_rgba(0,0,0,0.18)]
                            "
                        >
                            {mainImage && (
                                <div className="h-56 overflow-hidden bg-black/5">
                                    <img
                                        className="
                                            h-full w-full
                                            object-cover
                                            transition duration-500
                                            hover:scale-105
                                        "
                                        src={`${imagesUrl}/${mainImage}`}
                                        alt={product.name}
                                    />
                                </div>
                            )}

                            <div className="flex flex-1 flex-col p-6">
                                <p className="mb-2 text-xs font-bold uppercase tracking-[0.12em] text-neutral-500">
                                    {product.category_name}
                                </p>

                                <h3 className="text-2xl font-bold tracking-tight text-black">
                                    {product.name}
                                </h3>

                                <p className="my-4 leading-6 text-neutral-600">
                                    {product.description}
                                </p>

                                {product.origin && (
                                    <p className="mb-5 text-sm font-semibold text-neutral-700">
                                        Origine : {product.origin}
                                    </p>
                                )}

                                <div className="mt-auto flex items-center justify-between gap-4">
                                    <strong className="whitespace-nowrap text-xl font-bold">
                                        {Number(product.price)
                                            .toFixed(2)
                                            .replace('.', ',')} €
                                    </strong>

                                    <a
                                        href={`/public/index.php?route=/product&id=${product.id}`}
                                        className="
                                            rounded-full bg-black
                                            px-5 py-3
                                            font-bold text-white
                                            transition
                                            hover:-translate-y-0.5
                                        "
                                    >
                                        Voir le produit
                                    </a>
                                </div>
                            </div>
                        </article>
                    );
                })}
            </div>
        </div>
    );
}