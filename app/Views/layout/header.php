<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'CafThé - Dashboard vendeur') ?></title>
</head>
<body>
    <h1>CafThé - Dashboard vendeur</h1>

    <nav>
        <a href="/public/index.php?route=/dashboard">Dashboard</a> |
        <a href="/public/index.php?route=/products">Produits</a> |
        <a href="/public/index.php?route=/clients">Clients</a> |
        <a href="/public/index.php?route=/sales">Ventes</a>
    </nav>

    <hr>