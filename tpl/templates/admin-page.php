<div class="wrap adwpfw-admin-page adwpfw <?= $prefix ?>">

    <header><h1><?= $title ?></h1></header>

    <nav class="adwpfw-tabs-header nav-tab-wrapper">

        <?php foreach ($tabs as $tab): ?>
            <span class="nav-tab"><?= $tab['title'] ?></span>
        <?php endforeach; ?>

    </nav>

    <?php foreach ($tabs as $tab): ?>
        <?= $tab['content'] ?>
    <?php endforeach; ?>

</div>