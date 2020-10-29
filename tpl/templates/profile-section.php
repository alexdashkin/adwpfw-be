<div class="<?= $prefix ?> adwpfw adwpfw-profile-section">

    <?php foreach ($fields as $field): ?>

        <div class="adwpfw-field">

            <?php if ($field['label']): ?>

                <div class="adwpfw-label">
                    <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>

                    <?php if ($field['desc']): ?>
                        <div class="adwpfw-help-tip adwpfw-tooltip dashicons dashicons-editor-help">
                            <div class="adwpfw-tooltip-wrapper">
                                <span class="adwpfw-tooltip-text"><?= $field['desc'] ?></span>
                            </div>
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