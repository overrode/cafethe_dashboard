<section
    id="products"
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
        <div
            id="popular-products-app"
            data-products="<?= htmlspecialchars(
                json_encode($popularProducts),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            data-categories="<?= htmlspecialchars(
                json_encode($popularCategories),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            data-images-url="<?= htmlspecialchars(
                PRODUCT_IMAGES_URL,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        ></div>
    <?php else: ?>
        <p class="text-neutral-600">
            Aucun produit disponible pour le moment.
        </p>
    <?php endif; ?>
</section>