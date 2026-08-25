<?php

// Logged-in backoffice user, if any.
$currentUser = $_SESSION['user'] ?? null;

$userLabel = $currentUser
    ? ($currentUser['name'] ?: $currentUser['email'])
    : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'CafThé') ?></title>

    <link rel="stylesheet" href="/public/assets/css/tailwind.css">
</head>

<body
    class="
        min-h-screen
        bg-[radial-gradient(circle_at_15%_15%,rgba(255,255,255,0.95),transparent_32%),radial-gradient(circle_at_85%_20%,rgba(70,70,70,0.32),transparent_38%),radial-gradient(circle_at_45%_85%,rgba(0,0,0,0.14),transparent_42%),linear-gradient(135deg,#f5f5f5_0%,#bcbcbc_48%,#ededed_100%)]
        bg-fixed
        text-neutral-900
        antialiased
    "
>

<header
    class="
        sticky top-4 z-50
        mx-auto mt-4
        w-[calc(100%-2rem)] max-w-7xl
        rounded-3xl
        border border-white/70
        bg-white/40
        px-5 py-4
        shadow-xl
        backdrop-blur-2xl
    "
>
    <div class="flex items-center justify-between gap-5">

        <a
            href="/public/index.php?route=/"
            class="text-2xl font-black tracking-tighter text-black no-underline"
        >
            CafThé
        </a>

        <div id="cart-button-app"></div>

        <nav class="hidden items-center gap-2 md:flex">
            <a href="/public/index.php?route=/"
               class="rounded-full px-4 py-2 font-semibold hover:bg-black hover:text-white">
                Accueil
            </a>

            <a href="/public/index.php?route=/products" class="rounded-full px-4 py-2 font-semibold hover:bg-black hover:text-white">
                Produits
            </a>

            <a href="/public/index.php?route=/blog" class="rounded-full px-4 py-2 font-semibold hover:bg-black hover:text-white">
                Blog
            </a>

            <a href="/public/index.php?route=/about" class="rounded-full px-4 py-2 font-semibold hover:bg-black hover:text-white">
                À propos
            </a>

            <a href="/public/index.php?route=/contact" class="rounded-full px-4 py-2 font-semibold hover:bg-black hover:text-white">
                Contact
            </a>
            <?php if ($currentUser): ?>
                <!-- Connected user menu. -->
                <details class="group relative">
                    <summary
                        class="
                            flex cursor-pointer list-none
                            items-center gap-2
                            rounded-full
                            bg-black
                            px-4 py-2
                            font-semibold
                            text-white
                            transition
                            hover:bg-neutral-800
                        "
                    >
                        <?= htmlspecialchars(
                            $userLabel,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        <span
                            class="
                                text-xs
                                transition
                                group-open:rotate-180
                            "
                        >
                            ▼
                        </span>
                    </summary>
                    <div
                        class="
                            absolute right-0 top-full z-50
                            mt-3 w-56
                            overflow-hidden
                            rounded-2xl
                            border border-white/70
                            bg-white/80
                            p-2
                            shadow-2xl
                            backdrop-blur-2xl
                        "
                    >
                        <!-- User info. -->
                        <div class="border-b border-black/10 px-3 py-3">
                            <p class="font-bold">
                                <?= htmlspecialchars(
                                    $userLabel,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>
                            <p class="mt-1 text-xs text-neutral-500">
                                <?= htmlspecialchars(
                                    $currentUser['email'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>
                        </div>
                        <!-- Dashboard. -->
                        <a
                            href="/public/index.php?route=/dashboard"
                            class="
                                mt-2 block
                                rounded-xl
                                px-3 py-2
                                font-semibold
                                transition
                                hover:bg-black
                                hover:text-white
                            "
                        >
                            Dashboard
                        </a>
                        <!-- Logout. -->
                        <a
                            href="/public/index.php?route=/logout"
                            class="
                                block
                                rounded-xl
                                px-3 py-2
                                font-semibold
                                text-red-600
                                transition
                                hover:bg-red-50
                            "
                        >
                            Déconnexion
                        </a>
                    </div>
                </details>

            <?php else: ?>
                <!-- Guest login. -->
                <a
                    href="/public/index.php?route=/login"
                    data-login-trigger
                    class="
                        rounded-full
                        bg-black
                        px-4 py-2
                        font-semibold
                        text-white
                        transition
                        hover:bg-neutral-800
                    "
                >
                    Connexion
                </a>
            <?php endif; ?>
        </nav>

        <button
            type="button"
            class="
                flex h-11 w-11 items-center justify-center
                rounded-full bg-black text-white
                md:hidden
            "
            aria-label="Ouvrir le menu"
        >
            <span >☰</span>
            <span >×</span>
        </button>

    </div>

    <nav
        class="
            mt-4 flex flex-col gap-2
            rounded-2xl
            border border-white/60
            bg-white/60
            p-3
            backdrop-blur-xl
            md:hidden
        "
    >
        <a href="/" class="rounded-xl px-4 py-3 font-semibold hover:bg-black hover:text-white">
            Accueil
        </a>

        <a href="/public/index.php?route=/products" class="rounded-xl px-4 py-3 font-semibold hover:bg-black hover:text-white">
            Produits
        </a>

        <a href="/public/index.php?route=/blog" class="rounded-xl px-4 py-3 font-semibold hover:bg-black hover:text-white">
            Blog
        </a>

        <a href="/public/index.php?route=/about" class="rounded-xl px-4 py-3 font-semibold hover:bg-black hover:text-white">
            À propos
        </a>

        <a href="#contact" class="rounded-xl px-4 py-3 font-semibold hover:bg-black hover:text-white">
            Contact
        </a>
        <?php if ($currentUser): ?>
            <!-- Connected mobile account. -->
            <div
                class="
                    rounded-xl
                    bg-black/5
                    px-4 py-3
                "
            >
                <p class="font-bold">
                    <?= htmlspecialchars(
                        $userLabel,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
                <p class="mt-1 text-xs text-neutral-500">
                    <?= htmlspecialchars(
                        $currentUser['email'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            </div>
            <a
                href="/public/index.php?route=/dashboard"
                class="
                    rounded-xl
                    px-4 py-3
                    font-semibold
                    hover:bg-black
                    hover:text-white
                "
            >
                Dashboard
            </a>
            <a
                href="/public/index.php?route=/logout"
                class="
                    rounded-xl
                    px-4 py-3
                    font-semibold
                    text-red-600
                    hover:bg-red-50
                "
            >
                Déconnexion
            </a>
        <?php else: ?>
            <!-- Guest mobile login. -->
            <a
                href="/public/index.php?route=/login"
                data-login-trigger
                class="
                    rounded-xl
                    bg-black
                    px-4 py-3
                    font-semibold
                    text-white
                "
            >
                Connexion
            </a>
        <?php endif; ?>
    </nav>
</header>

<main class="mx-auto w-[calc(100%-2rem)] max-w-7xl">