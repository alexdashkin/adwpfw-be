<input type="hidden" name="<?= $name ?>" value="">

<select class="<?= $controlClasses ?>" id="<?= $id ?>" name="<?= $multiple ? $name . '[]' : $name ?>" <?= $multiple ?> <?= $required ?>>

    <?php foreach ($options as $option): ?>
        <option value="<?= $option['value'] ?>" <?= $option['selected'] ?>><?= $option['label'] ?></option>
    <?php endforeach; ?>

</select>
