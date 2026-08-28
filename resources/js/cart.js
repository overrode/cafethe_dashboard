const CART_KEY = 'cafethe_cart';

export function getCart() {
    const cart = localStorage.getItem(CART_KEY);

    return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
    localStorage.setItem(
        CART_KEY,
        JSON.stringify(cart)
    );

    window.dispatchEvent(
        new Event('cartUpdated')
    );
}

// Count distinct cart products.
export function getCartCount() {
    return getCart().reduce(
        (total, item) => {
            if (item.sale_type === 'poids') {
                return total + 1;
            }

            return total + Number(item.quantity);
        },
        0
    );
}


// Normalize quantity by sale type and stock.
function normalizeQuantity(product, quantity) {
    const stock = Number(product.stock);
    let normalizedQuantity = Number(quantity);

    if (
        !Number.isFinite(normalizedQuantity)
        || normalizedQuantity <= 0
    ) {
        return 0;
    }

    // Unit products require whole quantities.
    if (product.sale_type === 'unite') {
        normalizedQuantity = Math.floor(
            normalizedQuantity
        );
    }

    return Math.min(
        stock,
        normalizedQuantity
    );
}

export function addToCart(product, quantity) {
    const cart = getCart();

    const existingItem = cart.find(
        item => Number(item.id) === Number(product.id)
    );

    const requestedQuantity = Number(quantity);

    if (existingItem) {
        const totalQuantity =
            Number(existingItem.quantity)
            + requestedQuantity;

        existingItem.stock = Number(product.stock);
        existingItem.price = product.price;
        existingItem.vat_rate = product.vat_rate;
        existingItem.sale_type = product.sale_type;

        existingItem.quantity = normalizeQuantity(
            existingItem,
            totalQuantity
        );

    } else {
        const normalizedQuantity = normalizeQuantity(
            product,
            requestedQuantity
        );

        if (normalizedQuantity <= 0) {
            return cart;
        }

        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            vat_rate: product.vat_rate,
            image: product.image,
            stock: Number(product.stock),
            sale_type: product.sale_type,
            quantity: normalizedQuantity,
        });
    }

    saveCart(cart);

    return cart;
}

export function updateCartQuantity(productId, quantity) {
    const cart = getCart();

    const item = cart.find(
        item => Number(item.id) === Number(productId)
    );

    if (!item) {
        return;
    }

    item.quantity = normalizeQuantity(
        item,
        quantity
    );

    // Remove invalid/zero quantity.
    if (item.quantity <= 0) {
        removeFromCart(productId);
        return;
    }

    saveCart(cart);
}

export function removeFromCart(productId) {
    const cart = getCart().filter(
        item => Number(item.id) !== Number(productId)
    );

    saveCart(cart);
}

export function clearCart() {
    saveCart([]);
}