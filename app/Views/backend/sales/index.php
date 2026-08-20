<?php

$title = 'Ventes - CafThé';

/** @var array $sales */

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

$deliveryStatusLabels = [
    'pending' => 'En attente',
    'ready_for_pickup' => 'Prête à retirer',
    'shipped' => 'Expédiée',
    'delivered' => 'Livrée',
    'collected' => 'Retirée',
];

$paymentMethodLabels = [
    'cb' => 'Carte bancaire',
    'virement' => 'Virement',
    'especes' => 'Espèces',
    'cheque' => 'Chèque',
];

$deliveryMethodLabels = [
    'livraison' => 'Livraison',
    'magasin' => 'Retrait magasin',
];

$sourceLabels = [
    'dashboard' => 'Magasin',
    'website' => 'Site web',
];

$statusClasses = [
    'pending' => 'bg-amber-100 text-amber-800',
    'preparing' => 'bg-blue-100 text-blue-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800',
];

$paymentStatusClasses = [
    'pending' => 'bg-amber-100 text-amber-800',
    'paid' => 'bg-green-100 text-green-800',
    'failed' => 'bg-red-100 text-red-800',
    'refunded' => 'bg-purple-100 text-purple-800',
];

$deliveryStatusClasses = [
    'pending' => 'bg-neutral-100 text-neutral-700',
    'ready_for_pickup' => 'bg-blue-100 text-blue-800',
    'shipped' => 'bg-indigo-100 text-indigo-800',
    'delivered' => 'bg-green-100 text-green-800',
    'collected' => 'bg-green-100 text-green-800',
];

require BACKEND_HEADER_PATH;
?>

    <section class="py-8">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
                    Dashboard vendeur
                </p>

                <h1 class="text-4xl font-black tracking-[-0.05em]">
                    Ventes
                </h1>
            </div>

            <a
                href="/public/index.php?route=/sales/create"
                class="
                    rounded-full
                    bg-black
                    px-5 py-3
                    font-bold text-white
                    transition
                    hover:-translate-y-0.5
                "
            >
                Nouvelle vente
            </a>
        </div>

        <?php if (!empty($sales)): ?>
            <div
                class="
                    overflow-x-auto
                    rounded-[28px]
                    border border-white/70
                    bg-white/40
                    shadow-[0_18px_45px_rgba(0,0,0,0.12)]
                    backdrop-blur-2xl
                "
            >
                <table class="w-full text-left">
                    <thead class="border-b border-black/10">
                    <tr class="text-sm uppercase tracking-wide text-neutral-500">
                        <th class="px-5 py-4">ID</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Source</th>
                        <th class="px-5 py-4">Vendeur</th>
                        <th class="px-5 py-4">Client</th>
                        <th class="px-5 py-4">Paiement</th>
                        <th class="px-5 py-4">Livraison</th>
                        <th class="px-5 py-4 text-right">Total TTC</th>
                        <th class="px-5 py-4">Statut</th>
                        <th class="px-5 py-4">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5">
                    <?php foreach ($sales as $sale): ?>
                        <tr class="transition hover:bg-white/40">
                            <td class="px-5 py-4 font-bold">
                                #<?= (int) $sale['id'] ?>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-neutral-600">
                                <?= htmlspecialchars(
                                        $sale['sale_date'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                ) ?>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="
                                        inline-flex rounded-full
                                        bg-neutral-100
                                        px-3 py-1
                                        text-xs font-bold
                                        text-neutral-700
                                    "
                                >
                                    <?= htmlspecialchars(
                                        $sourceLabels[$sale['source']] ?? $sale['source'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars(
                                        $sale['user_name'] ?? 'Site web',
                                        ENT_QUOTES,
                                        'UTF-8'
                                ) ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars(
                                        $sale['client_name'] ?? 'Client non renseigné',
                                        ENT_QUOTES,
                                        'UTF-8'
                                ) ?>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold">
                                    <?= htmlspecialchars(
                                        $paymentMethodLabels[$sale['payment_method']]
                                            ?? $sale['payment_method'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </div>

                                <span
                                    class="
                                        mt-1 inline-flex rounded-full
                                        px-3 py-1
                                        text-xs font-bold
                                        <?= $paymentStatusClasses[$sale['payment_status']]
                                            ?? 'bg-neutral-100 text-neutral-700' ?>
                                    "
                                >
                                    <?= htmlspecialchars(
                                        $paymentStatusLabels[$sale['payment_status']]
                                            ?? $sale['payment_status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold">
                                    <?= htmlspecialchars(
                                        $deliveryMethodLabels[$sale['delivery_method']]
                                            ?? $sale['delivery_method'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </div>

                                <span
                                    class="
                                        mt-1 inline-flex rounded-full
                                        px-3 py-1
                                        text-xs font-bold
                                        <?= $deliveryStatusClasses[$sale['delivery_status']]
                                            ?? 'bg-neutral-100 text-neutral-700' ?>
                                    "
                                >
                                    <?= htmlspecialchars(
                                        $deliveryStatusLabels[$sale['delivery_status']]
                                            ?? $sale['delivery_status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right font-black">
                                <?= number_format(
                                        (float) $sale['total_ttc'],
                                        2,
                                        ',',
                                        ' '
                                ) ?> €
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="
                                        inline-flex rounded-full
                                        px-3 py-1
                                        text-xs font-bold
                                        <?= $statusClasses[$sale['status']]
                                            ?? 'bg-neutral-100 text-neutral-700' ?>
                                    "
                                >
                                    <?= htmlspecialchars(
                                        $statusLabels[$sale['status']] ?? $sale['status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </td>

<td class="px-5 py-4">
    <div class="flex flex-col items-start gap-2">

        <?php if (
            $sale['payment_status'] === 'pending'
            && $sale['status'] !== 'cancelled'
        ): ?>

            <form
                method="POST"
                action="/public/index.php?route=/sales/set-paid"
            >
                <input
                    type="hidden"
                    name="sale_id"
                    value="<?= (int) $sale['id'] ?>"
                >

                <button
                    type="submit"
                    class="
                        whitespace-nowrap
                        rounded-full
                        bg-black
                        px-4 py-2
                        text-sm font-bold text-white
                    "
                >
                    Marquer payé
                </button>
            </form>

        <?php endif; ?>


        <?php if (
            $sale['payment_status'] === 'paid'
            && $sale['status'] !== 'completed'
            && $sale['status'] !== 'cancelled'
        ): ?>

            <?php if (
                $sale['delivery_method'] === 'livraison'
                && $sale['delivery_status'] === 'pending'
            ): ?>

                <form
                    method="POST"
                    action="/public/index.php?route=/sales/set-delivery-status"
                >
                    <input
                        type="hidden"
                        name="sale_id"
                        value="<?= (int) $sale['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="delivery_status"
                        value="shipped"
                    >

                    <button
                        type="submit"
                        class="
                            whitespace-nowrap
                            rounded-full
                            bg-black
                            px-4 py-2
                            text-sm font-bold text-white
                        "
                    >
                        Expédier
                    </button>
                </form>

            <?php elseif (
                $sale['delivery_method'] === 'livraison'
                && $sale['delivery_status'] === 'shipped'
            ): ?>

                <form
                    method="POST"
                    action="/public/index.php?route=/sales/set-delivery-status"
                >
                    <input
                        type="hidden"
                        name="sale_id"
                        value="<?= (int) $sale['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="delivery_status"
                        value="delivered"
                    >

                    <button
                        type="submit"
                        class="
                            whitespace-nowrap
                            rounded-full
                            bg-black
                            px-4 py-2
                            text-sm font-bold text-white
                        "
                    >
                        Marquer livrée
                    </button>
                </form>

            <?php elseif (
                $sale['delivery_method'] === 'magasin'
                && $sale['delivery_status'] === 'pending'
            ): ?>

                <form
                    method="POST"
                    action="/public/index.php?route=/sales/set-delivery-status"
                >
                    <input
                        type="hidden"
                        name="sale_id"
                        value="<?= (int) $sale['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="delivery_status"
                        value="ready_for_pickup"
                    >

                    <button
                        type="submit"
                        class="
                            whitespace-nowrap
                            rounded-full
                            bg-black
                            px-4 py-2
                            text-sm font-bold text-white
                        "
                    >
                        Prête à retirer
                    </button>
                </form>

            <?php elseif (
                $sale['delivery_method'] === 'magasin'
                && $sale['delivery_status'] === 'ready_for_pickup'
            ): ?>

                <form
                    method="POST"
                    action="/public/index.php?route=/sales/set-delivery-status"
                >
                    <input
                        type="hidden"
                        name="sale_id"
                        value="<?= (int) $sale['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="delivery_status"
                        value="collected"
                    >

                    <button
                        type="submit"
                        class="
                            whitespace-nowrap
                            rounded-full
                            bg-black
                            px-4 py-2
                            text-sm font-bold text-white
                        "
                    >
                        Marquer retirée
                    </button>
                </form>

            <?php endif; ?>


            <?php if (
                $sale['delivery_status'] === 'delivered'
                || $sale['delivery_status'] === 'collected'
            ): ?>

                <form
                    method="POST"
                    action="/public/index.php?route=/sales/set-completed"
                >
                    <input
                        type="hidden"
                        name="sale_id"
                        value="<?= (int) $sale['id'] ?>"
                    >

                    <button
                        type="submit"
                        class="
                            whitespace-nowrap
                            rounded-full
                            bg-black
                            px-4 py-2
                            text-sm font-bold text-white
                        "
                    >
                        Terminer
                    </button>
                </form>

            <?php endif; ?>

        <?php endif; ?>


        <?php if (
            in_array(
                $sale['payment_status'],
                ['pending', 'failed'],
                true
            )
            && $sale['status'] !== 'cancelled'
            && $sale['status'] !== 'completed'
        ): ?>

            <form
                method="POST"
                action="/public/index.php?route=/sales/set-cancelled"
            >
                <input
                    type="hidden"
                    name="sale_id"
                    value="<?= (int) $sale['id'] ?>"
                >

                <button
                    type="submit"
                    class="
                        whitespace-nowrap
                        rounded-full
                        border border-red-300
                        px-4 py-2
                        text-sm font-bold
                        text-red-700
                    "
                >
                    Annuler
                </button>
            </form>

        <?php endif; ?>

    </div>
</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div
                class="
                    rounded-[28px]
                    border border-white/70
                    bg-white/40
                    p-10
                    text-center
                    shadow-md
                    backdrop-blur-2xl
                "
            >
                <p class="text-neutral-600">
                    Aucune vente pour le moment.
                </p>
            </div>
        <?php endif; ?>
    </section>

<?php require BACKEND_FOOTER_PATH; ?>