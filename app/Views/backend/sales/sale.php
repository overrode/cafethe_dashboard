<?php

/** @var array  $sale */

$title = 'Commande #' . $sale['id'] . ' - CafThé';

/** @var array $sale */
/** @var array $items */
/** @var array|null $deliveryAddress */

require BACKEND_HEADER_PATH;

// Display labels.
$statusLabels = [
    'pending' => 'En attente',
    'preparing' => 'En préparation',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée',
];

$paymentStatusLabels = [
    'pending' => 'En attente',
    'paid' => 'Payé',
    'failed' => 'Échoué',
    'refunded' => 'Remboursé',
];

$paymentMethodLabels = [
    'cb' => 'Carte bancaire',
    'especes' => 'Espèces',
    'cheque' => 'Chèque',
    'virement' => 'Virement',
];

$deliveryMethodLabels = [
    'magasin' => 'Retrait en magasin',
    'livraison' => 'Livraison',
];

$deliveryStatusLabels = [
    'pending' => 'En attente',
    'ready_for_pickup' => 'Prête à retirer',
    'shipped' => 'Expédiée',
    'delivered' => 'Livrée',
    'collected' => 'Retirée',
];

$sourceLabels = [
    'dashboard' => 'Dashboard',
    'website' => 'Site web',
];

$status =
    $statusLabels[$sale['status']]
    ?? $sale['status'];

$paymentStatus =
    $paymentStatusLabels[$sale['payment_status']]
    ?? $sale['payment_status'];

$paymentMethod =
    $paymentMethodLabels[$sale['payment_method']]
    ?? $sale['payment_method'];

$deliveryMethod =
    $deliveryMethodLabels[$sale['delivery_method']]
    ?? $sale['delivery_method'];

$deliveryStatus =
    $deliveryStatusLabels[$sale['delivery_status']]
    ?? $sale['delivery_status'];

$source =
    $sourceLabels[$sale['source']]
    ?? $sale['source'];
?>

<section class="py-8">

    <!-- Header -->
    <div class="mb-8">
        <a
            href="/public/index.php?route=/dashboard/sales"
            class="
                text-sm font-semibold
                text-neutral-500
                hover:text-black
            "
        >
            ← Retour aux ventes
        </a>

        <div
            class="
                mt-4
                flex flex-wrap
                items-end
                justify-between
                gap-4
            "
        >
            <div>
                <p
                    class="
                        text-sm font-bold
                        uppercase tracking-widest
                        text-neutral-400
                    "
                >
                    Commande
                </p>

                <h1
                    class="
                        mt-1
                        text-4xl font-black
                        tracking-[-0.05em]
                    "
                >
                    #<?= (int) $sale['id'] ?>
                </h1>

                <p class="mt-2 text-neutral-500">
                    <?= htmlspecialchars(
                        date(
                            'd/m/Y à H:i',
                            strtotime($sale['sale_date'])
                        )
                    ) ?>
                </p>
            </div>

            <div class="text-right">
                <p class="text-sm text-neutral-500">
                    Total TTC
                </p>

                <p class="text-3xl font-black">
                    <?= number_format(
                        (float) $sale['total_ttc'],
                        2,
                        ',',
                        ' '
                    ) ?> €
                </p>
            </div>
        </div>
    </div>


    <!-- Status -->
    <div class="grid gap-4 md:grid-cols-3">
        <div
            class="
                rounded-3xl
                border border-black/10
                bg-white
                p-5
            "
        >
            <p class="text-sm text-neutral-500">
                Statut
            </p>

            <p class="mt-2 text-lg font-black">
                <?= htmlspecialchars($status) ?>
            </p>
        </div>

        <div
            class="
                rounded-3xl
                border border-black/10
                bg-white
                p-5
            "
        >
            <p class="text-sm text-neutral-500">
                Paiement
            </p>

            <p class="mt-2 text-lg font-black">
                <?= htmlspecialchars($paymentStatus) ?>
            </p>

            <p class="mt-1 text-sm text-neutral-500">
                <?= htmlspecialchars($paymentMethod) ?>
            </p>
        </div>

        <div
            class="
                rounded-3xl
                border border-black/10
                bg-white
                p-5
            "
        >
            <p class="text-sm text-neutral-500">
                Livraison
            </p>

            <p class="mt-2 text-lg font-black">
                <?= htmlspecialchars($deliveryStatus) ?>
            </p>

            <p class="mt-1 text-sm text-neutral-500">
                <?= htmlspecialchars($deliveryMethod) ?>
            </p>
        </div>
    </div>


    <!-- Sale information -->
    <div class="mt-6 grid gap-6 lg:grid-cols-2">

        <section
            class="
                rounded-3xl
                border border-black/10
                bg-white
                p-6
            "
        >
            <h2 class="text-xl font-black">
                Client
            </h2>

            <?php if (!empty($sale['client_id'])): ?>

                <div class="mt-5 space-y-2">
                    <p class="font-bold">
                        <?= htmlspecialchars(
                            $sale['client_name'] ?? ''
                        ) ?>
                    </p>

                    <?php if (!empty($sale['client_email'])): ?>
                        <p class="text-neutral-600">
                            <?= htmlspecialchars(
                                $sale['client_email']
                            ) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($sale['client_phone'])): ?>
                        <p class="text-neutral-600">
                            <?= htmlspecialchars(
                                $sale['client_phone']
                            ) ?>
                        </p>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <p class="mt-5 text-neutral-500">
                    Client non renseigné
                </p>

            <?php endif; ?>
        </section>


        <section
            class="
                rounded-3xl
                border border-black/10
                bg-white
                p-6
            "
        >
            <h2 class="text-xl font-black">
                Vente
            </h2>

            <div class="mt-5 space-y-3">

                <div class="flex justify-between gap-4">
                    <span class="text-neutral-500">
                        Source
                    </span>

                    <strong>
                        <?= htmlspecialchars($source) ?>
                    </strong>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-neutral-500">
                        Vendeur
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $sale['user_name']
                            ?? 'Site web'
                        ) ?>
                    </strong>
                </div>

                <?php if (!empty($sale['paid_at'])): ?>
                    <div class="flex justify-between gap-4">
                        <span class="text-neutral-500">
                            Payée le
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y H:i',
                                    strtotime($sale['paid_at'])
                                )
                            ) ?>
                        </strong>
                    </div>
                <?php endif; ?>

            </div>
        </section>

    </div>


    <!-- Delivery address -->
    <?php if ($deliveryAddress): ?>
        <section
            class="
                mt-6
                rounded-3xl
                border border-black/10
                bg-white
                p-6
            "
        >
            <h2 class="text-xl font-black">
                Adresse de livraison
            </h2>

            <div class="mt-5 text-neutral-600">
                <p>
                    <?= htmlspecialchars(
                        $deliveryAddress['address'] ?? ''
                    ) ?>
                </p>

                <p>
                    <?= htmlspecialchars(
                        trim(
                            ($deliveryAddress['postal_code'] ?? '')
                            . ' '
                            . ($deliveryAddress['city'] ?? '')
                        )
                    ) ?>
                </p>
            </div>
        </section>
    <?php endif; ?>


    <!-- Products -->
    <section
        class="
            mt-6
            overflow-hidden
            rounded-3xl
            border border-black/10
            bg-white
        "
    >
        <div class="p-6">
            <h2 class="text-xl font-black">
                Produits
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">

                <thead class="border-y border-black/10 bg-neutral-50">
                    <tr>
                        <th class="px-6 py-4">
                            Produit
                        </th>

                        <th class="px-6 py-4">
                            Quantité / Poids
                        </th>

                        <th class="px-6 py-4">
                            Prix HT
                        </th>

                        <th class="px-6 py-4">
                            TVA
                        </th>

                        <th class="px-6 py-4 text-right">
                            Total TTC
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($items as $item): ?>
                        <?php $isWeighted = $item['sale_type'] === 'poids'; ?>

                        <tr class="border-b border-black/5">
                            <td class="px-6 py-4">
                                <p class="font-bold">
                                    <?= htmlspecialchars(
                                        $item['product_name']
                                    ) ?>
                                </p>

                                <p class="mt-1 text-sm text-neutral-400">
                                    <?= htmlspecialchars(
                                        $item['product_sku'] ?? ''
                                    ) ?>
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <?= number_format(
                                    (float) $item['quantity'],
                                    0,
                                    ',',
                                    ' '
                                ) ?>

                                <?= $isWeighted
                                    ? ' g'
                                    : '' ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= number_format(
                                    (float) $item['unit_price'],
                                    2,
                                    ',',
                                    ' '
                                ) ?> €

                                <?= $isWeighted
                                    ? '/ kg'
                                    : '/ unité' ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= number_format(
                                    (float) $item['vat_rate'],
                                    2,
                                    ',',
                                    ' '
                                ) ?> %
                            </td>

                            <td
                                class="
                                    px-6 py-4
                                    text-right
                                    font-bold
                                "
                            >
                                <?= number_format(
                                    (float) $item['total_ttc'],
                                    2,
                                    ',',
                                    ' '
                                ) ?> €
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>


        <!-- Totals -->
        <div
            class="
                ml-auto
                max-w-md
                space-y-3
                border-t border-black/10
                p-6
            "
        >
            <div class="flex justify-between">
                <span class="text-neutral-500">
                    Total HT
                </span>

                <strong>
                    <?= number_format(
                        (float) $sale['total_ht'],
                        2,
                        ',',
                        ' '
                    ) ?> €
                </strong>
            </div>

            <div class="flex justify-between">
                <span class="text-neutral-500">
                    TVA
                </span>

                <strong>
                    <?= number_format(
                        (float) $sale['total_vat'],
                        2,
                        ',',
                        ' '
                    ) ?> €
                </strong>
            </div>

            <div
                class="
                    flex justify-between
                    border-t border-black/10
                    pt-4
                    text-xl
                "
            >
                <span class="font-black">
                    Total TTC
                </span>

                <strong>
                    <?= number_format(
                        (float) $sale['total_ttc'],
                        2,
                        ',',
                        ' '
                    ) ?> €
                </strong>
            </div>
        </div>
    </section>

</section>

<?php require BACKEND_FOOTER_PATH; ?>