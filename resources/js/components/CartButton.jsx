import React, {useEffect, useState} from 'react';
import {getCartCount} from '../cart.js';

export default function CartButton() {
    const [count, setCount] = useState(getCartCount());

    useEffect(() => {
        const updateCount = () => {
            setCount(getCartCount());
        };

        window.addEventListener('cartUpdated', updateCount);

        return () => {
            window.removeEventListener('cartUpdated', updateCount);
        };
    }, []);

    return (
        <a
            href="/public/index.php?route=/cart"
            className="
                relative flex h-11 w-11
                items-center justify-center
                rounded-full
                bg-black text-white
                transition
                hover:-translate-y-0.5
            "
            aria-label="Panier"
        >
            🛒

            {count > 0 && (
                <span
                    className="
                        absolute -right-2 -top-2
                        flex h-6 min-w-6
                        items-center justify-center
                        rounded-full
                        bg-white px-1
                        text-xs font-black text-black
                        shadow
                    "
                >
                    {count}
                </span>
            )}
        </a>
    );
}