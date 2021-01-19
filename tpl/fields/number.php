<input type="number" class="<?= $controlClasses ?>" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>" <?= $required ?>
    <?= !empty($min) ? 'min="' . $min . '"' : '' ?>
    <?= !empty($max) ? 'max="' . $max . '"' : '' ?>
    <?= !empty($step) ? 'step="' . $step . '"' : '' ?>>
