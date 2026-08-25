<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'CafThé - Dashboard vendeur') ?></title>
    <link rel="stylesheet" href="/public/assets/css/backend.css">
    <link rel="stylesheet" href="/public/assets/css/tailwind.css">
</head>
<body>
<header>
    <h1>CafThé - Dashboard vendeur</h1>

    <nav>
        <a href="/public/index.php?route=/dashboard">
            Dashboard
        </a>

        <a href="/public/index.php?route=/dashboard/products">
            Produits
        </a>

        <a href="/public/index.php?route=/dashboard/clients">
            Clients
        </a>

        <a href="/public/index.php?route=/dashboard/sales">
            Ventes
        </a>

        <?php if (
                !empty($_SESSION['user'])
                && $_SESSION['user']['role'] === 'admin'
        ): ?>
            <a href="/public/index.php?route=/dashboard/users">
                Utilisateurs
            </a>
        <?php endif; ?>

        <a
            href="/public/index.php?route=/"
            class="
                rounded-full
                border border-black/20
                px-4 py-2
                font-semibold
                transition
                hover:bg-black
                hover:text-white
            "
        >
            Voir le site
        </a>
        <a href="/public/index.php?route=/logout"
           class="text-red-500!"
        >
            Déconnexion
        </a>
    </nav>
</header>

<main>