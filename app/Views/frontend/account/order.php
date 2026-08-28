<?php

use App\Models\Sale;

require FRONTEND_HEADER_PATH;

/** @var sale $sale */
/** @var array $items */


$orderLabels = [
    'pending' => 'En attente',
    'preparing' => 'En préparation',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée',
];

$paymentLabels = [
    'pending' => 'En attente',
    'paid' => 'Payé',
    'failed' => 'Échoué',
    'refunded' => 'Remboursé',
];

$deliveryLabels = [
    'pending' => 'En attente',
    'ready_for_pickup' => 'Prête au retrait',
    'shipped' => 'Expédiée',
    'delivered' => 'Livrée',
    'collected' => 'Retirée',
];

$deliveryMethodLabels = [
    'magasin' => 'Retrait en magasin',
    'livraison' => 'Livraison',
];

$paymentMethodLabels = [
    'cb' => 'Carte bancaire',
    'especes' => 'Espèces',
    'cheque' => 'Chèque',
    'virement' => 'Virement bancaire',
];
?>

<section class="mx-auto max-w-6xl px-4 py-10">

    <!-- Page heading. -->
    <div class="mb-8">
        <a
            href="/public/index.php?route=/account/orders"
            class="text-sm font-bold text-neutral-500 hover:text-black"
        >
            ← Mes commandes
        </a>

        <div
            class="
                mt-3 flex flex-col
                justify-between gap-4
                md:flex-row md:items-end
            "
        >
            <div>
                <p
                    class="
                        text-xs font-bold
                        uppercase tracking-wide
                        text-neutral-500
                    "
                >
                    Commande
                </p>

                <h1 class="mt-1 text-4xl font-black tracking-[-0.04em]">
                    #<?= (int) $sale['id'] ?>
                </h1>

                <p class="mt-2 text-neutral-500">
                    <?= htmlspecialchars(
                        date(
                            'd/m/Y à H:i',
                            strtotime($sale['sale_date'])
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            </div>

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


    <!-- Order summary. -->
    <div
        class="
            mb-6 grid gap-4
            md:grid-cols-3
        "
    >
        <div
            class="
                rounded-3xl
                border border-white/70
                bg-white/50
                p-5
                backdrop-blur-2xl
            "
        >
            <p class="text-xs font-bold uppercase text-neutral-500">
                Statut
            </p>

            <p class="mt-2 font-black">
                <?= htmlspecialchars(
                    $orderLabels[$sale['status']]
                        ?? $sale['status'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        </div>

        <div
            class="
                rounded-3xl
                border border-white/70
                bg-white/50
                p-5
                backdrop-blur-2xl
            "
        >
            <p class="text-xs font-bold uppercase text-neutral-500">
                Paiement
            </p>

            <p class="mt-2 font-black">
                <?= htmlspecialchars(
                    $paymentLabels[$sale['payment_status']]
                        ?? $sale['payment_status'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p class="mt-1 text-sm text-neutral-500">
                <?= htmlspecialchars(
                    $paymentMethodLabels[$sale['payment_method']]
                        ?? $sale['payment_method'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        </div>

        <div
            class="
                rounded-3xl
                border border-white/70
                bg-white/50
                p-5
                backdrop-blur-2xl
            "
        >
            <p class="text-xs font-bold uppercase text-neutral-500">
                Livraison
            </p>

            <p class="mt-2 font-black">
                <?= htmlspecialchars(
                    $deliveryLabels[$sale['delivery_status']]
                        ?? $sale['delivery_status'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p class="mt-1 text-sm text-neutral-500">
                <?= htmlspecialchars(
                    $deliveryMethodLabels[$sale['delivery_method']]
                        ?? $sale['delivery_method'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        </div>
    </div>


    <!-- Order products. -->
    <div
        class="
            overflow-hidden
            rounded-[28px]
            border border-white/70
            bg-white/50
            shadow-[0_18px_45px_rgba(0,0,0,0.10)]
            backdrop-blur-2xl
        "
    >
        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="border-b border-black/10">
                    <tr
                        class="
                            text-xs uppercase
                            tracking-wide
                            text-neutral-500
                        "
                    >
                        <th class="px-5 py-4">
                            Produit
                        </th>

                        <th class="px-5 py-4 text-right">
                            Quantité / Poids
                        </th>

                        <th class="px-5 py-4 text-right">
                            Prix HT
                        </th>

                        <th class="px-5 py-4 text-right">
                            TVA
                        </th>

                        <th class="px-5 py-4 text-right">
                            Total TTC
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-black/5">

                <?php foreach ($items as $item): ?>

                    <?php
                    $imageUrl = !empty($item['product_image'])
                        ? '/public/assets/images/products/'
                            . $item['product_image']
                        : null;
                    ?>

                    <tr>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        flex h-12 w-12
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-2xl
                                        bg-black/5
                                    "
                                >
                                    <?php if ($imageUrl): ?>
                                        <img
                                            src="<?= htmlspecialchars(
                                                $imageUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt=""
                                            class="h-full w-full object-cover"
                                        >
                                    <?php else: ?>
                                        <span class="font-black">
                                            <?= htmlspecialchars(
                                                strtoupper(
                                                    substr(
                                                        $item['product_name'],
                                                        0,
                                                        1
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <p class="font-black">
                                        <?= htmlspecialchars(
                                            $item['product_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </p>

                                    <p
                                        class="
                                            mt-1 text-xs
                                            text-neutral-500
                                        "
                                    >
                                        <?= htmlspecialchars(
                                            $item['product_sku'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </p>
                                </div>

                            </div>
                        </td>

                        <td class="px-5 py-4 text-right font-bold">
                            <?php if ($item['sale_type'] === 'poids'): ?>
                                <?= number_format(
                                    (float) $item['quantity'],
                                    0,
                                    ',',
                                    ' '
                                ) ?> g
                            <?php else: ?>
                                <?= number_format(
                                    (float) $item['quantity'],
                                    0,
                                    ',',
                                    ' '
                                ) ?>
                            <?php endif; ?>
                        </td>

                        <td class="px-5 py-4 text-right">
                             <?= number_format(
                                (float) $item['unit_price'],
                                2,
                                ',',
                                ' '
                            ) ?> €

                            <?= $item['sale_type'] === 'poids'
                                ? '/ kg'
                                : '/ unité' ?>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <?= number_format(
                                (float) $item['vat_rate'],
                                2,
                                ',',
                                ' '
                            ) ?> %
                        </td>

                        <td class="px-5 py-4 text-right font-black">
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
    </div>


    <!-- Order totals. -->
    <div
        class="
            mt-6 ml-auto
            w-full max-w-md
            rounded-[28px]
            border border-white/70
            bg-white/50
            p-6
            backdrop-blur-2xl
        "
    >
        <div class="flex justify-between gap-4">
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

        <div class="mt-3 flex justify-between gap-4">
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
                mt-4 flex
                justify-between gap-4
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

<?php require FRONTEND_FOOTER_PATH; ?>