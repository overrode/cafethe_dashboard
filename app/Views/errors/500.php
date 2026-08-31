<?php

declare(strict_types=1);

/** @var array|null $error */
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Erreur - CafThé</title>

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
        <p class="text-sm font-bold uppercase tracking-widest text-neutral-400">
            Erreur 500
        </p>

        <h1 class="mt-3 text-4xl font-black">
            Une erreur est survenue.
        </h1>

        <p class="mt-4 text-neutral-600">
            Impossible de terminer cette opération.
            Veuillez réessayer.
        </p>


        <?php if (IS_DEVELOPMENT && $error): ?>

            <div
                class="
                    mt-8
                    rounded-2xl
                    bg-neutral-100
                    p-5
                    font-mono
                    text-sm
                "
            >
                <p class="font-bold">
                    <?= htmlspecialchars(
                        $error['message'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p class="mt-3 text-neutral-600">
                    <?= htmlspecialchars(
                        $error['file'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                    :<?= (int) $error['line'] ?>
                </p>

                <?php if (!empty($error['trace'])): ?>
                    <pre
                        class="
                            mt-5
                            overflow-x-auto
                            whitespace-pre-wrap
                            text-xs
                        "
                    ><?= htmlspecialchars(
                        $error['trace'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></pre>
                <?php endif; ?>
            </div>

        <?php endif; ?>


        <a
            href="/public/index.php"
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
            Retour à l'accueil
        </a>
    </div>
</main>

</body>
</html>