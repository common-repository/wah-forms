<?php
/**
 * WAHForms Hooks
 *
 * @package WAHForms
 */

/**
 * WAHForms frontend_render_input_wrapper_start
 *
 * @param  string $field_type   field type.
 * @param  string $fields_index field index.
 */
function wahforms_frontend_input_wrapper_start( $field_type, $fields_index ) {
	ob_start();
	?>
		<span class="wf-controls wf-type-<?php echo esc_html( $field_type ); ?> wfitem-index-<?php echo esc_html( $fields_index ); ?>">
	<?php
	$html = ob_get_clean();
	echo wp_kses_post( apply_filters( 'wahforms_frontend_input_wrapper_start_filter', $html ) );
}
/**
 * WAHForms frontend_render_input_wrapper_end
 */
function wahforms_frontend_input_wrapper_end() {
	ob_start();
	?>
	</span>
	<?php
	$html = ob_get_clean();
	echo wp_kses_post( apply_filters( 'wahforms_frontend_input_wrapper_end_filter', $html ) );
}
