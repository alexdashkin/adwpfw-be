<div class="adwpfw-tab">

    <?php if ($form): ?>

        <form class="adwpfw-form" data-action="<?= $action ?>">

            <div class="adwpfw-fields">
                <?php
                foreach ($fields as $field) {
                    include __DIR__ . '/admin-page-tab-fields.php';
                }
                ?>
            </div>

            <div class="adwpfw-actions-bar">
                <button type="submit" class="button button-primary adwpfw-save">Save Changes</button>
            </div>

        </form>

    <?php else: ?>

        <div class="adwpfw-fields">
            <?php
            foreach ($fields as $field) {
                include __DIR__ . '/admin-page-tab-fields.php';
                }
            ?>
        </div>

    <?php endif; ?>

</div>
