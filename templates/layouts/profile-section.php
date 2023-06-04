<h2><?= $title ?></h2>

<table class="form-table adwpfw-profile-section">
    <?php foreach ($fields as $field): ?>
		<tr>
			<th>
                <?php if ($field['label']): ?>
					<label for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
                <?php endif; ?>
			</th>
			<td>
                <?= $field['content'] ?>
			</td>
		</tr>
    <?php endforeach ?>
</table>