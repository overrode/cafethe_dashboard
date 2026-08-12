<?php require FRONTEND_HEADER_PATH; ?>

<?php if (!empty($errors)): ?>
    <div class="mb-8 rounded-2xl border border-black/10 bg-white/50 p-5 backdrop-blur-xl">
        <?php foreach ($errors as $error): ?>
            <p class="text-sm font-semibold text-red-700">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section
    class="
        mt-20
        rounded-[38px]
        border border-white/70
        bg-white/40
        p-8
        shadow-[0_25px_70px_rgba(0,0,0,0.12)]
        backdrop-blur-3xl
        md:p-14
    "
>
    <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-neutral-500">
        Parlons ensemble
    </p>

    <h1 class="text-4xl font-black tracking-[-0.06em] text-black md:text-6xl">
        Contact
    </h1>

    <p class="mt-6 max-w-2xl text-lg leading-8 text-neutral-600">
        Une question sur nos cafés, nos thés ou votre commande ?
        Envoyez-nous un message.
    </p>

    <?php if (!empty($contact_success)): ?>
    <div class="mb-8 rounded-2xl border border-white/70 bg-white/50 p-5 backdrop-blur-xl">
        <p class="font-semibold text-green-700">
            <?= htmlspecialchars($contact_success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <?php endif; ?>
    <?php if (!empty($contact_error)): ?>
    <div class="mb-8 rounded-2xl border border-white/70 bg-white/50 p-5 backdrop-blur-xl">
        <p class="font-semibold text-red-700">
            <?= htmlspecialchars($contact_error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <?php endif; ?>

    <form
        method="POST"
        action="/public/index.php?route=/contact/send"
        class="mt-10 max-w-2xl space-y-6"
    >

        <div>
            <label for="contact_name" class="mb-2 block font-semibold">
                Nom
            </label>

            <input
                type="text"
                id="contact_name"
                name="contact_name"
                required
                class="
                    w-full rounded-2xl
                    border border-white/70
                    bg-white/50
                    px-4 py-3
                    outline-none
                    backdrop-blur-xl
                    focus:ring-2 focus:ring-black/20
                "
                value="<?= htmlspecialchars(
                    $contactName ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="contact_email" class="mb-2 block font-semibold">
                E-mail
            </label>

            <input
                type="email"
                id="contact_email"
                name="contact_email"
                class="
                    w-full rounded-2xl
                    border border-white/70
                    bg-white/50
                    px-4 py-3
                    outline-none
                    backdrop-blur-xl
                    focus:ring-2 focus:ring-black/20
                "
                value="<?= htmlspecialchars(
                    $contactEmail ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="contact_message" class="mb-2 block font-semibold">
                Message
            </label>

            <textarea
                id="contact_message"
                name="contact_message"
                rows="6"
                class="
                    w-full resize-none rounded-2xl
                    border border-white/70
                    bg-white/50
                    px-4 py-3
                    outline-none
                    backdrop-blur-xl
                    focus:ring-2 focus:ring-black/20
                "
            ><?= htmlspecialchars($contactMessage ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <button
            type="submit"
            class="
                rounded-full
                bg-black
                px-6 py-3
                font-bold text-white
                transition
                hover:-translate-y-0.5
            "
        >
            Envoyer le message
        </button>

    </form>
</section>

<?php require FRONTEND_FOOTER_PATH; ?>