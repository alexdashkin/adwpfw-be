<div class="adwpfw-widget adwpfw <?= $prefix ?>">

    <div class="adwpfw-fields">

        <?php foreach ($fields as $field): ?>

            <div class="adwpfw-field">

                <?php if ($field['label']): ?>

                    <div class="adwpfw-label">
                        <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
                    </div>

                <?php endif; ?>

                <div class="adwpfw-control">
                    <?= $field['content'] ?>
                </div>

                <?php if ($field['desc']): ?>

                    <div class="adwpfw-desc">
                        <?= $field['desc'] ?>
                    </div>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>
