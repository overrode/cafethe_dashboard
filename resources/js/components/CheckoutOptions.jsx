import React, {useState} from 'react';

export default function CheckoutOptions() {
    const [deliveryMethod, setDeliveryMethod] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');

    const needsAddress = deliveryMethod === 'livraison';

    const paymentMethods = [
        {
            value: 'cb',
            label: 'Carte bancaire',
            available: true,
        },
        {
            value: 'virement',
            label: 'Virement bancaire',
            available: true,
        },
        {
            value: 'especes',
            label: 'Espèces',
            available: deliveryMethod === 'magasin',
        },
        {
            value: 'cheque',
            label: 'Chèque',
            available: deliveryMethod === 'magasin',
        },
    ];

    const handleDeliveryChange = (method) => {
        setDeliveryMethod(method);

        /*
         * If the customer selected cash or cheque for shop pickup
         * and then switches to home delivery, that payment method
         * is no longer valid.
         */
        if (
            method === 'livraison'
            && ['especes', 'cheque'].includes(paymentMethod)
        ) {
            setPaymentMethod('');
        }
    };

    return (
        <>
            {/* Delivery method */}
            <h2 className="mt-10 text-2xl font-black">
                Mode de livraison
            </h2>

            <div className="mt-5 grid gap-3 sm:grid-cols-2">
                <label
                    className={`
                        cursor-pointer rounded-2xl border p-4
                        transition
                        ${
                            deliveryMethod === 'livraison'
                                ? 'border-black bg-black text-white'
                                : 'border-white/70 bg-white/40'
                        }
                    `}
                >
                    <input
                        type="radio"
                        name="delivery_method"
                        value="livraison"
                        required
                        checked={deliveryMethod === 'livraison'}
                        onChange={() => handleDeliveryChange('livraison')}
                    />

                    <span className="ml-2 font-semibold">
                        Livraison à domicile
                    </span>
                </label>

                <label
                    className={`
                        cursor-pointer rounded-2xl border p-4
                        transition
                        ${
                            deliveryMethod === 'magasin'
                                ? 'border-black bg-black text-white'
                                : 'border-white/70 bg-white/40'
                        }
                    `}
                >
                    <input
                        type="radio"
                        name="delivery_method"
                        value="magasin"
                        checked={deliveryMethod === 'magasin'}
                        onChange={() => handleDeliveryChange('magasin')}
                    />

                    <span className="ml-2 font-semibold">
                        Retrait en magasin
                    </span>
                </label>
            </div>


            {/* Delivery address */}
            {needsAddress && (
                <div className="mt-8">
                    <h3 className="text-xl font-black">
                        Adresse de livraison
                    </h3>

                    <div className="mt-5">
                        <label
                            htmlFor="address"
                            className="mb-2 block text-sm font-bold"
                        >
                            Adresse
                        </label>

                        <input
                            id="address"
                            name="address"
                            type="text"
                            required
                            className="
                                w-full rounded-2xl
                                border border-black/10
                                bg-white/70
                                px-4 py-3
                                outline-none
                                transition
                                focus:border-black
                            "
                        />
                    </div>

                    <div className="mt-5 grid gap-5 md:grid-cols-2">
                        <div>
                            <label
                                htmlFor="postal_code"
                                className="mb-2 block text-sm font-bold"
                            >
                                Code postal
                            </label>

                            <input
                                id="postal_code"
                                name="postal_code"
                                type="text"
                                required
                                className="
                                    w-full rounded-2xl
                                    border border-black/10
                                    bg-white/70
                                    px-4 py-3
                                    outline-none
                                    transition
                                    focus:border-black
                                "
                            />
                        </div>

                        <div>
                            <label
                                htmlFor="city"
                                className="mb-2 block text-sm font-bold"
                            >
                                Ville
                            </label>

                            <input
                                id="city"
                                name="city"
                                type="text"
                                required
                                className="
                                    w-full rounded-2xl
                                    border border-black/10
                                    bg-white/70
                                    px-4 py-3
                                    outline-none
                                    transition
                                    focus:border-black
                                "
                            />
                        </div>
                    </div>
                </div>
            )}


            {/* Payment method */}
            {deliveryMethod && (
                <>
                    <h2 className="mt-10 text-2xl font-black">
                        Mode de paiement
                    </h2>

                    <div className="mt-5 grid gap-3 sm:grid-cols-2">
                        {paymentMethods
                            .filter(method => method.available)
                            .map(method => (
                                <label
                                    key={method.value}
                                    className={`
                                        cursor-pointer rounded-2xl border p-4
                                        transition
                                        ${
                                            paymentMethod === method.value
                                                ? 'border-black bg-black text-white'
                                                : 'border-white/70 bg-white/40'
                                        }
                                    `}
                                >
                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value={method.value}
                                        required
                                        checked={paymentMethod === method.value}
                                        onChange={() =>
                                            setPaymentMethod(method.value)
                                        }
                                    />

                                    <span className="ml-2 font-semibold">
                                        {method.label}
                                    </span>
                                </label>
                            ))}
                    </div>
                </>
            )}
        </>
    );
}