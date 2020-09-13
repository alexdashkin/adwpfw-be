<div class="<?= $prefix ?> adwpfw adwpfw-profile-section">

    <?php if ($heading): ?>
        <h2><?= $heading ?></h2>
    <?php endif; ?>

    <table class="form-table">

        <?php foreach ($fields as $field): ?>

            <tr>

                <?php if ($field['label']): ?>

                    <th>
                        <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
                    </th>

                <?php endif; ?>

                <td <?= $field['label'] ? 'colspan="2"' : '' ?>>

                    <?= $field['content'] ?>

                    <?php if ($field['desc']): ?>
                        <span class="description"><?= $field['desc'] ?></span>
                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>