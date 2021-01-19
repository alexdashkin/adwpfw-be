<div class="adwpfw-fields">

    <?php foreach ($fields as $field): ?>

        <div class="adwpfw-field <?= $field['fieldClasses'] ?>">

            <?php if ($field['label']): ?>

                <div class="adwpfw-label">

                    <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>

                    <?php if ($field['desc']): ?>
                        <div class="adwpfw-help-tip" aria-label="<?= $field['desc'] ?>" role="tooltip" data-microtip-position="top">
                            <div class="dashicons dashicons-editor-help"></div>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <div class="adwpfw-control">
                <?= $field['content'] ?>
            </div>

        </div>

    <?php endforeach; ?>

</div>
