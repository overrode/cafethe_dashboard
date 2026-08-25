<?php

$title = 'Dashboard - CafThé';

/** @var array $stats */
/** @var array $topProducts */
/** @var array $topClients */
/** @var array $recentSales */
/** @var array $lowStockProducts */

require BACKEND_HEADER_PATH;

$statusLabels = [
    'pending' => 'En attente',
    'preparing' => 'En préparation',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée',
];

$paymentLabels = [
    'pending' => 'Paiement en attente',
    'paid' => 'Payée',
    'failed' => 'Échec paiement',
    'refunded' => 'Remboursée',
];
?>

<section class="py-8">

    <!-- PAGE HEADER -->
    <div
        class="
            mb-8 flex flex-wrap
            items-end justify-between gap-5
        "
    >
        <div>
            <p
                class="
                    mb-2 text-xs font-bold
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
                Vue d’ensemble
            </h1>

            <p class="mt-2 text-neutral-500">
                Activité et performances de CafThé.
            </p>
        </div>

        <a
            href="/public/index.php?route=/dashboard/sales/create"
            class="
                rounded-full bg-black
                px-5 py-3
                font-bold text-white
                transition
                hover:-translate-y-0.5
                !text-white
            "
        >
            + Nouvelle vente
        </a>
    </div>


    <!-- KPI CARDS -->
    <div
        class="
            grid gap-4
            sm:grid-cols-2
            lg:grid-cols-4
        "
    >
        <article
            class="
                rounded-[28px]
                border border-white/70
                bg-white/45 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-neutral-500">
                Chiffre d'affaires
            </p>

            <p
                class="
                    mt-3 text-3xl
                    font-black tracking-tight
                "
            >
                <?= number_format(
                    (float) $stats['revenue'],
                    2,
                    ',',
                    ' '
                ) ?> €
            </p>

            <p class="mt-2 text-xs text-neutral-400">
                Ventes payées
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-white/70
                bg-white/45 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-neutral-500">
                Ventes
            </p>

            <p class="mt-3 text-3xl font-black">
                <?= (int) $stats['sales_count'] ?>
            </p>

            <p class="mt-2 text-xs text-neutral-400">
                Commandes payées
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-white/70
                bg-white/45 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-neutral-500">
                Panier moyen
            </p>

            <p class="mt-3 text-3xl font-black">
                <?= number_format(
                    (float) $stats['average_basket'],
                    2,
                    ',',
                    ' '
                ) ?> €
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-white/70
                bg-white/45 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-neutral-500">
                Clients
            </p>

            <p class="mt-3 text-3xl font-black">
                <?= (int) $stats['clients_count'] ?>
            </p>

            <p class="mt-2 text-xs text-neutral-400">
                Clients enregistrés
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-white/70
                bg-white/45 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-neutral-500">
                Produits actifs
            </p>

            <p class="mt-3 text-3xl font-black">
                <?= (int) $stats['active_products_count'] ?>
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-amber-200/70
                bg-amber-50/60 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-amber-800">
                Paiements en attente
            </p>

            <p class="mt-3 text-3xl font-black text-amber-900">
                <?= (int) $stats['pending_payments_count'] ?>
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-blue-200/70
                bg-blue-50/60 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-blue-800">
                En préparation
            </p>

            <p class="mt-3 text-3xl font-black text-blue-900">
                <?= (int) $stats['preparing_sales_count'] ?>
            </p>
        </article>


        <article
            class="
                rounded-[28px]
                border border-red-200/70
                bg-red-50/60 p-6
                shadow-lg backdrop-blur-2xl
            "
        >
            <p class="text-sm font-semibold text-red-800">
                Stock faible
            </p>

            <p class="mt-3 text-3xl font-black text-red-900">
                <?= count($lowStockProducts) ?>
            </p>

            <p class="mt-2 text-xs text-red-700/60">
                Produits à surveiller
            </p>
        </article>
    </div>


    <!-- TOP PRODUCTS + TOP CLIENTS -->
    <div
        class="
            mt-8 grid gap-6
            lg:grid-cols-2
        "
    >

        <!-- TOP PRODUCTS -->
        <section
            class="
                rounded-[30px]
                border border-white/70
                bg-white/45 p-6
                shadow-xl backdrop-blur-2xl
            "
        >
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p
                        class="
                            text-xs font-bold
                            uppercase tracking-wide
                            text-neutral-400
                        "
                    >
                        Performance
                    </p>

                    <h2 class="mt-1 text-2xl font-black">
                        Produits les plus vendus
                    </h2>
                </div>
            </div>

            <?php if (empty($topProducts)): ?>

                <p class="text-neutral-500">
                    Aucune vente enregistrée.
                </p>

            <?php else: ?>

                <div class="space-y-3">
                    <?php foreach ($topProducts as $index => $product): ?>

                        <div
                            class="
                                flex items-center
                                justify-between gap-5
                                rounded-2xl
                                bg-white/60
                                px-4 py-4
                            "
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="
                                        flex h-10 w-10
                                        items-center justify-center
                                        rounded-full bg-black
                                        text-sm font-black
                                        text-white
                                    "
                                >
                                    <?= $index + 1 ?>
                                </div>

                                <div>
                                    <p class="font-black">
                                        <?= htmlspecialchars(
                                            $product['name']
                                        ) ?>
                                    </p>

                                    <p class="text-xs text-neutral-400">
                                        <?= htmlspecialchars(
                                            $product['sku']
                                        ) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-black">
                                    <?= number_format(
                                        (float) $product['quantity_sold'],
                                        0,
                                        ',',
                                        ' '
                                    ) ?>
                                </p>

                                <p class="text-xs text-neutral-400">
                                    unités
                                </p>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </section>


        <!-- TOP CLIENTS -->
        <section
            class="
                rounded-[30px]
                border border-white/70
                bg-white/45 p-6
                shadow-xl backdrop-blur-2xl
            "
        >
            <p
                class="
                    text-xs font-bold
                    uppercase tracking-wide
                    text-neutral-400
                "
            >
                Fidélité
            </p>

            <h2 class="mt-1 text-2xl font-black">
                Meilleurs clients
            </h2>

            <?php if (empty($topClients)): ?>

                <p class="mt-6 text-neutral-500">
                    Aucun client avec une vente payée.
                </p>

            <?php else: ?>

                <div class="mt-6 space-y-3">
                    <?php foreach ($topClients as $index => $client): ?>

                        <div
                            class="
                                flex items-center
                                justify-between gap-5
                                rounded-2xl
                                bg-white/60
                                px-4 py-4
                            "
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="
                                        flex h-10 w-10
                                        items-center justify-center
                                        rounded-full
                                        bg-black/5
                                        text-sm font-black
                                    "
                                >
                                    <?= $index + 1 ?>
                                </div>

                                <div>
                                    <p class="font-black">
                                        <?= htmlspecialchars(
                                            $client['name']
                                        ) ?>
                                    </p>

                                    <p class="text-xs text-neutral-400">
                                        <?= (int) $client['sales_count'] ?>
                                        vente(s)
                                    </p>
                                </div>
                            </div>

                            <strong>
                                <?= number_format(
                                    (float) $client['revenue'],
                                    2,
                                    ',',
                                    ' '
                                ) ?> €
                            </strong>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </section>
    </div>


    <!-- LOW STOCK -->
    <section
        class="
            mt-8
            rounded-[30px]
            border border-white/70
            bg-white/45
            p-6 shadow-xl
            backdrop-blur-2xl
        "
    >
        <h2 class="text-2xl font-black">
            Stock faible
        </h2>

        <?php if (empty($lowStockProducts)): ?>

            <p class="mt-4 text-neutral-500">
                Aucun produit en stock faible.
            </p>

        <?php else: ?>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="
                            border-b border-black/10
                            text-xs uppercase
                            tracking-wide
                            text-neutral-500
                        "
                    >
                        <tr>
                            <th class="px-4 py-3">Produit</th>
                            <th class="px-4 py-3">Catégorie</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5">
                        <?php foreach ($lowStockProducts as $product): ?>

                            <tr>
                                <td class="px-4 py-4 font-bold">
                                    <?= htmlspecialchars($product['name']) ?>
                                </td>

                                <td class="px-4 py-4">
                                    <?= htmlspecialchars(
                                        $product['category_name']
                                    ) ?>
                                </td>

                                <td class="px-4 py-4 text-neutral-500">
                                    <?= htmlspecialchars($product['sku']) ?>
                                </td>

                                <td
                                    class="
                                        px-4 py-4
                                        text-right font-black
                                        text-red-700
                                    "
                                >
                                    <?= number_format(
                                        (float) $product['stock'],
                                        0,
                                        ',',
                                        ' '
                                    ) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>


    <!-- RECENT SALES -->
    <section
        class="
            mt-8
            rounded-[30px]
            border border-white/70
            bg-white/45
            p-6 shadow-xl
            backdrop-blur-2xl
        "
    >
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-black">
                Dernières ventes
            </h2>

            <a
                href="/public/index.php?route=/dashboard/sales"
                class="text-sm font-bold hover:underline"
            >
                Voir toutes →
            </a>
        </div>

        <div class="mt-5 space-y-3">
            <?php foreach ($recentSales as $sale): ?>

                <div
                    class="
                        flex flex-wrap
                        items-center justify-between
                        gap-4
                        rounded-2xl
                        bg-white/60
                        px-5 py-4
                    "
                >
                    <div>
                        <p class="font-black">
                            Vente #<?= (int) $sale['id'] ?>
                        </p>

                        <p class="mt-1 text-sm text-neutral-500">
                            <?= htmlspecialchars(
                                $sale['client_name']
                                ?? 'Client non renseigné'
                            ) ?>
                            ·
                            <?= htmlspecialchars($sale['sale_date']) ?>
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            class="
                                rounded-full
                                bg-black/5
                                px-3 py-1
                                text-xs font-bold
                            "
                        >
                            <?= htmlspecialchars(
                                $paymentLabels[$sale['payment_status']]
                                ?? $sale['payment_status']
                            ) ?>
                        </span>

                        <span
                            class="
                                rounded-full
                                bg-black/5
                                px-3 py-1
                                text-xs font-bold
                            "
                        >
                            <?= htmlspecialchars(
                                $statusLabels[$sale['status']]
                                ?? $sale['status']
                            ) ?>
                        </span>

                        <strong class="min-w-24 text-right">
                            <?= number_format(
                                (float) $sale['total_ttc'],
                                2,
                                ',',
                                ' '
                            ) ?> €
                        </strong>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </section>

</section>

<?php require BACKEND_FOOTER_PATH; ?>