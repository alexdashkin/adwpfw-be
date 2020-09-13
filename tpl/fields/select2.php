<select class="adwpfw-select2 <?= $classes ?>"
        id="<?= $id ?>"
        name="<?= $name ?>"
    <?= $multiple ?> <?= $required ?>
    <?= !empty($ajax_action) ? 'data-ajax-action="' . $ajax_action . '"' : '' ?>
    <?= !empty($placeholder) ? 'data-placeholder="' . $placeholder . '"' : '' ?>
    <?= !empty($min_chars) ? 'data-min-chars="' . $min_chars . '"' : '' ?>
    <?= !empty($min_items_for_search) ? 'data-min-items-for-search="' . $min_items_for_search . '"' : '' ?>>

    <?php foreach ($options as $option): ?>
        <option value="<?= $option['value'] ?>" <?= $option['selected'] ?>><?= $option['label'] ?></option>
    <?php endforeach; ?>

</select>
