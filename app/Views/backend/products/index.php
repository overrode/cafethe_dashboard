<?php

$title = 'Produits - CafThé';

/** @var array $products */
/** @var array $categories */

require BACKEND_HEADER_PATH;

// Send PHP data safely to React.
$productsJson = htmlspecialchars(
    json_encode(
        $products,
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ),
    ENT_QUOTES,
    'UTF-8'
);

$categoriesJson = htmlspecialchars(
    json_encode(
        $categories,
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ),
    ENT_QUOTES,
    'UTF-8'
);
?>

<section class="py-8">
    <!-- Page heading. -->
    <div class="mb-8">
        <p
            class="
                mb-2 text-xs font-bold
                uppercase tracking-[0.16em]
                text-neutral-500
            "
        >
            Dashboard vendeur
        </p>

        <h1 class="text-4xl font-black tracking-[-0.05em]">
            Produits
        </h1>
    </div>

    <!-- React products manager. -->
    <div
        id="dashboard-products-app"
        data-products="<?= $productsJson ?>"
        data-categories="<?= $categoriesJson ?>"
    ></div>
</section>

<?php require BACKEND_FOOTER_PATH; ?>