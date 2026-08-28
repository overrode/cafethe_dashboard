<?php

/** @var array $product */

require FRONTEND_HEADER_PATH; ?>

<a
    href="/public/index.php?route=/products"
    class="
        mt-6 inline-flex items-center gap-2
        rounded-full
        border border-white/70
        bg-white/40
        px-5 py-3
        font-bold text-black
        shadow-md
        backdrop-blur-xl
        transition
        hover:-translate-y-0.5
        hover:bg-black
        hover:text-white
    "
>
    ← Retour aux produits
</a>

<div
    id="product-app"
    data-product="<?= htmlspecialchars(
        json_encode($product),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    data-images-url="<?= htmlspecialchars(
        PRODUCT_IMAGES_URL,
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    data-weight-step="<?= PRODUCT_WEIGHT_STEP_GRAMS ?>"
></div>

<?php require FRONTEND_FOOTER_PATH; ?>