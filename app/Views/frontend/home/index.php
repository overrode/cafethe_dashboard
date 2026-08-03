<?php require FRONTEND_HEADER_PATH; ?>

<section class="products-section" id="products">
    <div class="section-heading">
        <p class="section-heading__eyebrow">
            Customer favourites
        </p>

        <h2>Our best sellers</h2>

        <p>
            Discover the products most appreciated by our customers.
        </p>
    </div>

    <div class="products-grid">
        <?php foreach ($bestSellers as $product): ?>
            <article class="product-card">
                <div class="product-card__content">
                    <p class="product-card__category">
                        <?= htmlspecialchars($product['category_name'] ?? 'CafThé') ?>
                    </p>

                    <h3>
                        <?= htmlspecialchars($product['name']) ?>
                    </h3>

                    <p class="product-card__description">
                        <?= htmlspecialchars(
                            $product['description'] ?? 'Discover this selected product.'
                        ) ?>
                    </p>

                    <div class="product-card__footer">
                        <strong class="product-card__price">
                            <?= number_format((float) $product['price'], 2, ',', ' ') ?> €
                        </strong>

                        <a
                            href="/public/index.php?route=/product&id=<?= (int) $product['id'] ?>"
                            class="button button--primary"
                        >
                            View product
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require FRONTEND_FOOTER_PATH; ?>