<?php

declare(strict_types=1);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Accès refusé - CafThé</title>

    <link
        rel="stylesheet"
        href="/public/assets/css/app.css"
    >
</head>

<body class="bg-neutral-100 text-black">

<main
    class="
        mx-auto
        flex min-h-screen
        max-w-4xl
        items-center
        px-6 py-16
    "
>
    <div
        class="
            w-full
            rounded-3xl
            border border-black/10
            bg-white
            p-8
            shadow-lg
        "
    >
        <p
            class="
                text-sm
                font-bold
                uppercase
                tracking-widest
                text-neutral-400
            "
        >
            Erreur 403
        </p>

        <h1 class="mt-3 text-4xl font-black">
            Accès refusé.
        </h1>

        <p class="mt-4 text-neutral-600">
            Vous n'avez pas l'autorisation d'accéder à cette page.
        </p>

        <a
            href="/public/index.php?route=/dashboard"
            class="
                mt-8
                inline-block
                rounded-full
                bg-black
                px-6 py-3
                font-bold
                text-white
            "
        >
            Retour au dashboard
        </a>
    </div>
</main>

</body>
</html>