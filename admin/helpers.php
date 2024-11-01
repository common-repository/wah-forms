<?php
/**
 * WAHForms helpers admin functions
 *
 * @package WAHForms
 */

add_action( 'add_meta_boxes', 'add_wahforms_metaboxes' );
add_action( 'admin_menu', 'wah_forms_admin_menu' );

add_filter( 'screen_layout_columns', 'wahfroms_screen_layout_columns' );
add_filter( 'get_user_option_screen_layout_wah_forms', 'wahforms_screen_layout_post' );

/**
 * WAHForms add metabox
 */
function add_wahforms_metaboxes() {
	add_meta_box(
		'wahforms_builder',
		'WAHForms builder',
		'wahforms_builder',
		'wah_forms',
		'normal',
		'default'
	);
}

/**
 * WAHForms admin_settings_get_allowed_html
 *
 * @return array allowed html tags
 */
function wahforms_admin_settings_get_allowed_html() {
	$allowed_html = array(
		'a'        => array(
			'href'  => array(),
			'title' => array(),
		),
		'hr'       => array(),
		'em'       => array(),
		'h3'       => array(
			'class' => array(),
			'id'    => array(),
		),
		'ul'       => array(
			'id'         => array(),
			'class'      => array(),
			'data-index' => array(),
		),
		'li'       => array(
			'class'      => array(),
			'data-index' => array(),
		),
		'strong'   => array(),
		'small'    => array(),
		'p'        => array(
			'class' => array(),
		),
		'button'   => array(
			'disabled'         => array(),
			'name'             => array(),
			'class'            => array(),
			'type'             => array(),
			'id'               => array(),
			'title'            => array(),
			'data-field-index' => array(),
			'data-option-key'  => array(),
		),
		'input'    => array(
			'value'    => array(),
			'readonly' => array(),
			'name'     => array(),
			'class'    => array(),
			'type'     => array(),
			'id'       => array(),
			'required' => array(),
			'checked'  => array(),
		),
		'textarea' => array(
			'name'  => array(),
			'rows'  => array(),
			'cols'  => array(),
			'id'    => array(),
			'class' => array(),
		),
		'label'    => array(
			'class' => array(),
		),
		'div'      => array(
			'class'      => array(),
			'id'         => array(),
			'data-field' => array(),
			'style'      => array(),
		),
		'span'     => array(
			'class' => array(),
			'id'    => array(),
		),
	);
	return $allowed_html;
}
add_filter(
	'safe_style_css',
	function( $styles ) {
		$styles[] = 'display';
		return $styles;
	}
);

/**
 * WAHForms admin menu page
 */
function wah_forms_admin_menu() {
	add_submenu_page(
		'options-general.php',
		__( 'WAH Forms', 'wah-forms' ),
		__( 'WAH Forms', 'wah-forms' ),
		'manage_options',
		'wahforms-page', // slug.
		'wah_forms_admin_page_contents',
		'dashicons-schedule',
		3
	);
}
/**
 * WAHForms get_field_types
 *
 * @return array field types
 */
function wahforms_get_field_types() {
	$field_types = array(
		'text',
		'textarea',
		'email',
		'tel',
		'number',
		'selectbox',
		'checkbox',
		'checkboxes',
		'radio',
		'hidden',
		'url',
		'file',
	);
	return $field_types;
}
/**
 * WAHForms render_admin_init_field_item
 *
 * @param  string $field_type               field type.
 * @param  string $key                      field key.
 * @param  array  $item                     item array.
 */
function wahforms_render_admin_init_field_item( $field_type, $key, $item ) {
	ob_start();
	if ( 'text' === $field_type ) {
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$maxlength   = isset( $item['maxlength'] ) ? sanitize_text_field( $item['maxlength'] ) : '';
		$minlength   = isset( $item['minlength'] ) ? sanitize_text_field( $item['minlength'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
				<?php esc_html_e( 'Text', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
								<span class="input-selector"></span>
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
									<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Max length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][maxlength]"
									value="<?php echo esc_html( $maxlength ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Min length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][minlength]"
									value="<?php echo esc_html( $minlength ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'number' === $field_type ) {
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$max         = isset( $item['max'] ) ? sanitize_text_field( $item['max'] ) : '';
		$min         = isset( $item['min'] ) ? sanitize_text_field( $item['min'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
				<?php esc_html_e( 'Number', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Max', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][max]"
									value="<?php echo esc_html( $max ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Min', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][min]"
									value="<?php echo esc_html( $min ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'tel' === $field_type ) {
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$maxlength   = isset( $item['maxlength'] ) ? sanitize_text_field( $item['maxlength'] ) : '';
		$minlength   = isset( $item['minlength'] ) ? sanitize_text_field( $item['minlength'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
				<?php esc_html_e( 'Phone', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Max length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][maxlength]"
									value="<?php echo esc_html( $maxlength ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Min length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][minlength]"
									value="<?php echo esc_html( $minlength ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'textarea' === $field_type ) {
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$maxlength   = isset( $item['maxlength'] ) ? sanitize_text_field( $item['maxlength'] ) : '';
		$minlength   = isset( $item['minlength'] ) ? sanitize_text_field( $item['minlength'] ) : '';
		$rows        = isset( $item['rows'] ) ? sanitize_text_field( $item['rows'] ) : '';
		$cols        = isset( $item['cols'] ) ? sanitize_text_field( $item['cols'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
			<?php esc_html_e( 'Textarea', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]" value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>
						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Max length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][maxlength]"
									value="<?php echo esc_html( $maxlength ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Min length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][minlength]"
									value="<?php echo esc_html( $minlength ); ?>" >
							</label>
						</div>
						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Rows', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][rows]"
									value="<?php echo esc_html( $rows ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Cols', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][cols]"
									value="<?php echo esc_html( $cols ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</li>
		<?php
	} elseif ( 'selectbox' === $field_type ) {
		$label             = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$required          = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$input_name        = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id          = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class       = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$selectbox_options = isset( $item['options'] ) ? $item['options'] : array();
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
			<?php esc_html_e( 'Selectbox', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]" value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<ul class="item-group is-repeater is-radio-options">
						<?php
						if ( $selectbox_options ) :
							$options_counter = 0;
							foreach ( $selectbox_options as $option_key => $option_value ) :
								$options_counter++;
								?>
								<li class="draggable-item ui-draggable ui-draggable-handle">
									<label>
										<div class="label-name">
											<span class="dashicons dashicons-menu"></span>
											<?php esc_html_e( 'Option', 'wah-forms' ); ?>
										</div>
										<input type="text"
											name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][<?php echo esc_html( $option_key ); ?>][key]" value="<?php echo esc_html( $option_value['key'] ); ?>" class="key-input">
										<input type="text"
											name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][<?php echo esc_html( $option_key ); ?>][value]" value="<?php echo esc_html( $option_value['value'] ); ?>">
										<button type="button" class="add-selectbox-option"
											data-field-index="<?php echo esc_html( $key ); ?>"
											data-option-key="<?php echo esc_html( $option_key ); ?>"
											title="<?php esc_html_e( 'Add option', 'wah-froms' ); ?>">
											<span class="dashicons dashicons-plus"></span>
										</button>
										<?php if ( 1 !== $options_counter ) : ?>
											<button type="button" class="remove-selectbox-option"
												data-field-index="<?php echo esc_html( $field_index ); ?>"
												title="<?php esc_html_e( 'Delete option', 'wah-froms' ); ?>">
												<span class="dashicons dashicons-trash"></span></span>
											</button>
										<?php endif; ?>
									</label>
								</li>
								<?php endforeach; ?>
							<?php else : ?>
								<label class="draggable-item ui-draggable ui-draggable-handle">
									<div class="label-name">
										<span class="dashicons dashicons-menu"></span>
										<?php esc_html_e( 'Option', 'wah-forms' ); ?>
									</div>
									<input type="text"
										name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][0][key]" value="" class="key-input">
									<input type="text"
										name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][0][value]" value="">
									<button type="button" class="add-radio-option" data-field-index="0">
										<span class="dashicons dashicons-plus"></span>
									</button>
								</label>
							<?php endif; ?>
						</ul>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>"
								title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
								<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</li>
		<?php
	} elseif ( 'hidden' === $field_type ) {
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_value = isset( $item['input_value'] ) ? sanitize_text_field( $item['input_value'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title">
			<?php esc_html_e( 'Hidden field', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Value', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_value]"
									value="<?php echo esc_html( $input_value ); ?>">
							</label>
						</div>
						<div class="item-group column2">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'ID', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Class', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>">
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'checkbox' === $field_type ) {
		$required           = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$checked_by_default = isset( $item['checked_by_default'] ) ? sanitize_text_field( $item['checked_by_default'] ) : '';
		$input_name         = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id           = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class        = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$label              = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
			<?php esc_html_e( 'Checkbox', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Label', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>">
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>
						<div class="item-group column2">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'ID', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Class', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>">
							</label>
						</div>
						<div class="item-group column2 is-checkbox">
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][checked_by_default]"
							<?php echo ( 'on' === $checked_by_default ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Checked by default', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'radio' === $field_type ) {
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$legend      = isset( $item['legend'] ) ? sanitize_text_field( $item['legend'] ) : '';
		$options     = isset( $item['options'] ) ? array_map( 'sanitize_text_field', $item['options'] ) : array();
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
			<?php esc_html_e( 'Radio', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>">
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Legend', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][legend]"
									value="<?php echo esc_html( $legend ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>
						<div class="item-group column2">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'ID', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Class', 'wah-forms' ); ?>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>">
							</label>
						</div>
						<ul class="item-group is-repeater is-radio-options">
						<?php
						if ( $options ) :
							$options_counter = 0;
							foreach ( $options as $radio_option ) :
								$options_counter++;
								$radio_option_name = str_replace( ' ', '_', $radio_option );
								?>
									<li class="draggable-item ui-draggable ui-draggable-handle">
										<label>
											<div class="label-name">
												<span class="dashicons dashicons-menu"></span>
												<?php esc_html_e( 'Option', 'wah-forms' ); ?>
											</div>
											<input type="text"
												name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][<?php echo esc_html( $radio_option_name ); ?>]" value="<?php echo esc_html( $radio_option ); ?>">
											<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $key ); ?>"
												title="<?php esc_html_e( 'Add option', 'wah-froms' ); ?>">
												<span class="dashicons dashicons-plus"></span>
											</button>
											<?php if ( 1 !== $options_counter ) : ?>
												<button class="remove-radio-option" data-field-index="<?php echo esc_html( $field_index ); ?>"
													title="<?php esc_html_e( 'Delete option', 'wah-froms' ); ?>">
													<span class="dashicons dashicons-trash"></span></span>
												</button>
											<?php endif; ?>
										</label>
									</li>
								<?php endforeach; ?>
							<?php else : ?>
								<li class="draggable-item ui-draggable ui-draggable-handle">
									<label>
										<div class="label-name">
											<span class="dashicons dashicons-menu"></span>
											<?php esc_html_e( 'Option', 'wah-forms' ); ?>
										</div>
										<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][]">
										<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $key ); ?>">
											<span class="dashicons dashicons-plus"></span>
										</button>
									</label>
								</li>
							<?php endif; ?>
						</ul>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
								<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
								<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'url' === $field_type ) {
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$maxlength   = isset( $item['maxlength'] ) ? sanitize_text_field( $item['maxlength'] ) : '';
		$minlength   = isset( $item['minlength'] ) ? sanitize_text_field( $item['minlength'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
				<?php esc_html_e( 'URL', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
							<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Max length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][maxlength]"
									value="<?php echo esc_html( $maxlength ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Min length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][minlength]"
									value="<?php echo esc_html( $minlength ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
							<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
						<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'email' === $field_type ) {
			$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
			$placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '';
			$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
			$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
			$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
			$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
			$maxlength   = isset( $item['maxlength'] ) ? sanitize_text_field( $item['maxlength'] ) : '';
			$minlength   = isset( $item['minlength'] ) ? sanitize_text_field( $item['minlength'] ) : '';
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
					<?php esc_html_e( 'Email', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
								<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'Max length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][maxlength]"
									value="<?php echo esc_html( $maxlength ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Min length', 'wah-forms' ); ?></div>
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][minlength]"
									value="<?php echo esc_html( $minlength ); ?>" >
							</label>
						</div>
						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
								<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
							<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'file' === $field_type ) {
			$label               = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
			$input_name          = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
			$input_id            = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
			$input_class         = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
			$required            = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
			$file_size_limit     = isset( $item['file_size_limit'] ) ? sanitize_text_field( $item['file_size_limit'] ) : '';
			$allowed_file_types  = wahforms_get_allowed_file_types();
			$selected_file_types = isset( $item['allowed_file_types'] ) ? array_map( 'esc_html', $item['allowed_file_types'] ) : array();
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
					<?php esc_html_e( 'File', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Placeholder', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][placeholder]"
									value="<?php echo esc_html( $placeholder ); ?>" >
							</label>
						</div>

						<div class="item-group">
							<label>
								<div class="label-name">
								<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
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
												name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][allowed_file_types][<?php echo esc_html( $file_key ); ?>]"
												<?php echo ( in_array( $file_key, array_keys( $selected_file_types ), true ) ) ? 'checked' : ''; ?>>
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
								<input type="number" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][file_size_limit]"
									value="<?php echo esc_html( $file_size_limit ); ?>">
							</label>
						</div>

						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
								<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
							<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	} elseif ( 'checkboxes' === $field_type ) {
		$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$legend      = isset( $item['legend'] ) ? sanitize_text_field( $item['legend'] ) : '';
		$input_name  = isset( $item['input_name'] ) ? sanitize_text_field( $item['input_name'] ) : '';
		$input_id    = isset( $item['input_id'] ) ? sanitize_text_field( $item['input_id'] ) : '';
		$input_class = isset( $item['input_class'] ) ? sanitize_text_field( $item['input_class'] ) : '';
		$required    = isset( $item['required'] ) ? sanitize_text_field( $item['required'] ) : '';
		$checkboxes  = isset( $item['options'] ) ? array_map( 'esc_html', $item['options'] ) : array();
		?>
		<li class="draggable-item ui-draggable ui-draggable-handle" data-index="<?php echo esc_html( $key ); ?>">
			<div class="wahforms-builder-item" data-field="<?php echo esc_html( $field_type ); ?>">
				<div class="title <?php echo $required ? 'is-red-asterix' : ''; ?>">
					<?php esc_html_e( 'Checkboxes', 'wah-forms' ); ?> [ <?php echo esc_html( $label ); ?> ]
					<button class="add-item closed" type="button">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>

				<div class="wahforms-builder-item-options" style="display:none;">
					<div class="item-fields">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][hidden_index]" value="<?php echo esc_html( $key ); ?>">
						<input type="hidden" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][field_type]"
							value="<?php echo esc_html( $field_type ); ?>">
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Label', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][label]"
									value="<?php echo esc_html( $label ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name"><?php esc_html_e( 'Legend', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][legend]"
									value="<?php echo esc_html( $legend ); ?>" >
							</label>
						</div>
						<div class="item-group">
							<label>
								<div class="label-name">
								<?php esc_html_e( 'Name attribute', 'wah-forms' ); ?>
									<span class="red-asterix">*</span>
								</div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_name]"
									value="<?php echo esc_html( $input_name ); ?>" required>
							</label>
						</div>

						<div class="item-group column2">
							<label>
								<div class="label-name"><?php esc_html_e( 'ID', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_id]"
									value="<?php echo esc_html( $input_id ); ?>" >
							</label>
							<label>
								<div class="label-name"><?php esc_html_e( 'Class', 'wah-forms' ); ?></div>
								<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][input_class]"
									value="<?php echo esc_html( $input_class ); ?>" >
							</label>
						</div>

						<ul class="item-group is-repeater is-radio-options">
						<?php
						if ( $checkboxes ) :
							$options_counter = 0;
							foreach ( $checkboxes as $checkbox_option ) :
								$options_counter++;
								$checkbox_option_name = str_replace( ' ', '_', $checkbox_option );
								?>
								<li class="draggable-item ui-draggable ui-draggable-handle">
									<label>
										<div class="label-name">
											<span class="dashicons dashicons-menu"></span>
											<?php esc_html_e( 'Option', 'wah-forms' ); ?>
										</div>
										<input type="text"
											name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][<?php echo esc_html( $checkbox_option_name ); ?>]" value="<?php echo esc_html( $checkbox_option ); ?>">
										<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $key ); ?>"
											title="<?php esc_html_e( 'Add option', 'wah-froms' ); ?>">
											<span class="dashicons dashicons-plus"></span>
										</button>
										<?php if ( 1 !== $options_counter ) : ?>
											<button class="remove-radio-option" data-field-index="<?php echo esc_html( $field_index ); ?>"
												title="<?php esc_html_e( 'Delete option', 'wah-froms' ); ?>">
												<span class="dashicons dashicons-trash"></span></span>
											</button>
										<?php endif; ?>
									</label>
								</li>
								<?php endforeach; ?>
							<?php else : ?>
								<label class="draggable-item ui-draggable ui-draggable-handle">
									<div class="label-name">
										<span class="dashicons dashicons-menu"></span>
										<?php esc_html_e( 'Option', 'wah-forms' ); ?>
									</div>
									<input type="text" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][options][]">
									<button type="button" class="add-radio-option" data-field-index="<?php echo esc_html( $key ); ?>">
										<span class="dashicons dashicons-plus"></span>
									</button>
								</label>
							<?php endif; ?>
						</ul>

						<div class="item-group is-checkbox">
							<hr>
							<label>
								<input type="checkbox" name="wahform_fields[fields][<?php echo esc_html( $key ); ?>][required]"
								<?php echo ( 'on' === $required ) ? 'checked' : ''; ?>>
								<div class="label-name"><?php esc_html_e( 'Required', 'wah-forms' ); ?></div>
							</label>
						</div>
						<div class="item-group delete-this-item">
							<button type="button" data-index="<?php echo esc_html( $key ); ?>" title="<?php esc_html_e( 'Delete', 'wah-forms' ); ?>">
							<?php esc_html_e( 'Delete', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</li>
		<?php
	}

	$html = ob_get_clean();
	echo wp_kses( $html, wahforms_admin_settings_get_allowed_html() );
}
/**
 * WAHForms get_allowed_file_types
 *
 * @return array file types array
 */
function wahforms_get_allowed_file_types() {
	return array(
		'.xls,.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel' => 'Excel',
		'.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
		'image/*'              => 'Images',
		'audio/*'              => 'Audio',
		'video/*'              => 'Video',
		'application/pdf,.pdf' => 'PDF',
	);
}

// Remove default publish metabox.
add_action(
	'admin_menu',
	function() {
		// remove_meta_box( 'submitdiv', 'wah_forms', 'normal' );
		remove_meta_box( 'slugdiv', 'wah_forms', 'normal' );
	}
);

/**
 * Number columns in admin edit page
 *
 * @param  array $columns  columns.
 * @return integer number
 */
function wahfroms_screen_layout_columns( $columns ) {
	$columns['post'] = 1;
	return $columns;
}
/**
 * Screen Layout
 *
 * @return integer column numbers
 */
function wahforms_screen_layout_post() {
	return 1;
}
