<?php

declare(strict_types=1);

/** @var array $client */

require FRONTEND_HEADER_PATH;

?>

<section class="mx-auto max-w-6xl px-4 py-10">
    <div
        class="
            rounded-[32px]
            border border-white/60
            bg-white/50
            p-8
            shadow-[0_24px_70px_rgba(0,0,0,0.12)]
            backdrop-blur-2xl
        "
    >
        <p class="text-sm font-bold uppercase tracking-[0.18em] text-neutral-500">
            Mon compte
        </p>

        <h1 class="mt-2 text-4xl font-black tracking-[-0.04em]">
            Bonjour <?= htmlspecialchars($client['name']) ?>
        </h1>

        <p class="mt-3 text-neutral-600">
            Gérez vos informations personnelles et vos commandes.
        </p>

        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">

            <a
                href="/public/index.php?route=/account/profile"
                class="
                    rounded-3xl
                    border border-black/10
                    bg-white/60
                    p-6
                    transition
                    hover:-translate-y-1
                    hover:bg-white
                "
            >
                <h2 class="text-xl font-black">
                    Mes informations
                </h2>

                <p class="mt-2 text-sm text-neutral-500">
                    Nom, email, téléphone et adresse.
                </p>
            </a>

            <a
                href="/public/index.php?route=/account/orders"
                class="
                    rounded-3xl
                    border border-black/10
                    bg-white/60
                    p-6
                    transition
                    hover:-translate-y-1
                    hover:bg-white
                "
            >
                <h2 class="text-xl font-black">
                    Mes commandes
                </h2>

                <p class="mt-2 text-sm text-neutral-500">
                    Consultez votre historique de commandes.
                </p>
            </a>

            <a
                href="/public/index.php?route=/account/security"
                class="
                    rounded-3xl
                    border border-black/10
                    bg-white/60
                    p-6
                    transition
                    hover:-translate-y-1
                    hover:bg-white
                "
            >
                <h2 class="text-xl font-black">
                    Sécurité
                </h2>

                <p class="mt-2 text-sm text-neutral-500">
                    Modifiez votre mot de passe.
                </p>
            </a>

        </div>

        <div class="mt-8">
            <a
                href="/public/index.php?route=/account/logout"
                class="
                    inline-flex
                    rounded-full
                    border border-black/20
                    px-5 py-3
                    font-bold
                    transition
                    hover:bg-black
                    hover:text-white
                "
            >
                Se déconnecter
            </a>
        </div>
    </div>
</section>