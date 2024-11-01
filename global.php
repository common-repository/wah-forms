<?php
/**
 * WAHForms global functions
 *
 * @package WAHForms
 */

/**
 * WAHForms_save_option
 *
 * @param  string $option_name  option name.
 * @param  mixed  $option_value option value string/array.
 */
function wahforms_save_option( $option_name, $option_value ) {
	update_option( $option_name, $option_value );
}
/**
 * WAHForms_save_option
 *
 * @param  string $option_name  option name.
 * @return mixed option value string/array.
 */
function wahforms_get_option( $option_name ) {
	return get_option( $option_name );
}
/**
 * Extract WAHForms data
 *
 * @param  array $wahform_fields all form fields array.
 * @param  array $form_data      array.
 */
function wahforms_extract_data( $wahform_fields, $form_data ) {
	$form_id = isset( $form_data['wahforms_id'] ) ? $form_data['wahforms_id'] : '';
	$post_id = isset( $form_data['post_id'] ) ? $form_data['post_id'] : '';

	if ( $form_id ) {
		if ( isset( $wahform_fields['fields'] ) && is_array( $wahform_fields['fields'] ) && $wahform_fields['fields'] ) {

			foreach ( $wahform_fields['fields'] as $param ) {

				foreach ( $form_data as $submit_input_name => $submit_input_value ) {

					if ( 'checkboxes' === $param['field_type'] ) {
						$submit_input_value = implode( ', ', $submit_input_value );
					} else {
						$submit_input_value = $submit_input_value;
					}
					$text_tag    = '[wfinput type="' . $param['field_type'] . '" index="' . $param['hidden_index'] . '"]';
					$input_name  = isset( $param['input_name'] ) ? $param['input_name'] : '';
					$input_index = isset( $param['hidden_index'] ) ? $param['hidden_index'] : '';
					if ( $input_name && $submit_input_name === $input_name ) {
						$wahform_fields['settings']['email_body'] = str_replace( $text_tag, $submit_input_value, $wahform_fields['settings']['email_body'] );
					}
				}
			}
		}

		if ( strpos( $wahform_fields['settings']['email_body'], '[wfinput type="submit_date"]' ) !== false ) {
			$wahform_fields['settings']['email_body'] = str_replace( '[wfinput type="submit_date"]', gmdate( 'd/m/Y H:i:s' ), $wahform_fields['settings']['email_body'] );
		}
		if ( isset( $post_id ) && $post_id ) {
			if ( strpos( $wahform_fields['settings']['email_body'], '[wfinput type="submit_url"]' ) !== false ) {
				$wahform_fields['settings']['email_body'] = str_replace( '[wfinput type="submit_url"]', get_permalink( $post_id ), $wahform_fields['settings']['email_body'] );
			}
		}
		// clear files shortcode from the email body.
		$files_shortcodes_to_find    = array();
		$files_shortcodes_to_replace = array();
		for ( $x = 1; $x <= 30; $x++ ) {
			$files_shortcodes_to_find[]    = '[wfinput type="file" index="' . $x . '"]';
			$files_shortcodes_to_replace[] = esc_html__( 'File attached to the mail.', 'wah-forms' );
		}

		$wahform_fields['settings']['email_body'] = str_replace( $files_shortcodes_to_find, $files_shortcodes_to_replace, $wahform_fields['settings']['email_body'] );
	}
	return $wahform_fields['settings']['email_body'];
}
