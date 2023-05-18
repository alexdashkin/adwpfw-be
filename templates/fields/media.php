<div class="adwpfw-media-field <?= $controlClasses ?> <?= $value ? 'adwpfw-field-has-media' : '' ?>" id="<?= $name ?>">
	<input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
	<div class="adwpfw-media-field-image">
		<button type="button" class="adwpfw-media-field-change">
            <?php if ($value) : ?>
                <?= wp_get_attachment_image($value, 'full', true) ?>
            <?php endif ?>
		</button>
		<button type="button" class="adwpfw-media-field-add">
            <?= $placeholder ?? 'Set image' ?>
		</button>
	</div>
	<input type="text" class="adwpfw-media-field-url <?= $showUrl ? 'show' : '' ?>" value="<?= wp_get_attachment_url($value) ?>" readonly />
	<div class="adwpfw-media-field-helpers">
		<button type="button" class="adwpfw-media-replace components-button is-secondary">Replace file</button>
		<button type="button" class="adwpfw-media-remove components-button is-link is-destructive">Remove file</button>
	</div>
</div>
