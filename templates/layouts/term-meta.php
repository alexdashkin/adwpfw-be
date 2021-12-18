<div class="adwpfw adwpfw-term-meta">

    <?php if ($title): ?>
        <h2><?= $title ?></h2>
    <?php endif; ?>

    <table class="form-table">

        <?php foreach ($fields as $field): ?>

            <tr class="form-field">

                <?php if ($field['label']): ?>

                    <th scope="row">
                        <label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
                    </th>

                <?php endif; ?>

                <td <?= !$field['label'] ? 'colspan="2" style="padding:0"' : '' ?>>

                    <?= $field['content'] ?>

                    <?php if ($field['description']): ?>
                        <p class="description"><?= $field['description'] ?></p>
                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>
