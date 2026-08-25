<?php require FRONTEND_HEADER_PATH; ?>

<?php if (isset($_GET['account_deactivated'])): ?>
    <div
        class="
            mb-6
            rounded-2xl
            bg-green-100
            px-4 py-3
            font-semibold
            text-green-800
        "
    >
        Votre compte a été désactivé avec succès.
    </div>
<?php endif; ?>

<?php require __DIR__ . '/hero.php'; ?>

<?php require __DIR__ . '/popular.php'; ?>

<?php require FRONTEND_FOOTER_PATH; ?>