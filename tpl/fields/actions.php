<div class="adwpfw-actions-selector">

    <select id="<?= $id ?>" class="<?= $classes ?>">

        <?php foreach ($options as $option): ?>

            <option value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'selected' : '' ?>>
                <?= $option['label'] ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button class="button">Do</button>

</div>
