<div class="adwpfw-tab">

    <?php if($form): ?>

        <form class="adwpfw-form" data-slug="<?= $slug ?>">

            <?= $fields ?>

            <div class="adwpfw-actions-bar">

                <button type="submit" class="button button-primary adwpfw-save">Save changes</button>

            </div>

        </form>

    <?php else: ?>

        <?= $fields ?>

    <?php endif; ?>

</div>