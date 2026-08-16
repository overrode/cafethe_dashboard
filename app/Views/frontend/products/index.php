<?php require FRONTEND_HEADER_PATH; ?>

<section class="mt-16 py-10">
    <div class="mb-10 max-w-2xl">
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
            Notre sélection
        </p>

        <h1 class="text-4xl font-black tracking-[-0.06em] text-black md:text-6xl">
            Tous nos produits
        </h1>

        <p class="mt-5 text-lg leading-7 text-neutral-600">
            Découvrez notre sélection de cafés, thés, infusions, accessoires et coffrets.
        </p>
    </div>

    <div
        id="products-app"
        data-products="<?= htmlspecialchars(
            json_encode($products),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-categories="<?= htmlspecialchars(
            json_encode($categories),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-images-url="<?= htmlspecialchars(
            PRODUCT_IMAGES_URL,
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    ></div>
</section>

<?php require FRONTEND_FOOTER_PATH; ?>