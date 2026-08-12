    <section
            id="products"
            x-data="productFilter"
            class="mt-20 py-16"
    >
        <div class="mb-10 max-w-2xl">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
                Les préférés de nos clients
            </p>

            <h2 class="text-4xl font-black tracking-[-0.06em] text-black md:text-6xl">
                Nos produits populaires
            </h2>

            <p class="mt-5 text-lg leading-7 text-neutral-600">
                Découvrez les produits les plus appréciés par nos clients.
            </p>
        </div>
        <?php if (!empty($popularProducts)): ?>
            <div class="mb-8 flex flex-wrap gap-3">
                <button
                        type="button"
                        @click="filter('all')"
                        :class="isActive('all')
                            ? 'bg-black text-white'
                            : 'bg-white/40 text-black'
                        "
                        class="
                            rounded-full
                            border border-white/70
                            px-5 py-2.5
                            font-semibold
                            shadow-md
                            backdrop-blur-xl
                            transition
                            hover:-translate-y-0.5
                        "
                >
                    Tous
                </button>
                <?php foreach ($popularCategories as $categoryId => $categoryName): ?>
                    <button
                        type="button"
                        @click="filter('<?= (int)$categoryId ?>')"
                        :class="isActive('<?= (int)$categoryId ?>')
                            ? 'bg-black text-white'
                            : 'bg-white/40 text-black'
                        "
                        class="
                            rounded-full
                            border border-white/70
                            px-5 py-2.5
                            font-semibold
                            shadow-md
                            backdrop-blur-xl
                            transition
                            hover:-translate-y-0.5
                        "
                    >
                        <?= htmlspecialchars(
                                $categoryName,
                                ENT_QUOTES,
                                'UTF-8'
                        ) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div
                class="
                    grid grid-cols-1 gap-6
                    transition-all duration-300 ease-out
                    md:grid-cols-2
                    lg:grid-cols-3
                "
                :class="changing
                    ? 'opacity-0 scale-[0.985] translate-y-2 blur-[2px]'
                    : 'opacity-100 scale-100 translate-y-0 blur-0'
                "
            >
                <?php foreach ($popularProducts as $product): ?>
                    <article
                        x-show="isVisible('<?= (int)$product['category_id'] ?>')"
                        class="
                            flex flex-col overflow-hidden
                            rounded-[28px]
                            border border-white/70
                            bg-white/40
                            shadow-[0_18px_45px_rgba(0,0,0,0.12)]
                            backdrop-blur-2xl
                            transition duration-300
                            hover:-translate-y-2
                            hover:shadow-[0_28px_60px_rgba(0,0,0,0.18)]
                        "
                    >
                        <?php if (!empty($product['image'])): ?>
                            <?php
                            $images = explode(';', $product['image']);
                            $mainImage = $images[0] ?: 'placeholder.jpg';
                            ?>

                            <div class="h-56 overflow-hidden bg-black/5">
                                <img
                                    class="
                                        h-full w-full
                                        object-cover
                                        transition duration-500
                                        hover:scale-105
                                    "
                                        src="<?= PRODUCT_IMAGES_URL . '/' . htmlspecialchars(
                                                $mainImage,
                                                ENT_QUOTES,
                                                'UTF-8'
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                                $product['name'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                        ) ?>"
                                >
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-1 flex-col p-6">

                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.12em] text-neutral-500">
                                <?= htmlspecialchars(
                                        $product['category_name'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                ) ?>
                            </p>

                            <h3 class="text-2xl font-bold tracking-tight text-black">
                                <?= htmlspecialchars(
                                        $product['name'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                ) ?>
                            </h3>

                            <p class="my-4 leading-6 text-neutral-600">
                                <?= htmlspecialchars(
                                        $product['description'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                ) ?>
                            </p>

                            <?php if (!empty($product['origin'])): ?>
                                <p class="mb-5 text-sm font-semibold text-neutral-700">
                                    Origine :
                                    <?= htmlspecialchars(
                                            $product['origin'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                    ) ?>
                                </p>
                            <?php endif; ?>

                            <div class="mt-auto flex items-center justify-between gap-4">
                                <strong class="whitespace-nowrap text-xl font-bold">
                                    <?= number_format(
                                            (float)$product['price'],
                                            2,
                                            ',',
                                            ' '
                                    ) ?> €
                                </strong>

                                <a
                                    href="#"
                                    class="
                                        rounded-full
                                        bg-black
                                        px-5 py-3
                                        font-bold
                                        text-white
                                        transition
                                        hover:-translate-y-0.5
                                    "
                                >
                                    Voir le produit
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-neutral-600">
                Aucun produit disponible pour le moment.
            </p>
        <?php endif; ?>
    </section>
