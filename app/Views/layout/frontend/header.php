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

            <a
                href="/public/index.php?route=/login"
                class="rounded-full bg-black px-4 py-2 font-semibold text-white"
            >
                Connexion
            </a>
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

        <a
            href="/public/index.php?route=/login"
            class="rounded-xl bg-black px-4 py-3 font-semibold text-white"
        >
            Connexion
        </a>
    </nav>
</header>

<main class="mx-auto w-[calc(100%-2rem)] max-w-7xl">