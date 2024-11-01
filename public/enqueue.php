<?php
/**
 * WAHForms public scripts and styles
 *
 * @package WAHForms
 */

add_action( 'wp_enqueue_scripts', 'wah_forms_public_plugin_scripts' );

/**
 * WAHForms public_plugin_scripts
 */
function wah_forms_public_plugin_scripts() {
	// WAHFORMS_VERSION.

	$ver = 'v-' . time();
	wp_register_style( 'wahforms', plugins_url( 'public/css/wahforms.css', __DIR__ ), array(), $ver );
	wp_enqueue_style( 'wahforms' );

	wp_register_script( 'wahforms', plugins_url( 'public/js/wahforms.js', __DIR__ ), array( 'jquery' ), $ver, true );
	wp_enqueue_script( 'wahforms' );

	$wahforms_localize_data = array(
		'nonce'                  => wp_create_nonce( 'wahforms-public-ajax-nonce' ),
		'ajax'                   => admin_url( 'admin-ajax.php' ),
		'validate_before_submit' => wahforms_get_option( 'wahforms_validation_before_submit' ),
		'submit_errors'          => array(
			'message_limit' => __( 'File size is too big. Please choose another file.', 'wah-forms' ),
			'message_type'  => __( 'File type is not allowed.', 'wah-forms' ),
		),
	);

	if ( wahforms_get_option( 'wahforms_validation_before_submit' ) ) {
		$wahforms_localize_data['presubmit_errors']['required'] = '<div class="required-message">This field is required.</div>';
	}

	wp_localize_script( 'wahforms', 'wahforms_vars', $wahforms_localize_data );
}
