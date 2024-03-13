<?php

use App\Core\Session;

?>
    <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= htmlspecialchars($heading ?? '')?></h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <?php if (Session::get('error')) : ?>
            <?php errorToast(Session::get('error')); ?>
        <?php endif; ?>
        <?php if (Session::get('info')) : ?>
            <?php infoToast(Session::get('info')); ?>
        <?php endif; ?>
        <?php if (Session::get('success')) : ?>
            <?php successToast(Session::get('success')); ?>
        <?php endif; ?>
        <?php if (Session::get('warning')) : ?>
        <?php warningToast(Session::get('warning')); ?>
<?php endif; ?>