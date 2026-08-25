<?php

declare(strict_types=1);

require FRONTEND_HEADER_PATH;

// Friendly status labels.
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
?>

<section class="mx-auto max-w-6xl px-4 py-10">

    <!-- Page heading. -->
    <div class="mb-8">
        <a
            href="/public/index.php?route=/account"
            class="text-sm font-bold text-neutral-500 hover:text-black"
        >
            ← Mon compte
        </a>

        <h1 class="mt-3 text-4xl font-black tracking-[-0.04em]">
            Mes commandes
        </h1>

        <p class="mt-2 text-neutral-600">
            Consultez l’historique de vos commandes.
        </p>
    </div>


    <?php if (empty($sales)): ?>

        <!-- Empty order history. -->
        <div
            class="
                rounded-[32px]
                border border-white/70
                bg-white/50
                p-10
                text-center
                shadow-[0_20px_60px_rgba(0,0,0,0.12)]
                backdrop-blur-2xl
            "
        >
            <h2 class="text-2xl font-black">
                Aucune commande
            </h2>

            <p class="mt-2 text-neutral-500">
                Vous n’avez pas encore passé de commande.
            </p>

            <a
                href="/public/index.php?route=/products"
                class="
                    mt-6 inline-flex
                    rounded-full
                    bg-black
                    px-6 py-3
                    font-bold text-white
                "
            >
                Voir les produits
            </a>
        </div>

    <?php else: ?>

        <!-- Orders. -->
        <div class="space-y-4">

            <?php foreach ($sales as $sale): ?>

                <article
                    class="
                        rounded-[28px]
                        border border-white/70
                        bg-white/50
                        p-6
                        shadow-[0_18px_45px_rgba(0,0,0,0.10)]
                        backdrop-blur-2xl
                    "
                >
                    <div
                        class="
                            flex flex-col
                            justify-between gap-5
                            md:flex-row md:items-center
                        "
                    >
                        <a
                            href="/public/index.php?route=/account/order&id=<?= (int) $sale['id'] ?>"
                            class="
                                inline-flex rounded-full
                                border border-black/20
                                px-4 py-2
                                text-sm font-bold
                                transition
                                hover:bg-black
                                hover:text-white
                            "
                        >
                            Voir la commande
                        </a>

                        <!-- Order identity. -->
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">
                                Commande
                            </p>

                            <h2 class="mt-1 text-xl font-black">
                                #<?= (int) $sale['id'] ?>
                            </h2>

                            <p class="mt-1 text-sm text-neutral-500">
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


                        <!-- Order statuses. -->
                        <div class="flex flex-wrap gap-2">

                            <span
                                class="
                                    rounded-full
                                    bg-black/5
                                    px-3 py-1
                                    text-xs font-bold
                                "
                            >
                                <?= htmlspecialchars(
                                    $orderLabels[$sale['status']]
                                        ?? $sale['status'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                            <span
                                class="
                                    rounded-full
                                    bg-green-100
                                    px-3 py-1
                                    text-xs font-bold
                                    text-green-800
                                "
                            >
                                Paiement :
                                <?= htmlspecialchars(
                                    $paymentLabels[$sale['payment_status']]
                                        ?? $sale['payment_status'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                            <span
                                class="
                                    rounded-full
                                    bg-blue-100
                                    px-3 py-1
                                    text-xs font-bold
                                    text-blue-800
                                "
                            >
                                Livraison :
                                <?= htmlspecialchars(
                                    $deliveryLabels[$sale['delivery_status']]
                                        ?? $sale['delivery_status'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                        </div>


                        <!-- Order total. -->
                        <div class="md:text-right">
                            <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">
                                Total TTC
                            </p>

                            <p class="mt-1 text-2xl font-black">
                                <?= number_format(
                                    (float) $sale['total_ttc'],
                                    2,
                                    ',',
                                    ' '
                                ) ?> €
                            </p>
                        </div>

                    </div>
                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>