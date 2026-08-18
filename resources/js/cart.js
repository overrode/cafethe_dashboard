const CART_KEY = 'cafethe_cart';

export function getCart() {
    const cart = localStorage.getItem(CART_KEY);

    return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    window.dispatchEvent(new Event('cartUpdated'));
}

export function getCartCount() {
    return getCart().reduce(
        (total, item) => total + Number(item.quantity),
        0
    );
}

export function addToCart(product, quantity) {
    const cart = getCart();

    const existingItem = cart.find(
        item => Number(item.id) === Number(product.id)
    );

    if (existingItem) {
        existingItem.quantity += Number(quantity);
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            stock: product.stock,
            quantity: Number(quantity)
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

    item.quantity = Math.max(
        1,
        Math.min(Number(item.stock), Number(quantity))
    );

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