<?php require FRONTEND_HEADER_PATH; ?>

    <section class="products-section" id="products">
        <div class="section-heading">
            <p class="section-heading__eyebrow">
                Les préférés de nos clients
            </p>
            <h2>Nos meilleures ventes</h2>
            <p>
                Découvrez les produits les plus appréciés par nos clients.
            </p>
        </div>

        <?php if (!empty($bestSellers)): ?>

            <div class="product-filters">
                <?php foreach ($popularCategories as $categoryId => $categoryName): ?>
                    <button
                            type="button"
                            class="product-filter"
                            data-category="<?= $categoryId ?>"
                    >
                        <?= htmlspecialchars(
                                $categoryName,
                                ENT_QUOTES,
                                'UTF-8'
                        ) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="products-grid">
                <?php foreach ($bestSellers as $product): ?>
                    <article class="product-card"
                             data-category="<?= (int)$product['category_id'] ?>">

                        <?php if (!empty($product['image'])): ?>
                            <?php
                            $images = explode(';', $product['image'] ?? '');
                            $mainImage = $images[0] ?? 'placeholder.jpg';
                            ?>
                            <div class="product-card__image">
                                <img
                                        src="<?= PRODUCT_IMAGES_URL . '/' . htmlspecialchars($mainImage ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= PRODUCT_IMAGES_URL . '/' . htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>
                        <?php endif; ?>

                        <div class="product-card__content">
                            <p class="product-card__category">
                                <?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <h3>
                                <?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="product-card__description">
                                <?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <?php if (!empty($product['origin'])): ?>
                                <p class="product-card__origin">
                                    Origine :
                                    <?= htmlspecialchars($product['origin'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>

                            <div class="product-card__footer">
                                <strong class="product-card__price">
                                    <?= number_format((float)$product['price'], 2, ',', ' ') ?> €
                                </strong>

                                <a href="#" class="button button--primary">
                                    Voir le produit
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun produit disponible pour le moment.</p>
        <?php endif; ?>
    </section>

<?php require FRONTEND_FOOTER_PATH; ?>