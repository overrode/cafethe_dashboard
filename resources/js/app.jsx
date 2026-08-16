import React from 'react';
import {createRoot} from 'react-dom/client';

import ProductFilter from './components/ProductFilter.jsx';
import ProductPage from './components/ProductPage.jsx';


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