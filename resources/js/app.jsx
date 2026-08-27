import React from 'react';
import {createRoot} from 'react-dom/client';

import ProductFilter from './components/ProductFilter.jsx';
import ProductPage from './components/ProductPage.jsx';
import CartButton from './components/CartButton.jsx';
import CartPage from './components/CartPage.jsx';
import CheckoutSuccess from './components/CheckoutSuccess.jsx';
import CheckoutOptions from './components/CheckoutOptions.jsx';
import DashboardSaleForm from './components/DashboardSaleForm.jsx';
import LoginModal from './components/LoginModal.jsx';
import DashboardUsers from './components/DashboardUsers.jsx';
import DashboardClients from "./components/DashboardClients";
import DashboardProducts from './components/DashboardProducts.jsx';


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


const checkoutOptionsRoot = document.getElementById(
    'checkout-options-app'
);
if (checkoutOptionsRoot) {
    const initialAddress = JSON.parse(
        checkoutOptionsRoot.dataset.address || 'null'
    );
    createRoot(checkoutOptionsRoot).render(
        <CheckoutOptions
            initialAddress={initialAddress}
        />
    );
}


const dashboardSaleFormRoot = document.getElementById(
    'dashboard-sale-form-app'
);
if (dashboardSaleFormRoot) {
    const clients = JSON.parse(
        dashboardSaleFormRoot.dataset.clients || '[]'
    );

    const products = JSON.parse(
        dashboardSaleFormRoot.dataset.products || '[]'
    );

    createRoot(dashboardSaleFormRoot).render(
        <DashboardSaleForm
            clients={clients}
            products={products}
        />
    );
}


const loginModalRoot = document.getElementById(
    'login-modal-app'
);
if (loginModalRoot) {
    createRoot(loginModalRoot).render(
        <LoginModal />
    );
}


const dashboardClientsRoot = document.getElementById(
    'dashboard-clients-app'
);
if (dashboardClientsRoot) {
    const clients = JSON.parse(
        dashboardClientsRoot.dataset.clients || '[]'
    );

    createRoot(dashboardClientsRoot).render(
        <DashboardClients clients={clients} />
    );
}


const dashboardUsersRoot = document.getElementById(
    'dashboard-users-app'
);
if (dashboardUsersRoot) {
    const users = JSON.parse(
        dashboardUsersRoot.dataset.users || '[]'
    );

    const currentUserId = Number(
        dashboardUsersRoot.dataset.currentUserId
    );

    createRoot(dashboardUsersRoot).render(
        <DashboardUsers
            users={users}
            currentUserId={currentUserId}
        />
    );
}

// Dashboard products manager.
const dashboardProductsRoot = document.getElementById(
    'dashboard-products-app'
);

if (dashboardProductsRoot) {
    const products = JSON.parse(
        dashboardProductsRoot.dataset.products || '[]'
    );

    const categories = JSON.parse(
        dashboardProductsRoot.dataset.categories || '[]'
    );

    createRoot(dashboardProductsRoot).render(
        <DashboardProducts
            products={products}
            categories={categories}
        />
    );
}