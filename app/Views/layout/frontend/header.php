<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'CafThé') ?></title>

    <link rel="stylesheet" href="/public/assets/css/frontend.css">
</head>

<body>

<header class="frontend-header">
    <a href="/public/index.php?route=/" class="frontend-logo">
        CafThé
    </a>

    <nav class="frontend-nav">
        <a href="/public/index.php?route=/">Home</a>
        <a href="#products">Products</a>
        <a href="#blog">Blog</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="/public/index.php?route=/login">Login</a>
    </nav>
</header>

<main class="frontend-main">