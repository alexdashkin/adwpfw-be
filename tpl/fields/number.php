<input type="number" class="<?= $classes ?>" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>" <?= $required ?>
    <?= !empty($min) ? 'min="' . $min . '"' : '' ?>
    <?= !empty($max) ? 'max="' . $max . '"' : '' ?>
    <?= !empty($step) ? 'step="' . $step . '"' : '' ?>>