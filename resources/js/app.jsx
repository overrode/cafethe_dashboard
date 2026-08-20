import React from 'react';
import {createRoot} from 'react-dom/client';

import ProductFilter from './components/ProductFilter.jsx';
import ProductPage from './components/ProductPage.jsx';
import CartButton from './components/CartButton.jsx';
import CartPage from './components/CartPage.jsx';
import CheckoutSuccess from './components/CheckoutSuccess.jsx';



const productsRoot = document.getElementById('products-app');
if (productsRoot) {
    const products = JSON.parse(productsRoot.dataset.products);
    const categories = JSON.parse(productsRoot.dataset.categories);
    const imagesUrl = productsRoot.dataset.imagesUrl;

    createRoot(productsRoot).render(
        <ProductFilter
            products={products}
            categories={categories}
            imagesUrl={imagesUrl}
        />
    );
}

const productRoot = document.getElementById('product-app');
if (productRoot) {
    const product = JSON.parse(productRoot.dataset.product);
    const imagesUrl = productRoot.dataset.imagesUrl;

    createRoot(productRoot).render(
        <ProductPage
            product={product}
            imagesUrl={imagesUrl}
        />
    );
}

const popularProductsRoot = document.getElementById('popular-products-app');
if (popularProductsRoot) {
    const products = JSON.parse(popularProductsRoot.dataset.products);
    const categories = JSON.parse(popularProductsRoot.dataset.categories);
    const imagesUrl = popularProductsRoot.dataset.imagesUrl;

    createRoot(popularProductsRoot).render(
        <ProductFilter
            products={products}
            categories={categories}
            imagesUrl={imagesUrl}
        />
    );
}

const cartButtonRoot = document.getElementById('cart-button-app');
if (cartButtonRoot) {
    createRoot(cartButtonRoot).render(
        <CartButton />
    );
}

const cartRoot = document.getElementById('cart-app');
if (cartRoot) {
    createRoot(cartRoot).render(
        <CartPage
            imagesUrl={cartRoot.dataset.imagesUrl}
        />
    );
}

const checkoutSuccessRoot = document.getElementById(
    'checkout-success-app'
);
if (checkoutSuccessRoot) {
    createRoot(checkoutSuccessRoot).render(
        <CheckoutSuccess />
    );
}