<?php require FRONTEND_HEADER_PATH; ?>

<section class="mt-16 py-10">
    <div class="mb-10">
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
            Votre sélection
        </p>

        <h1 class="text-4xl font-black tracking-[-0.06em] md:text-6xl">
            Panier
        </h1>
    </div>

    <div
        id="cart-app"
        data-images-url="<?= htmlspecialchars(
            PRODUCT_IMAGES_URL,
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-weight-step="<?= PRODUCT_WEIGHT_STEP_GRAMS ?>"
    ></div>
</section>

<?php require FRONTEND_FOOTER_PATH; ?>