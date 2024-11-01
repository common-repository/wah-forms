<?php
/**
 * Admin AJAX functions
 *
 * @package WAHForms
 */

add_action( 'wp_ajax_wahforms_get_item_options', 'wahforms_get_item_options' );
add_action( 'wp_ajax_wahforms_load_leads_by_form_id', 'wahforms_load_leads_by_form_id' );
add_action( 'wp_ajax_wahforms_​add_radio_option', 'wahforms_​add_radio_option' );
add_action( 'wp_ajax_wahforms_add_selectbox_option', 'wahforms_add_selectbox_option' );

/**
 * Get draggable item options
 */
function wahforms_get_item_options() {
	$field_type  = isset( $_POST['field_type'] ) ? sanitize_text_field( wp_unslash( $_POST['field_type'] ) ) : ''; //phpcs:ignore
	$field_index = isset( $_POST['field_index'] ) ? sanitize_text_field( wp_unslash( $_POST['field_index'] ) ) : ''; // phpcs:ignore
	$html        = '';

	ob_start();

	$hidden_index = 'wahform_fields[fields][' . $field_index . '][hidden_index]';

	if ( 'text' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$placeholder = 'wahform_fields[fields][' . $field_index . '][placeholder]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$maxlength   = 'wahform_fields[fields][' . $field_index . '][maxlength]';
		$minlength   = 'wahform_fields[fields][' . $field_index . '][minlength]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">Label</div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">Placeholder</div>
					<input type="text" name="<?php echo esc_html( $placeholder ); ?>" >
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name">Max length</div>
					<input type="number" name="<?php echo esc_html( $maxlength ); ?>">
				</label>
				<label>
					<div class="label-name">Min length</div>
					<input type="number" name="<?php echo esc_html( $minlength ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name">Required</div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'number' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$placeholder = 'wahform_fields[fields][' . $field_index . '][placeholder]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$max         = 'wahform_fields[fields][' . $field_index . '][max]';
		$min         = 'wahform_fields[fields][' . $field_index . '][min]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $placeholder ); ?>" >
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'Max', 'wah-forms' ); ?></div>
					<input type="number" name="<?php echo esc_html( $max ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Min', 'wah-forms' ); ?></div>
					<input type="number" name="<?php echo esc_html( $min ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>"><?php esc_html_e( 'Delete', 'wah-forms' ); ?></button>
			</div>
		</div>
		<?php
	} elseif ( 'tel' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$placeholder = 'wahform_fields[fields][' . $field_index . '][placeholder]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$maxlength   = 'wahform_fields[fields][' . $field_index . '][maxlength]';
		$minlength   = 'wahform_fields[fields][' . $field_index . '][minlength]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $placeholder ); ?>" >
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name">Max length</div>
					<input type="number" name="<?php echo esc_html( $maxlength ); ?>">
				</label>
				<label>
					<div class="label-name">Min length</div>
					<input type="number" name="<?php echo esc_html( $minlength ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name">Required</div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'textarea' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$placeholder = 'wahform_fields[fields][' . $field_index . '][placeholder]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$maxlength   = 'wahform_fields[fields][' . $field_index . '][maxlength]';
		$minlength   = 'wahform_fields[fields][' . $field_index . '][minlength]';
		$rows        = 'wahform_fields[fields][' . $field_index . '][rows]';
		$cols        = 'wahform_fields[fields][' . $field_index . '][cols]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">Label</div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">Placeholder</div>
					<input type="text" name="<?php echo esc_html( $placeholder ); ?>" >
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>

			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name">Max length</div>
					<input type="number" name="<?php echo esc_html( $maxlength ); ?>">
				</label>
				<label>
					<div class="label-name">Min length</div>
					<input type="number" name="<?php echo esc_html( $minlength ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name">Rows</div>
					<input type="number" name="<?php echo esc_html( $rows ); ?>">
				</label>
				<label>
					<div class="label-name">Cols</div>
					<input type="number" name="<?php echo esc_html( $cols ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name">Required</div>
				</label>
			</div>
			<div class="item-group delete-this-item"><button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</div>
		</div>
		<?php
	} elseif ( 'selectbox' === $field_type ) {
		$hidden_type       = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required          = 'wahform_fields[fields][' . $field_index . '][required]';
		$input_name        = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id          = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class       = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label             = 'wahform_fields[fields][' . $field_index . '][label]';
		$selectbox_options = 'wahform_fields[fields][' . $field_index . '][selectbox_options][]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<ul class="item-group is-repeater is-radio-options">
				<li class="draggable-item ui-draggable ui-draggable-handle">
					<label>
						<div class="label-name">
							<span class="dashicons dashicons-menu"></span>
							<?php esc_html_e( 'Option', 'wah-forms' ); ?>
						</div>
						<input type="text"
							name="<?php echo esc_html( 'wahform_fields[fields][' . $field_index . '][options][0][key]' ); ?>" placeholder="key">
						<input type="text"
							name="<?php echo esc_html( 'wahform_fields[fields][' . $field_index . '][options][0][value]' ); ?>" placeholder="value">
						<button type="button" class="add-selectbox-option"
							data-field-index="<?php echo esc_html( $field_index ); ?>"
							data-option-key="0">
							<span class="dashicons dashicons-plus"></span>
						</button>
					</label>
				</li>
			</ul>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">
					<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
				</button>
			</div>
		</div>
		<?php
	} elseif ( 'hidden' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$input_value = 'wahform_fields[fields][' . $field_index . '][input_value]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Value', 'wah-forms' ); ?>
					</div>
					<input type="text" name="<?php echo esc_html( $input_value ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'checkbox' === $field_type ) {
		$hidden_type        = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required           = 'wahform_fields[fields][' . $field_index . '][required]';
		$checked_by_default = 'wahform_fields[fields][' . $field_index . '][checked_by_default]';
		$input_name         = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id           = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class        = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label              = 'wahform_fields[fields][' . $field_index . '][label]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Label', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $label ); ?>" required>
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2 is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
				<label>
					<input type="checkbox" name="<?php echo esc_html( $checked_by_default ); ?>">
					<div class="label-name"><?php esc_html_e( 'Checked by default', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'radio' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$legend      = 'wahform_fields[fields][' . $field_index . '][legend]';
		$options     = 'wahform_fields[fields][' . $field_index . '][options][]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Label', 'wah-forms' ); ?>
					</div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Legend', 'wah-forms' ); ?>
					</div>
					<input type="text" name="<?php echo esc_html( $legend ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<ul class="item-group is-repeater is-radio-options">
				<li class="draggable-item ui-draggable ui-draggable-handle">
					<label>
						<div class="label-name">
							<span class="dashicons dashicons-menu"></span>
							<?php esc_html_e( 'Option', 'wah-forms' ); ?>
						</div>
						<input type="text" name="<?php echo esc_html( $options ); ?>">
						<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $field_index ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</button>
					</label>
				</li>
			</ul>
			<div class="item-group is-checkbox">
				<hr>
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'url' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$placeholder = 'wahform_fields[fields][' . $field_index . '][placeholder]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$maxlength   = 'wahform_fields[fields][' . $field_index . '][maxlength]';
		$minlength   = 'wahform_fields[fields][' . $field_index . '][minlength]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">Label</div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">Placeholder</div>
					<input type="text" name="<?php echo esc_html( $placeholder ); ?>" >
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name">Max length</div>
					<input type="number" name="<?php echo esc_html( $maxlength ); ?>">
				</label>
				<label>
					<div class="label-name">Min length</div>
					<input type="number" name="<?php echo esc_html( $minlength ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name">Required</div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'email' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$placeholder = 'wahform_fields[fields][' . $field_index . '][placeholder]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$maxlength   = 'wahform_fields[fields][' . $field_index . '][maxlength]';
		$minlength   = 'wahform_fields[fields][' . $field_index . '][minlength]';
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name">Label</div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">Placeholder</div>
					<input type="text" name="<?php echo esc_html( $placeholder ); ?>" >
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name">Max length</div>
					<input type="number" name="<?php echo esc_html( $maxlength ); ?>">
				</label>
				<label>
					<div class="label-name">Min length</div>
					<input type="number" name="<?php echo esc_html( $minlength ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name">Required</div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'file' === $field_type ) {
		$hidden_type     = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required        = 'wahform_fields[fields][' . $field_index . '][required]';
		$input_name      = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id        = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class     = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label           = 'wahform_fields[fields][' . $field_index . '][label]';
		$file_size_limit = 'wahform_fields[fields][' . $field_index . '][file_size_limit]';

		$allowed_file_types = wahforms_get_allowed_file_types();
		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<div class="item-group is-file-group">
				<div class="is-file-group-label">
					<div class="label-name">Select allowed file types:</div>
					<div class="allowed-items">
						<?php foreach ( $allowed_file_types as $file_key => $file_name ) : ?>
							<div class="allowed-item">
								<label>
									<input type="checkbox"
									name="wahform_fields[fields][<?php echo esc_html( $field_index ); ?>][allowed_file_types][<?php echo esc_html( $file_key ); ?>]">
									<?php echo esc_html( $file_name ); ?>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'File size limit in MB', 'wah-forms' ); ?>
					</div>
					<input type="number" class="file-size-limit" min="1" max="200" name="<?php echo esc_html( $file_size_limit ); ?>">
				</label>
			</div>
			<div class="item-group is-checkbox">
				<hr>
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	} elseif ( 'checkboxes' === $field_type ) {
		$hidden_type = 'wahform_fields[fields][' . $field_index . '][field_type]';
		$required    = 'wahform_fields[fields][' . $field_index . '][required]';
		$input_name  = 'wahform_fields[fields][' . $field_index . '][input_name]';
		$input_id    = 'wahform_fields[fields][' . $field_index . '][input_id]';
		$input_class = 'wahform_fields[fields][' . $field_index . '][input_class]';
		$label       = 'wahform_fields[fields][' . $field_index . '][label]';
		$legend      = 'wahform_fields[fields][' . $field_index . '][legend]';
		$checkboxes  = 'wahform_fields[fields][' . $field_index . '][options][]';

		?>
		<div class="item-fields">
			<input type="hidden" name="<?php echo esc_html( $hidden_index ); ?>" value="<?php echo esc_html( $field_index ); ?>">
			<input type="hidden" name="<?php echo esc_html( $hidden_type ); ?>" value="<?php echo esc_html( $field_type ); ?>">
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $label ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name"><?php esc_html_e( 'Legend', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $legend ); ?>">
				</label>
			</div>
			<div class="item-group">
				<label>
					<div class="label-name">
						<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
						<span class="red-asterix">*</span>
					</div>
					<input type="text" name="<?php echo esc_html( $input_name ); ?>" required>
				</label>
			</div>
			<div class="item-group column2">
				<label>
					<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_id ); ?>">
				</label>
				<label>
					<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
					<input type="text" name="<?php echo esc_html( $input_class ); ?>">
				</label>
			</div>
			<ul class="item-group is-repeater is-radio-options">
				<li>
					<label>
						<div class="label-name">
							<?php esc_html_e( 'Option', 'wah-forms' ); ?>
						</div>
						<input type="text" name="<?php echo esc_html( $checkboxes ); ?>">
						<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $field_index ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</button>
					</label>
				</li>
			</ul>
			<div class="item-group is-checkbox">
				<hr>
				<label>
					<input type="checkbox" name="<?php echo esc_html( $required ); ?>">
					<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
				</label>
			</div>
			<div class="item-group delete-this-item">
				<button type="button" data-index="<?php echo esc_html( $field_index ); ?>">Delete</button>
			</div>
		</div>
		<?php
	}
	$html = ob_get_clean();

	wp_send_json( $html );
}
/**
 * Load leads_by_form_id description]
 */
function wahforms_load_leads_by_form_id() {
	// Check for nonce security.
	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax-nonce-backend' ) ) {
		die( 'Busted! Security varification failed.' );
	}
	$post_id     = isset( $_POST['form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['form_id'] ) ) : '';
	$paged       = isset( $_POST['paged'] ) ? sanitize_text_field( wp_unslash( $_POST['paged'] ) ) : 1;
	$all_leads   = wahforms_get_leads_by_id( $post_id );
	$leads       = wahforms_get_leads_by_id( $post_id, $paged );
	$date_format = wahforms_get_option( 'wahforms_lead_date_format' ) ? wahforms_get_option( 'wahforms_lead_date_format' ) : 'ymd';
	if ( 'ymd' === $date_format ) {
		$date_format = 'Y-m-d H:i:s';
	} else {
		$date_format = 'd/m/Y H:i:s';
	}
	$pagination_posts_per_page = 10;

	$response = array(
		'html' => '',
	);
	ob_start();
	if ( $leads ) :
		echo ( 1 === (int) $paged ) ? '<div class="wahforms-lead-item"><ol>' : '';
		foreach ( $leads as $lead ) :
			include plugin_dir_path( __FILE__ ) . 'tpl/lead-item.php';
		endforeach;
		echo ( 1 === (int) $paged ) ? '</ol></div>' : '';
	else :
		echo esc_html__( 'There is no leads yet', 'wah-forms' );
	endif;
	$response['html'] = ob_get_clean();

	if ( (int) count( $all_leads ) > $pagination_posts_per_page * ( (int) $paged ) ) {
		$response['load_more'] = true;
	} else {
		$response['load_more'] = false;
	}

	$response['paged'] = (int) $paged + 1;

	wp_send_json( $response );
}
/**
 * Add radio option
 */
function wahforms_​add_radio_option() {
	$html = '';
	// Check for nonce security.
	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax-nonce-backend' ) ) {
		die( 'Busted! Security varification failed.' );
	}
	$field_index = isset( $_POST['field_index'] ) ? sanitize_text_field( wp_unslash( $_POST['field_index'] ) ) : '';
	if ( $field_index ) {
		ob_start();
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle">
			<label>
				<div class="label-name">
					<span class="dashicons dashicons-menu"></span>
					<?php esc_html_e( 'Option', 'wah-forms' ); ?>
				</div>
				<input type="text" name="wahform_fields[fields][<?php echo esc_html( $field_index ); ?>][options][]">
				<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $field_index ); ?>">
					<span class="dashicons dashicons-plus"></span>
				</button>
			</label>
		</li>
		<?php
		$html = ob_get_clean();
	}
	wp_send_json( $html );
}
/**
 * Add selectbox option
 */
function wahforms_add_selectbox_option() {
	$html = '';
	// Check for nonce security.
	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax-nonce-backend' ) ) {
		die( 'Busted! Security varification failed.' );
	}
	$field_index    = isset( $_POST['field_index'] ) ? sanitize_text_field( wp_unslash( $_POST['field_index'] ) ) : '';
	$option_key     = isset( $_POST['option_key'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['option_key'] ) ) : '';
	$new_option_key = $option_key + 1;

	if ( $field_index ) {
		ob_start();
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle">
			<label class="selectbox_option">
				<div class="label-name">
					<span class="dashicons dashicons-menu"></span>
					<?php esc_html_e( 'Option', 'wah-forms' ); ?>
				</div>
				<input type="text"
					name="wahform_fields[fields][<?php echo esc_html( $field_index ); ?>][options][<?php echo esc_html( $new_option_key ); ?>][key]"
					placeholder="key">
				<input type="text"
					name="wahform_fields[fields][<?php echo esc_html( $field_index ); ?>][options][<?php echo esc_html( $new_option_key ); ?>][value]" placeholder="value">
				<button type="button" class="add-selectbox-option"
					data-field-index="<?php echo esc_html( $field_index ); ?>"
					data-option-key="<?php echo esc_html( $new_option_key ); ?>">
					<span class="dashicons dashicons-plus"></span>
				</button>
				<button type="button" class="remove-selectbox-option"
					data-field-index="<?php echo esc_html( $field_index ); ?>"
					data-option-key="<?php echo esc_html( $new_option_key ); ?>">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</label>
		</li>
		<?php
		$html = ob_get_clean();
	}
	wp_send_json( $html );
}
