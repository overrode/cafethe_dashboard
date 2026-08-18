<?php
/** @var array $checkout_items */
/** @var float $checkout_total */
require FRONTEND_HEADER_PATH; ?>

    <section class="mt-16 py-10">
        <div class="mb-10">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
                Finaliser votre commande
            </p>

            <h1 class="text-4xl font-black tracking-[-0.06em] md:text-6xl">
                Commande
            </h1>
        </div>

        <div class="space-y-4">
            <?php foreach ($checkout_items as $checkout_item): ?>
                <div
                        class="
                        flex items-center justify-between
                        rounded-[24px]
                        border border-white/70
                        bg-white/40
                        p-5
                        shadow-md
                        backdrop-blur-2xl
                    "
                >
                    <div>
                        <strong class="text-lg">
                            <?= htmlspecialchars(
                                $checkout_item['checkout_product']['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <p class="text-neutral-500">
                            Quantité :
                            <?= (int)$checkout_item['checkout_quantity'] ?>
                        </p>
                    </div>

                    <strong>
                        <?= number_format(
                            $checkout_item['checkout_line_total'],
                            2,
                            ',',
                            ' '
                        ) ?> €
                    </strong>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8 text-right">
            <span class="mr-4 text-lg">
                Total
            </span>

            <strong class="text-3xl">
                <?= number_format(
                    $checkout_total,
                    2,
                    ',',
                    ' '
                ) ?> €
            </strong>
            <form
                method="POST"
                action="/public/index.php?route=/checkout/confirm"
                class="
                    mt-10
                    rounded-[30px]
                    border border-white/70
                    bg-white/40
                    p-6
                    shadow-lg
                    backdrop-blur-2xl
                    md:p-8
                "
            >
                <h2 class="text-2xl font-black">
                    Vos informations
                </h2>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div>
                        <label
                                for="firstname"
                                class="mb-2 block text-sm font-bold"
                        >
                            Prénom
                        </label>

                        <input
                                id="firstname"
                                name="firstname"
                                type="text"
                                required
                                class="
                    w-full rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    transition
                    focus:border-black
                "
                        >
                    </div>

                    <div>
                        <label
                                for="lastname"
                                class="mb-2 block text-sm font-bold"
                        >
                            Nom
                        </label>

                        <input
                                id="lastname"
                                name="lastname"
                                type="text"
                                required
                                class="
                    w-full rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    transition
                    focus:border-black
                "
                        >
                    </div>

                    <div>
                        <label
                                for="email"
                                class="mb-2 block text-sm font-bold"
                        >
                            E-mail
                        </label>

                        <input
                                id="email"
                                name="email"
                                type="email"
                                required
                                class="
                    w-full rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    transition
                    focus:border-black
                "
                        >
                    </div>

                    <div>
                        <label
                                for="phone"
                                class="mb-2 block text-sm font-bold"
                        >
                            Téléphone
                        </label>

                        <input
                                id="phone"
                                name="phone"
                                type="tel"
                                class="
                    w-full rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    transition
                    focus:border-black
                "
                        >
                    </div>
                </div>

                <h2 class="mt-10 text-2xl font-black">
                    Livraison
                </h2>

                <div class="mt-6">
                    <label
                            for="address"
                            class="mb-2 block text-sm font-bold"
                    >
                        Adresse
                    </label>

                    <input
                            id="address"
                            name="address"
                            type="text"
                            required
                            class="
                w-full rounded-2xl
                border border-black/10
                bg-white/70
                px-4 py-3
                outline-none
                transition
                focus:border-black
            "
                    >
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div>
                        <label
                                for="postal_code"
                                class="mb-2 block text-sm font-bold"
                        >
                            Code postal
                        </label>

                        <input
                                id="postal_code"
                                name="postal_code"
                                type="text"
                                required
                                class="
                    w-full rounded-2xl
                    border border-black/10
                    bg-white/70
                    px-4 py-3
                    outline-none
                    transition
                    focus:border-black
                "
                        >
                    </div>

                    <div>
                        <label
                                for="city"
                                class="mb-2 block text-sm font-bold"
                        >
                            Ville
                        </label>

                        <input
                            id="city"
                            name="city"
                            type="text"
                            required
                            class="
                                w-full rounded-2xl
                                border border-black/10
                                bg-white/70
                                px-4 py-3
                                outline-none
                                transition
                                focus:border-black
                            "
                        >
                    </div>
                </div>

                <input
                    type="hidden"
                    name="items"
                    value="<?= htmlspecialchars(
                        json_encode(
                            array_map(
                                static fn(array $checkout_item): array => [
                                    'product_id' => $checkout_item['checkout_product']['id'],
                                    'quantity' => $checkout_item['checkout_quantity'],
                                ],
                                $checkout_items
                            )
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <button
                    type="submit"
                    class="
                        mt-10 w-full
                        rounded-full
                        bg-black
                        px-6 py-4
                        text-lg font-bold text-white
                        transition
                        hover:-translate-y-0.5
                    "
                >
                    Continuer
                </button>
            </form>
        </div>
    </section>

<?php require FRONTEND_FOOTER_PATH; ?>