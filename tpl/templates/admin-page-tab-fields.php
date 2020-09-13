<div class="adwpfw-fields">

    <?php foreach ($fields as $field): ?>

        <div class="adwpfw-field">

            <?php if ($field['label']): ?>

                <div class="adwpfw-label">
                    <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
                </div>

                <?php if ($field['desc']): ?>
                    <div class="adwpfw-help-tip adwpfw-tooltip dashicons dashicons-editor-help">
                        <span class="tooltiptext"><?= $field['desc'] ?></span>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <div class="adwpfw-control">
                <?= $field['content'] ?>
            </div>

        </div>

    <?php endforeach; ?>

</div>