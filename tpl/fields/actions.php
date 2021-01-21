<div id="<?= $id ?>" class="d-flex <?= $controlClasses ?>">

    <select>

        <?php foreach ($options as $optionValue => $label): ?>

            <option value="<?= $optionValue ?>" <?= $value == $optionValue ? 'selected' : '' ?>>
                <?= $label ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button class="button">Do</button>

</div>
