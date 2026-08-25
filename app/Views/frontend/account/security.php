<?php

declare(strict_types=1);

require FRONTEND_HEADER_PATH;
?>

<section class="mx-auto max-w-4xl px-4 py-10">

    <!-- Page heading. -->
    <div class="mb-8">
        <a
            href="/public/index.php?route=/account"
            class="text-sm font-bold text-neutral-500 hover:text-black"
        >
            ← Mon compte
        </a>

        <h1 class="mt-3 text-4xl font-black tracking-[-0.04em]">
            Sécurité
        </h1>

        <p class="mt-2 text-neutral-600">
            Modifiez le mot de passe de votre compte.
        </p>
    </div>

    <!-- Alerts. -->
    <?php if (isset($_GET['updated'])): ?>
    <div
        class="
            mb-5
            rounded-2xl
            bg-green-100
            px-4 py-3
            font-semibold
            text-green-800
        "
    >
        Mot de passe modifié avec succès.
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['current_password'])): ?>
        <div
            class="
                mb-5
                rounded-2xl
                bg-red-100
                px-4 py-3
                font-semibold
                text-red-700
            "
        >
            Le mot de passe actuel est incorrect.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['weak_password'])): ?>
        <div
            class="
                mb-5
                rounded-2xl
                bg-red-100
                px-4 py-3
                font-semibold
                text-red-700
            "
        >
            Le nouveau mot de passe doit contenir au moins 8 caractères.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['mismatch'])): ?>
        <div
            class="
                mb-5
                rounded-2xl
                bg-red-100
                px-4 py-3
                font-semibold
                text-red-700
            "
        >
            Les nouveaux mots de passe ne correspondent pas.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div
            class="
                mb-5
                rounded-2xl
                bg-red-100
                px-4 py-3
                font-semibold
                text-red-700
            "
        >
            Veuillez remplir tous les champs.
        </div>
    <?php endif; ?>

    <!-- Password form. -->
    <form
        action="/public/index.php?route=/account/security/password"
        method="POST"
        class="
            rounded-[32px]
            border border-white/70
            bg-white/50
            p-7
            shadow-[0_20px_60px_rgba(0,0,0,0.12)]
            backdrop-blur-2xl
        "
    >
        <div class="space-y-5">

            <div>
                <label class="mb-1 block text-sm font-bold">
                    Mot de passe actuel
                </label>

                <input
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    class="
                        w-full rounded-2xl
                        border border-black/10
                        bg-white/70
                        px-4 py-3
                        outline-none
                        focus:border-black/40
                    "
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-bold">
                    Nouveau mot de passe
                </label>

                <input
                    type="password"
                    name="new_password"
                    required
                    autocomplete="new-password"
                    class="
                        w-full rounded-2xl
                        border border-black/10
                        bg-white/70
                        px-4 py-3
                        outline-none
                        focus:border-black/40
                    "
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-bold">
                    Confirmer le nouveau mot de passe
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="
                        w-full rounded-2xl
                        border border-black/10
                        bg-white/70
                        px-4 py-3
                        outline-none
                        focus:border-black/40
                    "
                >
            </div>

        </div>


        <!-- Form actions. -->
        <div class="mt-7 flex justify-end">
            <button
                type="submit"
                class="
                    rounded-full
                    bg-black
                    px-6 py-3
                    font-bold text-white
                "
            >
                Modifier le mot de passe
            </button>
        </div>
    </form>

    <!-- Account deactivation. -->
    <div
        class="
            mt-8
            rounded-[32px]
            border border-red-200
            bg-red-50/70
            p-7
            backdrop-blur-2xl
        "
    >
        <h2 class="text-2xl font-black text-red-700">
            Désactiver mon compte
        </h2>

        <p class="mt-2 text-sm text-red-700/80">
            Votre compte sera désactivé et vous ne pourrez plus vous connecter.
            Vos commandes resteront conservées.
        </p>

        <?php if (isset($_GET['deactivate_error'])): ?>
            <div
                class="
                    mt-5
                    rounded-2xl
                    bg-red-100
                    px-4 py-3
                    font-semibold
                    text-red-700
                "
            >
                Mot de passe incorrect.
            </div>
        <?php endif; ?>

        <form
            action="/public/index.php?route=/account/deactivate"
            method="POST"
            class="mt-6"
            onsubmit="return confirm('Voulez-vous vraiment désactiver votre compte ?');"
        >
            <label class="mb-1 block text-sm font-bold">
                Confirmez votre mot de passe
            </label>

            <input
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="
                    w-full rounded-2xl
                    border border-red-200
                    bg-white/80
                    px-4 py-3
                    outline-none
                    focus:border-red-400
                "
            >

            <div class="mt-5 flex justify-end">
                <button
                    type="submit"
                    class="
                        rounded-full
                        bg-red-600
                        px-6 py-3
                        font-bold text-white
                        hover:bg-red-700
                    "
                >
                    Désactiver mon compte
                </button>
            </div>
        </form>
    </div>

</section>