<?php $items = array_values($value) ?>

<div class="adwpfw-list-field">
    <?php if (is_array($items) && $items) : ?>
        
        <?php foreach ($items as $key => $item) : ?>

			<div class="adwpfw-lf-item">

				<button type="button" class="adwpfw-lf-button" data-action="level-down">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
						<path d="M4 7.2v1.5h16V7.2H4zm8 8.6h8v-1.5h-8v1.5zm-4-4.6l-4 4 4 4 1-1-3-3 3-3-1-1z"></path>
					</svg>
				</button>
				
				<button type="button" class="adwpfw-lf-button" data-action="level-up">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
						<path d="M4 7.2v1.5h16V7.2H4zm8 8.6h8v-1.5h-8v1.5zm-8-3.5l3 3-3 3 1 1 4-4-4-4-1 1z"></path>
					</svg>
				</button>
				
				<input type="hidden" name="<?= $name . '[' . $key . '][level]' ?>" value="<?= $item['level'] ?: 0 ?>" />
				
				<input type="text"
				       class="item-level-<?= $item['level'] ?: 0 ?>"
				       data-level="<?= $item['level'] ?: 0 ?>"
				       data-name="<?= $name ?>"
				       data-key="<?= $key ?>"
				       placeholder="<?= $placeholder ?>"
				       name="<?= $name . '[' . $key . '][value]' ?>"
				       value="<?= $item['value'] ?>"
				/>

				<button type="button" class="adwpfw-lf-button" data-action="<?= isset($items[$key + 1]) ? 'remove' : 'add' ?>-item"></button>

			</div>
        
        <?php endforeach ?>
    
    <?php else : ?>
    
			<div class="adwpfw-lf-item">

				<button type="button" class="adwpfw-lf-button" data-action="level-down">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
						<path d="M4 7.2v1.5h16V7.2H4zm8 8.6h8v-1.5h-8v1.5zm-4-4.6l-4 4 4 4 1-1-3-3 3-3-1-1z"></path>
					</svg>
				</button>

				<button type="button" class="adwpfw-lf-button" data-action="level-up">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
						<path d="M4 7.2v1.5h16V7.2H4zm8 8.6h8v-1.5h-8v1.5zm-8-3.5l3 3-3 3 1 1 4-4-4-4-1 1z"></path>
					</svg>
				</button>

				<input type="hidden" name="<?= $name . '[0][level]' ?>" value="0" />
				<input type="text"
				       class="item-level-0"
				       data-level="0"
				       data-name="<?= $name ?>"
				       data-key="0"
				       placeholder="<?= $placeholder ?>"
				       name="<?= $name . '[0][value]' ?>"
				       value=""
				/>

				<button type="button" class="adwpfw-lf-button" data-action="add-item"></button>

			</div>
    
    <?php endif ?>
</div>