<div class="wrap adwpfw-admin-page adwpfw">

    <header><h1><?= $title ?></h1></header>

    <nav class="adwpfw-tabs-header nav-tab-wrapper">

        <?php foreach ($tabs as $tab): ?>
            <a class="nav-tab <?= $tab['current'] ? 'nav-tab-active' : '' ?>" href="<?= $tab['link'] ?>"><?= $tab['title'] ?></a>
        <?php endforeach; ?>

    </nav>

    <?= $content ?>

</div>
