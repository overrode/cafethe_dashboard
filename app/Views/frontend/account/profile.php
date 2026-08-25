<?php

declare(strict_types=1);

/** @var array $client */

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
            Mes informations
        </h1>

        <p class="mt-2 text-neutral-600">
            Gérez vos informations personnelles.
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
        Informations mises à jour avec succès.
    </div>
    <?php endif; ?>


    <?php if (isset($_GET['email_exists'])): ?>
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
            Cette adresse email est déjà utilisée.
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
            Veuillez vérifier les informations saisies.
        </div>
    <?php endif; ?>

    <!-- Profile form. -->
    <form
        action="/public/index.php?route=/account/profile/update"
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
        <div class="grid gap-5 md:grid-cols-2">

            <div>
                <label class="mb-1 block text-sm font-bold">
                    Nom
                </label>

                <input
                    type="text"
                    name="name"
                    required
                    value="<?= htmlspecialchars(
                        $client['name'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
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
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    value="<?= htmlspecialchars(
                        $client['email'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
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
                    Téléphone
                </label>

                <input
                    type="text"
                    name="phone"
                    value="<?= htmlspecialchars(
                        $client['phone'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
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


            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-bold">
                    Adresse
                </label>

                <textarea
                    name="address"
                    rows="4"
                    class="
                        w-full rounded-2xl
                        border border-black/10
                        bg-white/70
                        px-4 py-3
                        outline-none
                        focus:border-black/40
                    "
                ><?= htmlspecialchars(
                    $client['address'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>
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
                Enregistrer
            </button>
        </div>
    </form>

</section>