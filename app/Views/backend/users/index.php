<?php

$title = 'Utilisateurs - CafThé';

/** @var array $users */

require BACKEND_HEADER_PATH;

$usersJson = htmlspecialchars(
    json_encode(
        $users,
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ),
    ENT_QUOTES,
    'UTF-8'
);
?>

<section class="py-8">
    <div class="mb-8">
        <p
            class="
                mb-2 text-xs font-bold
                uppercase tracking-[0.16em]
                text-neutral-500
            "
        >
            Administration
        </p>

        <h1 class="text-4xl font-black tracking-[-0.05em]">
            Utilisateurs
        </h1>
    </div>

    <div
        id="dashboard-users-app"
        data-users="<?= $usersJson ?>"
        data-current-user-id="<?= (int) $_SESSION['user']['id'] ?>"
    ></div>
</section>

<?php require BACKEND_FOOTER_PATH; ?>