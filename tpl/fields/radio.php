<div class="adwpfw-radio">

    <?php foreach ($options as $option): ?>

        <label>

            <input type="radio" name="<?= $name ?>" value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'checked' : '' ?>>
            <?= $option['label'] ?>

        </label>

        <br>

    <?php endforeach; ?>

</div>
