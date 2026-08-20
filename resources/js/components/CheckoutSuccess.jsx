import React, {useEffect} from 'react';

import {clearCart} from '../cart.js';

export default function CheckoutSuccess() {
    useEffect(() => {
        clearCart();
    }, []);

    return null;
}