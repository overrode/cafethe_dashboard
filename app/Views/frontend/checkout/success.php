<?php

/** @var int $sale_id */

require FRONTEND_HEADER_PATH;
?>

<section class="mt-16 py-16">
    <div
        class="
            mx-auto max-w-2xl
            rounded-[38px]
            border border-white/70
            bg-white/40
            p-10
            text-center
            shadow-[0_25px_70px_rgba(0,0,0,0.12)]
            backdrop-blur-3xl
        "
    >
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
            Commande reçue
        </p>

        <h1 class="text-4xl font-black tracking-[-0.06em] md:text-5xl">
            Merci pour votre commande
        </h1>

        <p class="mt-6 text-lg text-neutral-600">
            Votre commande a bien été enregistrée.
        </p>

        <p class="mt-3 font-bold">
            Commande n° <?= (int) $sale_id ?>
        </p>

        <div id="checkout-success-app"></div>

        <a
            href="/public/index.php?route=/products"
            class="
                mt-8 inline-block
                rounded-full
                bg-black
                px-6 py-3
                font-bold text-white
                transition
                hover:-translate-y-0.5
            "
        >
            Continuer mes achats
        </a>
    </div>
</section>

<?php require FRONTEND_FOOTER_PATH; ?>