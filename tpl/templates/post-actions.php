<select name="<?= $name ?>>">

    <option value="">All <?= ucfirst($label) ?></option>

    <?php foreach ($options as $option): ?>
        <option value="<?= $option['value'] ?>" <?= $option['selected'] ?>><?= $option['label'] ?></option>
    <?php endforeach; ?>

</select>
