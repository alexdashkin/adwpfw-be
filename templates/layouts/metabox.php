<div class="adwpfw-metabox adwpfw-context-<?= $context ?> adwpfw">

	<div class="adwpfw-fields">
        
        <?php foreach ($fields as $field): ?>

			<div class="adwpfw-field">
                
                <?php if ($field['label']): ?>

					<div class="adwpfw-label">
						<label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
                        <?php if ($field['required']) : ?>
							<span class="adwpfw-required">*</span>
                        <?php endif ?>
					</div>
                
                <?php endif; ?>

				<div class="adwpfw-control">
                    <?= $field['content'] ?>
				</div>
                
                <?php if ($field['description']): ?>

					<div class="adwpfw-description">
                        <?= $field['description'] ?>
					</div>
                
                <?php endif; ?>

			</div>
        
        <?php endforeach; ?>

	</div>

</div>
