<select name="<?= $name ?>>">

    <option value="0">All <?= ucfirst($label) ?></option>

    <?php foreach ($options as $option): ?>
        <option value="<?= $option['value'] ?>" <?= $option['selected'] ?>><?= $option['label'] ?></option>
    <?php endforeach; ?>

</select>
