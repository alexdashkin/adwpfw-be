<div id="<?= $id ?>" class="d-flex <?= $controlClasses ?>">

    <select>

        <?php foreach ($options as $optionValue => $label): ?>

            <option value="<?= $prefix . '_' . $optionValue ?>">
                <?= $label ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button type="button" class="button">Do</button>

</div>
