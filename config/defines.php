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