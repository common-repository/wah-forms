<?php
/**
 * Initialize public plugin functions
 *
 * @package WAHForms
 */

add_action( 'init', 'wahforms_post_type', 0 );
add_shortcode( 'wahforms', 'wahforms_form_shortcode' );

/**
 * Register WAHForms Post Type
 */
function wahforms_post_type() {

	$labels = array(
		'name'                  => _x( 'WAH Forms', 'Post Type General Name', 'wah-forms' ),
		'singular_name'         => _x( 'WAH Form', 'Post Type Singular Name', 'wah-forms' ),
		'menu_name'             => __( 'WAH Forms', 'wah-forms' ),
		'name_admin_bar'        => __( 'WAH Forms', 'wah-forms' ),
		'archives'              => __( 'Item Archives', 'wah-forms' ),
		'attributes'            => __( 'Item Attributes', 'wah-forms' ),
		'parent_item_colon'     => __( 'Parent Item:', 'wah-forms' ),
		'all_items'             => __( 'All Items', 'wah-forms' ),
		'add_new_item'          => __( 'Add New Item', 'wah-forms' ),
		'add_new'               => __( 'Add New WAH Form', 'wah-forms' ),
		'new_item'              => __( 'New WAH Form', 'wah-forms' ),
		'edit_item'             => __( 'Edit WAH Form', 'wah-forms' ),
		'update_item'           => __( 'Update Item', 'wah-forms' ),
		'view_item'             => __( 'View Item', 'wah-forms' ),
		'view_items'            => __( 'View Items', 'wah-forms' ),
		'search_items'          => __( 'Search Item', 'wah-forms' ),
		'not_found'             => __( 'Not found', 'wah-forms' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wah-forms' ),
		'featured_image'        => __( 'Featured Image', 'wah-forms' ),
		'set_featured_image'    => __( 'Set featured image', 'wah-forms' ),
		'remove_featured_image' => __( 'Remove featured image', 'wah-forms' ),
		'use_featured_image'    => __( 'Use as featured image', 'wah-forms' ),
		'insert_into_item'      => __( 'Insert into item', 'wah-forms' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'wah-forms' ),
		'items_list'            => __( 'Items list', 'wah-forms' ),
		'items_list_navigation' => __( 'Items list navigation', 'wah-forms' ),
		'filter_items_list'     => __( 'Filter items list', 'wah-forms' ),
	);
	$args   = array(
		'label'               => __( 'WAH Form', 'wah-forms' ),
		'description'         => __( 'WAH Forms', 'wah-forms' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-feedback',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
		'show_in_rest'        => true,
	);
	register_post_type( 'wah_forms', $args );

}
/**
 * WAHForms shortcode
 *
 * @param  array   $atts                  attributes.
 * @param  boolean $content               content.
 * @return string          contact form html output
 */
function wahforms_form_shortcode( $atts, $content = null ) {

	global $post;

	$atts = shortcode_atts(
		array(
			'id' => '',
		),
		$atts,
		'wahforms'
	);

	ob_start();
	if ( isset( $atts['id'] ) && $atts['id'] ) {
		$form_params = wahforms_get_params_by_id( $atts['id'] );
		$fields      = isset( $form_params['fields'] ) ? $form_params['fields'] : array();

		$settings     = isset( $form_params['settings'] ) ? $form_params['settings'] : array();
		$submit_title = isset( $settings['submit_title'] ) && ! empty( $settings['submit_title'] ) ? esc_html( $settings['submit_title'] ) : __( 'Submit', 'wah-forms' );
		$submit_class = isset( $settings['submit_class'] ) && ! empty( $settings['submit_class'] ) ? sanitize_text_field( str_replace( '.', '', $settings['submit_class'] ) ) : '';
		if ( $fields ) {
			?>
			<div class="wahforms-wrapper" data-id="<?php echo esc_html( $atts['id'] ); ?>">
				<form class="wahforms-form" method="post" action="#wahform"
					<?php if ( wahforms_has_file( $fields ) ) : ?>
						enctype="multipart/form-data"
					<?php endif; ?>>
					<input type="hidden" name="wahforms_id" value="<?php echo esc_html( $atts['id'] ); ?>">
					<input type="hidden" name="post_id" value="<?php echo isset( $post->ID ) ? esc_html( $post->ID ) : ''; ?>">
					<?php do_action( 'wahforms_append_fields', $atts['id'] ); ?>
				<?php
				$fields_index = 0;
				foreach ( $fields as $field ) {
					$fields_index++;
					echo wahforms_frontend_render_input( $field, $fields_index );
				}
				?>
				<div class="wahforms-submit-wrapper">
					<button type="submit" <?php echo $submit_class ? 'class="' . esc_html( $submit_class ) . '"' : ''; ?>>
						<?php echo esc_html( $submit_title ); ?>
					</button>
				</div>
				<div class="wahforms-ajax-response"></div>
				</form>
			</div>
			<?php
		}
	}

	return ob_get_clean();

}
/**
 * Check if WAHForms has file input field.
 *
 * @param  array $fields  array of all form fields.
 * @return boolean         true/false
 */
function wahforms_has_file( $fields ) {
	if ( $fields ) {
		foreach ( $fields as $field ) {
			if ( 'file' === $field['field_type'] ) {
				return true;
			}
		}
	}
	return false;
}
/**
 * WAHForms frontend_render_input
 *
 * @param  array   $field                    field params.
 * @param  integer $fields_index             field index/count.
 * @return string               input item html code
 */
function wahforms_frontend_render_input( $field, $fields_index ) {
	$field_type  = isset( $field['field_type'] ) ? sanitize_text_field( $field['field_type'] ) : '';
	$label       = isset( $field['label'] ) && $field['label'] ? sanitize_text_field( $field['label'] ) : '';
	$required    = isset( $field['required'] ) && ( 'on' === $field['required'] ) ? true : false;
	$placeholder = isset( $field['placeholder'] ) && $field['placeholder'] ? sanitize_text_field( $field['placeholder'] ) : '';
	$input_name  = isset( $field['input_name'] ) && $field['input_name'] ? $field['input_name'] : '';
	$input_id    = isset( $field['input_id'] ) && $field['input_id'] ? sanitize_text_field( $field['input_id'] ) : '';
	$input_class = isset( $field['input_class'] ) && $field['input_class'] ? sanitize_text_field( $field['input_class'] ) : '';
	ob_start();
	if ( 'text' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$maxlength = isset( $field['maxlength'] ) && $field['maxlength'] ? sanitize_text_field( $field['maxlength'] ) : '';
		$minlength = isset( $field['minlength'] ) && $field['minlength'] ? sanitize_text_field( $field['minlength'] ) : '';
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input type="text" value=""
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $maxlength ) : ?>
				maxlength="<?php echo esc_html( $maxlength ); ?>"
			<?php endif; ?>
			<?php if ( $minlength ) : ?>
				minlength="<?php echo esc_html( $minlength ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'url' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$maxlength = isset( $field['maxlength'] ) && $field['maxlength'] ? sanitize_text_field( $field['maxlength'] ) : '';
		$minlength = isset( $field['minlength'] ) && $field['minlength'] ? sanitize_text_field( $field['minlength'] ) : '';
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input type="url" value=""
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $maxlength ) : ?>
				maxlength="<?php echo esc_html( $maxlength ); ?>"
			<?php endif; ?>
			<?php if ( $minlength ) : ?>
				minlength="<?php echo esc_html( $minlength ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'email' === $field_type ) :
			wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
			$maxlength = isset( $field['maxlength'] ) && $field['maxlength'] ? sanitize_text_field( $field['maxlength'] ) : '';
			$minlength = isset( $field['minlength'] ) && $field['minlength'] ? sanitize_text_field( $field['minlength'] ) : '';
		?>
			<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input type="email" value=""
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $maxlength ) : ?>
				maxlength="<?php echo esc_html( $maxlength ); ?>"
			<?php endif; ?>
			<?php if ( $minlength ) : ?>
				minlength="<?php echo esc_html( $minlength ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
			<?php
			wahforms_frontend_input_wrapper_end();
	elseif ( 'number' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$max = isset( $field['max'] ) && $field['max'] ? sanitize_text_field( $field['max'] ) : '';
		$min = isset( $field['min'] ) && $field['min'] ? sanitize_text_field( $field['min'] ) : '';
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input type="number" value=""
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $max ) : ?>
				max="<?php echo esc_html( $max ); ?>"
			<?php endif; ?>
			<?php if ( $min ) : ?>
				min="<?php echo esc_html( $min ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'tel' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$maxlength = isset( $field['maxlength'] ) && $field['maxlength'] ? sanitize_text_field( $field['maxlength'] ) : '';
		$minlength = isset( $field['minlength'] ) && $field['minlength'] ? sanitize_text_field( $field['minlength'] ) : '';
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input type="tel" value=""
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $maxlength ) : ?>
				maxlength="<?php echo esc_html( $maxlength ); ?>"
			<?php endif; ?>
			<?php if ( $minlength ) : ?>
				minlength="<?php echo esc_html( $minlength ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'textarea' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$maxlength = isset( $field['maxlength'] ) && $field['maxlength'] ? sanitize_text_field( $field['maxlength'] ) : '';
		$minlength = isset( $field['minlength'] ) && $field['minlength'] ? sanitize_text_field( $field['minlength'] ) : '';
		$rows      = isset( $field['rows'] ) && $field['rows'] ? sanitize_text_field( $field['rows'] ) : '';
		$cols      = isset( $field['cols'] ) && $field['cols'] ? sanitize_text_field( $field['cols'] ) : '';
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<textarea
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . $input_name ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $maxlength ) : ?>
				maxlength="<?php echo esc_html( $maxlength ); ?>"
			<?php endif; ?>
			<?php if ( $minlength ) : ?>
				minlength="<?php echo esc_html( $minlength ); ?>"
			<?php endif; ?>
			<?php if ( $rows ) : ?>
				rows="<?php echo esc_html( $rows ); ?>"
			<?php endif; ?>
			<?php if ( $cols ) : ?>
				cols="<?php echo esc_html( $cols ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>></textarea>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'selectbox' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$maxlength         = isset( $field['maxlength'] ) && $field['maxlength'] ? sanitize_text_field( $field['maxlength'] ) : '';
		$minlength         = isset( $field['minlength'] ) && $field['minlength'] ? sanitize_text_field( $field['minlength'] ) : '';
		$selectbox_options = isset( $field['options'] ) && $field['options'] ? $field['options'] : '';
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<select
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . $input_name ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
			<?php if ( is_array( $selectbox_options ) ) : ?>
				<?php
				foreach ( $selectbox_options as $option_key => $option_value ) :
					?>
					<option value="<?php echo esc_html( $option_value['key'] ); ?>">
						<?php echo esc_html( $option_value['value'] ); ?>
					</option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'checkbox' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$checked_by_default = isset( $field['checked_by_default'] ) && ( 'on' === $field['checked_by_default'] ) ? true : false;
		?>
		<input type="checkbox"
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $checked_by_default ) : ?>
				checked
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'radio' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$legend        = isset( $field['legend'] ) && $field['legend'] ? sanitize_text_field( $field['legend'] ) : '';
		$radio_options = isset( $field['options'] ) && $field['options'] ? $field['options'] : array();
		?>
		<fieldset>
			<legend><?php echo esc_html( $legend ? $legend : $label ); ?></legend>
			<?php
			if ( $radio_options ) :
				$radio_options_index = 0;
				foreach ( $radio_options as $option_key => $option_value ) :
					$radio_options_index++;
					?>
					<div class="radio-option-item index-<?php echo esc_html( $radio_options_index ); ?>">
						<input id="radio-option-<?php echo esc_html( $fields_index ); ?>-<?php echo esc_html( $radio_options_index ); ?>"
							type="radio" name="<?php echo esc_html( $input_name ); ?>" value="<?php echo esc_html( $option_value ); ?>">
						<label for="radio-option-<?php echo esc_html( $fields_index ); ?>-<?php echo esc_html( $radio_options_index ); ?>">
							<?php echo esc_html( $option_value ); ?>
						</label>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</fieldset>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'checkboxes' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$legend           = isset( $field['legend'] ) && $field['legend'] ? sanitize_text_field( $field['legend'] ) : '';
		$checkbox_options = isset( $field['options'] ) && $field['options'] ? $field['options'] : array();
		?>
		<fieldset>
			<legend><?php echo esc_html( $legend ? $legend : $label ); ?></legend>
			<?php
			if ( $checkbox_options ) :
				$checkbox_options_index = 0;
				foreach ( $checkbox_options as $option_key => $option_value ) :
					$checkbox_options_index++;
					?>
					<div class="checkbox-option-item index-<?php echo esc_html( $checkbox_options_index ); ?>">
						<input id="checkbox-option-<?php echo esc_html( $fields_index ); ?>-<?php echo esc_html( $checkbox_options_index ); ?>"
							type="checkbox" name="<?php echo esc_html( $input_name ); ?>[]" value="<?php echo esc_html( $option_value ); ?>">
						<label for="checkbox-option-<?php echo esc_html( $fields_index ); ?>-<?php echo esc_html( $checkbox_options_index ); ?>">
							<?php echo esc_html( $option_value ); ?>
						</label>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</fieldset>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'file' === $field_type ) :
		wahforms_frontend_input_wrapper_start( $field_type, $fields_index );
		$file_size_limit = isset( $field['file_size_limit'] ) ? sanitize_text_field( $field['file_size_limit'] ) : '';
		if ( $file_size_limit ) {
			$file_size_limit = (int) $file_size_limit * 1024 * 1024;
		}
		$allowed_file_types = isset( $field['allowed_file_types'] ) && $field['allowed_file_types'] ? array_keys( $field['allowed_file_types'] ) : array();
		?>
		<?php if ( $label ) : ?>
			<label for="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input type="hidden" name="file_hidden_index_<?php echo esc_html( $fields_index ); ?>" value="<?php echo esc_html( $fields_index ); ?>">
		<input type="file" value=""
			id="<?php echo esc_html( $input_id ? $input_id : 'id-' . str_replace( ' ', '_', $input_name ) ); ?>"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>
			<?php if ( $placeholder ) : ?>
				placeholder="<?php echo esc_html( $placeholder ); ?>"
			<?php endif; ?>
			<?php if ( $required ) : ?>
				required aria-required="true"
			<?php endif; ?>
			<?php if ( $file_size_limit ) : ?>
				data-sl="<?php echo esc_html( $file_size_limit ); ?>"
			<?php endif; ?>
			<?php if ( $allowed_file_types ) : ?>
				accept="<?php echo esc_html( implode( ',', $allowed_file_types ) ); ?>"
			<?php endif; ?>>
		<?php
		wahforms_frontend_input_wrapper_end();
	elseif ( 'hidden' === $field_type ) :
		$input_value = isset( $field['input_value'] ) && $field['input_value'] ? sanitize_text_field( $field['input_value'] ) : '';
		$input_name  = isset( $field['input_name'] ) && $field['input_name'] ? sanitize_text_field( str_replace( ' ', '_', $field['input_name'] ) ) : '';
		$input_id    = isset( $field['input_id'] ) && $field['input_id'] ? sanitize_text_field( $field['input_id'] ) : '';
		$input_class = isset( $field['input_class'] ) && $field['input_class'] ? sanitize_text_field( $field['input_class'] ) : '';
		?>
		<input type="hidden"
			<?php if ( $input_name ) : ?>
				name="<?php echo esc_html( $input_name ); ?>"
			<?php endif; ?>
			<?php if ( $input_value ) : ?>
				value="<?php echo esc_html( $input_value ); ?>"
			<?php endif; ?>
			<?php if ( $input_id ) : ?>
				id="<?php echo esc_html( $input_id ); ?>"
			<?php endif; ?>
			<?php if ( $input_class ) : ?>
				class="<?php echo esc_html( $input_class ); ?>"
			<?php endif; ?>>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

/**
 * WAHF get_params_by_id get form params by form/post id.
 *
 * @param  string $id form/post ID.
 * @return array     forms setings
 */
function wahforms_get_params_by_id( $id ) {
	$params = get_post_meta( $id, 'wahform_fields', true );
	return $params;
}

if ( ! function_exists( 'wahforms_leads_post_type' ) ) {

	/**
	 * Register WAHForms Lead post type
	 */
	function wahforms_leads_post_type() {

		$labels = array(
			'name'                  => _x( 'Leads', 'Post Type General Name', 'wah-forms' ),
			'singular_name'         => _x( 'Lead', 'Post Type Singular Name', 'wah-forms' ),
			'menu_name'             => __( 'Leads', 'wah-forms' ),
			'name_admin_bar'        => __( 'Lead', 'wah-forms' ),
			'archives'              => __( 'Item Archives', 'wah-forms' ),
			'attributes'            => __( 'Item Attributes', 'wah-forms' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wah-forms' ),
			'all_items'             => __( 'All Items', 'wah-forms' ),
			'add_new_item'          => __( 'Add New Item', 'wah-forms' ),
			'add_new'               => __( 'Add New', 'wah-forms' ),
			'new_item'              => __( 'New Item', 'wah-forms' ),
			'edit_item'             => __( 'Edit Item', 'wah-forms' ),
			'update_item'           => __( 'Update Item', 'wah-forms' ),
			'view_item'             => __( 'View Item', 'wah-forms' ),
			'view_items'            => __( 'View Items', 'wah-forms' ),
			'search_items'          => __( 'Search Item', 'wah-forms' ),
			'not_found'             => __( 'Not found', 'wah-forms' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wah-forms' ),
			'featured_image'        => __( 'Featured Image', 'wah-forms' ),
			'set_featured_image'    => __( 'Set featured image', 'wah-forms' ),
			'remove_featured_image' => __( 'Remove featured image', 'wah-forms' ),
			'use_featured_image'    => __( 'Use as featured image', 'wah-forms' ),
			'insert_into_item'      => __( 'Insert into item', 'wah-forms' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wah-forms' ),
			'items_list'            => __( 'Items list', 'wah-forms' ),
			'items_list_navigation' => __( 'Items list navigation', 'wah-forms' ),
			'filter_items_list'     => __( 'Filter items list', 'wah-forms' ),
		);
		$args   = array(
			'label'               => __( 'Lead', 'wah-forms' ),
			'description'         => __( 'Leads', 'wah-forms' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false, // XXX.
			'show_in_menu'        => false, // XXX.
			'menu_position'       => 5,
			'show_in_admin_bar'   => false, // XXX.
			'show_in_nav_menus'   => false, // XXX.
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'wahforms_lead', $args );

	}
	add_action( 'init', 'wahforms_leads_post_type', 0 );

}
