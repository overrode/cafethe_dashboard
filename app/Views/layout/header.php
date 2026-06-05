<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'CafThé - Dashboard vendeur') ?></title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
    <header>
        <h1>CafThé - Dashboard vendeur</h1>

        <nav>
            <a href="/public/index.php?route=/dashboard">Dashboard</a>
            <a href="/public/index.php?route=/products">Produits</a>
            <a href="/public/index.php?route=/clients">Clients</a>
            <a href="/public/index.php?route=/sales">Ventes</a>
            <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                <a href="/public/index.php?route=/users">Utilisateurs</a>
            <?php endif; ?>
            <a href="/public/index.php?route=/logout">Déconnexion</a>
            <?php if (!empty($_SESSION['user'])): ?>
                <span>
                    Connecté : <?= htmlspecialchars($_SESSION['user']['name']) ?>
                    (<?= htmlspecialchars($_SESSION['user']['role']) ?>)
                </span>
            <?php endif; ?>
        </nav>
    </header>

    <main>