<?php

/** @var array $checkout_items */
/** @var float $checkout_total_ht */
/** @var float $checkout_total_vat */
/** @var float $checkout_total_ttc */
/** @var array|null $client */

require FRONTEND_HEADER_PATH;
?>

<section class="mt-16 py-10">

    <!-- Page heading. -->
    <div class="mb-10">
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
            Finaliser votre commande
        </p>

        <h1 class="text-4xl font-black tracking-[-0.06em] md:text-6xl">
            Commande
        </h1>
    </div>


    <!-- Cart products. -->
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
                        <?= number_format(
                            (float) $checkout_item['checkout_quantity'],
                            $checkout_item['checkout_product']['sale_type'] === 'poids'
                                ? 2
                                : 0,
                            ',',
                            ' '
                        ) ?>
                    </p>
                </div>

                <strong>
                    <?= number_format(
                        $checkout_item['checkout_line_total_ttc'],
                        2,
                        ',',
                        ' '
                    ) ?> €
                </strong>
            </div>

        <?php endforeach; ?>
    </div>


    <!-- Checkout totals. -->
    <div
        class="
            mt-8 ml-auto
            max-w-md
            space-y-2
            text-right
        "
    >
        <p>
            <span class="mr-4 text-neutral-500">
                Total HT
            </span>

            <strong>
                <?= number_format(
                    $checkout_total_ht,
                    2,
                    ',',
                    ' '
                ) ?> €
            </strong>
        </p>

        <p>
            <span class="mr-4 text-neutral-500">
                TVA
            </span>

            <strong>
                <?= number_format(
                    $checkout_total_vat,
                    2,
                    ',',
                    ' '
                ) ?> €
            </strong>
        </p>

        <p
            class="
                mt-3
                border-t border-black/10
                pt-4
                text-xl
            "
        >
            <span class="mr-4 font-bold">
                Total TTC
            </span>

            <strong class="text-3xl">
                <?= number_format(
                    $checkout_total_ttc,
                    2,
                    ',',
                    ' '
                ) ?> €
            </strong>
        </p>
    </div>


    <!-- Checkout form. -->
    <form
        method="POST"
        action="/public/index.php?route=/checkout/confirm"
        class="
            mt-10
            rounded-[30px]
            border border-white/70
            bg-white/40
            p-6
            text-left
            shadow-lg
            backdrop-blur-2xl
            md:p-8
        "
    >

        <?php if ($client): ?>

            <!-- Logged-in client. -->
            <div>
                <h2 class="text-2xl font-black">
                    Vos informations
                </h2>

                <div
                    class="
                        mt-6
                        rounded-3xl
                        border border-black/10
                        bg-white/60
                        p-5
                    "
                >
                    <p class="font-black">
                        <?= htmlspecialchars(
                            $client['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p class="mt-1 text-sm text-neutral-500">
                        <?= htmlspecialchars(
                            $client['email'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <?php if (!empty($client['phone'])): ?>
                        <p class="mt-1 text-sm text-neutral-500">
                            <?= htmlspecialchars(
                                $client['phone'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>

            <!-- Guest information. -->
            <div>
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
                                focus:border-black
                            "
                        >

                        <!-- Existing client message. -->
                        <p
                            id="existing-client-message"
                            class="
                                mt-2 hidden
                                text-sm
                                text-amber-700
                            "
                        >
                            Un compte existe déjà avec cet e-mail.

                            <a
                                href="/public/index.php?route=/login"
                                data-login-trigger
                                class="
                                    font-bold
                                    underline
                                    underline-offset-2
                                "
                            >
                                Se connecter
                            </a>
                        </p>
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
                                focus:border-black
                            "
                        >
                    </div>

                </div>
            </div>

        <?php endif; ?>


        <!-- Payment and delivery. -->
        <div
            id="checkout-options-app"
            data-address="<?= htmlspecialchars(
                json_encode(
                    $client['address'] ?? null,
                    JSON_UNESCAPED_UNICODE
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        ></div>


        <!-- Trusted cart payload. -->
        <input
            type="hidden"
            name="items"
            value="<?= htmlspecialchars(
                json_encode(
                    array_map(
                        static fn(array $checkout_item): array => [
                            'product_id' =>
                                $checkout_item['checkout_product']['id'],

                            'quantity' =>
                                $checkout_item['checkout_quantity'],
                        ],
                        $checkout_items
                    )
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >


        <button
            id="checkout-submit"
            type="submit"
            class="
                mt-10 w-full
                rounded-full
                bg-black
                px-6 py-4
                text-lg font-bold
                text-white
                transition
                hover:-translate-y-0.5
                disabled:cursor-not-allowed
                disabled:opacity-30
            "
        >
            Continuer
        </button>
    </form>

</section>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const emailInput = document.getElementById('email');

    const message = document.getElementById(
        'existing-client-message'
    );

    const submitButton = document.getElementById(
        'checkout-submit'
    );

    // Logged-in clients do not have a guest email input.
    if (!emailInput || !message || !submitButton) {
        return;
    }

    let timeoutId;

    emailInput.addEventListener('input', () => {
        clearTimeout(timeoutId);

        const email = emailInput.value.trim();

        // Reset while the email changes.
        message.classList.add('hidden');
        submitButton.disabled = false;

        if (
            email === ''
            || !emailInput.validity.valid
        ) {
            return;
        }

        timeoutId = setTimeout(async () => {
            const formData = new FormData();

            formData.append('email', email);

            try {
                const response = await fetch(
                    '/public/index.php?route=/account/check-email',
                    {
                        method: 'POST',
                        body: formData,
                    }
                );

                const data = await response.json();

                // Ignore responses for an older email value.
                if (emailInput.value.trim() !== email) {
                    return;
                }

                if (data.exists) {
                    message.classList.remove('hidden');
                    submitButton.disabled = true;
                }

            } catch (error) {
                console.error(
                    'Email check failed:',
                    error
                );
            }
        }, 400);
    });
});
</script>

<?php require FRONTEND_FOOTER_PATH; ?>