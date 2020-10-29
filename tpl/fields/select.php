<input type="hidden" name="<?= $name ?>" value="">

<select class="<?= $classes ?>" id="<?= $id ?>" name="<?= $name ?>" <?= $multiple ?> <?= $required ?>>

    <?php foreach ($options as $option): ?>
        <option value="<?= $option['value'] ?>" <?= $option['selected'] ?>><?= $option['label'] ?></option>
    <?php endforeach; ?>

</select>
