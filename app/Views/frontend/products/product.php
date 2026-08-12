<?php require FRONTEND_HEADER_PATH; ?>

<?php
$images = explode(';', $product['image'] ?? '');
$images = array_values(array_filter($images));
$mainImage = $images[0] ?? 'placeholder.jpg';
?>
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
    <section
            x-data="productPage(<?= htmlspecialchars(
                json_encode($mainImage),
                ENT_QUOTES,
                'UTF-8'
            ) ?>)"
            class="
            mt-16 grid grid-cols-1 gap-10
            rounded-[38px]
            border border-white/70
            bg-white/40
            p-6
            shadow-[0_25px_70px_rgba(0,0,0,0.12)]
            backdrop-blur-3xl
            md:grid-cols-2
            md:p-10
        "
    >
        <!-- Images -->
        <div>
            <div class="overflow-hidden rounded-[30px] bg-black/5">
                <img
                        :src="'<?= PRODUCT_IMAGES_URL ?>/' + activeImage"
                        alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                        class="h-[500px] w-full object-cover"
                >
            </div>

            <?php if (count($images) > 1): ?>
                <div class="mt-4 flex gap-3">
                    <?php foreach ($images as $image): ?>
                        <button type="button"
                                @click="selectImage(<?= htmlspecialchars(
                                    json_encode($image),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>)"
                                class="
                            h-20 w-20 overflow-hidden
                            rounded-2xl
                            border border-white/70
                            bg-white/40
                            transition
                            hover:scale-105
                        "
                        >
                            <img
                                    src="<?= PRODUCT_IMAGES_URL ?>/<?= htmlspecialchars(
                                        $image,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt=""
                                    class="h-full w-full object-cover"
                            >
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product info -->
        <div class="flex flex-col">
            <p class="text-sm font-bold uppercase tracking-[0.14em] text-neutral-500">
                <?= htmlspecialchars(
                    $product['category_name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <h1 class="mt-3 text-5xl font-black tracking-[-0.06em] text-black">
                <?= htmlspecialchars(
                    $product['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <?php if (!empty($product['origin'])): ?>
                <p class="mt-4 text-sm font-semibold text-neutral-500">
                    Origine :
                    <?= htmlspecialchars(
                        $product['origin'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            <?php endif; ?>

            <p class="mt-8 text-lg leading-8 text-neutral-600">
                <?= htmlspecialchars(
                    $product['description'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <div class="mt-8">
                <strong class="text-3xl font-black">
                    <?= number_format(
                        (float)$product['price'],
                        2,
                        ',',
                        ' '
                    ) ?> €
                </strong>
            </div>

            <p class="mt-4 text-sm font-semibold text-neutral-500">
                Stock :
                <?= (int)$product['stock'] ?>
            </p>

            <div class="mt-auto pt-10">
                <div class="mt-8">
                    <p class="mb-3 font-semibold">
                        Quantité
                    </p>
                    <div
                         class="
                        inline-flex items-center gap-5
                        rounded-full
                        border border-white/70
                        bg-white/40
                        px-3 py-2
                        shadow-md
                        backdrop-blur-xl
                    "
                    >
                        <button
                            type="button"
                            @click="decreaseQuantity()"
                            class="
                            flex h-10 w-10 items-center justify-center
                            rounded-full bg-black
                            text-xl font-bold text-white
                        "
                        >
                            −
                        </button>

                        <span
                                x-text="quantity"
                                class="min-w-8 text-center text-xl font-bold"
                        ></span>

                        <button
                                type="button"
                                @click="increaseQuantity()"
                                class="
                flex h-10 w-10 items-center justify-center
                rounded-full bg-black
                text-xl font-bold text-white
            "
                        >
                            +
                        </button>
                    </div>
                </div>
                <button
                        type="button"
                        class="
                        w-full rounded-full
                        bg-black
                        px-6 py-4
                        text-lg font-bold text-white
                        transition
                        hover:-translate-y-0.5
                    "
                >

                    Ajouter au panier
                </button>
            </div>
        </div>
    </section>

<?php require FRONTEND_FOOTER_PATH; ?>