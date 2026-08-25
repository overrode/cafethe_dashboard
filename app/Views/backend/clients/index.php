<?php

$title = 'Clients - CafThé';

/** @var array $clients */

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
?>

<section class="py-8">
    <div class="mb-8">
        <p
            class="
                mb-2
                text-xs font-bold
                uppercase tracking-[0.16em]
                text-neutral-500
            "
        >
            Dashboard vendeur
        </p>

        <h1
            class="
                text-4xl font-black
                tracking-[-0.05em]
            "
        >
            Clients
        </h1>
    </div>

    <div
        id="dashboard-clients-app"
        data-clients="<?= $clientsJson ?>"
    ></div>
</section>

<?php require BACKEND_FOOTER_PATH; ?>