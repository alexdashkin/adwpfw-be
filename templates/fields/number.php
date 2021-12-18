<input type="number" class="<?= $controlClasses ?>" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>" <?= $required ?>
    <?= isset($min) ? 'min="' . (int)$min . '"' : '' ?>
    <?= isset($max) ? 'max="' . (int)$max . '"' : '' ?>
    <?= isset($step) ? 'step="' . (int)$step . '"' : '' ?>>
