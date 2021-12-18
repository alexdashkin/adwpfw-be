<div class="adwpfw-field <?= $field['fieldClasses'] ?>">

    <?php if ($field['label']): ?>

        <div class="adwpfw-label">

            <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>

            <?php if ($field['description']): ?>
                <div class="adwpfw-help-tip" aria-label="<?= $field['description'] ?>" role="tooltip" data-microtip-position="top" data-microtip-size="large">
                    <div class="dashicons dashicons-editor-help"></div>
                </div>
            <?php endif; ?>

        </div>

    <?php endif; ?>

    <div class="adwpfw-control">
        <?= $field['content'] ?>
    </div>

</div>
