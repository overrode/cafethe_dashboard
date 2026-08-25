<?php

$title = 'Nouvelle vente - CafThé';

/** @var array $clients */
/** @var array $products */

require BACKEND_HEADER_PATH;

$clientsJson = htmlspecialchars(
    json_encode(
        $clients,
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ),
    ENT_QUOTES,
    'UTF-8'
);

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
?>

    <section class="py-8">
        <div class="mb-8">
            <a
                href="/public/index.php?route=/dashboard/sales"
                class="text-sm font-semibold text-neutral-500 hover:text-black"
            >
                ← Retour aux ventes
            </a>

            <h1 class="mt-4 text-4xl font-black tracking-[-0.05em]">
                Nouvelle vente
            </h1>
        </div>

        <div
            id="dashboard-sale-form-app"
            data-clients="<?= $clientsJson ?>"
            data-products="<?= $productsJson ?>"
        ></div>
    </section>

<?php require BACKEND_FOOTER_PATH; ?>