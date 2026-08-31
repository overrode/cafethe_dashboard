<?php
//Constat
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEWS_PATH', APP_PATH . '/Views');

define(
    'BACKEND_HEADER_PATH',
    VIEWS_PATH . '/layout/backend/header.php'
);
define(
    'BACKEND_FOOTER_PATH',
    VIEWS_PATH . '/layout/backend/footer.php'
);
define(
    'FRONTEND_HEADER_PATH',
    VIEWS_PATH . '/layout/frontend/header.php'
);
define(
    'FRONTEND_FOOTER_PATH',
    VIEWS_PATH . '/layout/frontend/footer.php'
);
define('PUBLIC_PATH', ROOT_PATH . '/public');
define(
    'PRODUCT_IMAGES_PATH',
    PUBLIC_PATH . '/assets/images/products'
);
define(
    'PRODUCT_IMAGES_URL',
    '/public/assets/images/products'
);
define('PRODUCT_WEIGHT_STEP_GRAMS', 10);
define('GRAMS_PER_KILOGRAM', 1000);
define('APP_MODE_DEVELOPMENT', 'development');
define('APP_MODE_PRODUCTION', 'production');

define('APP_MODE', APP_MODE_DEVELOPMENT);
define(
    'IS_DEVELOPMENT',
    APP_MODE === APP_MODE_DEVELOPMENT
);

define(
    'IS_PRODUCTION',
    APP_MODE === APP_MODE_PRODUCTION
);