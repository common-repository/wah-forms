<?php
/**
 * Admin scripts and styles
 *
 * @package WAHForms
 */

add_action( 'admin_enqueue_scripts', 'wah_forms_register_plugin_scripts' );
add_action( 'admin_enqueue_scripts', 'wah_forms_load_plugin_scripts' );

/**
 * Register plugin scripts
 */
function wah_forms_register_plugin_scripts() {
	// $ver = WAHFORMS_VERSION;
	$ver = 'v-' . time();
	wp_register_style( 'wah-forms-jquery-ui', plugins_url( 'admin/css/jquery-ui.css', __DIR__ ), array(), $ver );
	wp_register_style( 'wah-forms', plugins_url( 'admin/css/plugin.css', __DIR__ ), array(), $ver );
	if ( is_rtl() ) {
		wp_register_style( 'wah-forms-rtl', plugins_url( 'admin/css/plugin-rtl.css', __DIR__ ), array(), $ver );
	}
	wp_register_script( 'wah-forms', plugins_url( 'admin/js/plugin.js', __DIR__ ), array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable', 'jquery-ui-tabs' ), $ver, true );
}
/**
 * Load plugin scripts
 *
 * @param  string $hook  hook name.
 */
function wah_forms_load_plugin_scripts( $hook ) {
	global $post;
	$allowed = false;

	$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';

	if ( 'post.php' === $hook && isset( $post->ID ) && $post->ID && 'wah_forms' === $post->post_type ) {
		$allowed = true;
	} elseif ( 'settings_page_wahforms-page' === $hook || ( $post_type && 'wah_forms' === $post_type && 'post-new.php' === $hook ) ) {
		$allowed = true;
	}

	if ( $allowed ) {
		wp_enqueue_style( 'wah-forms-jquery-ui' );
		wp_enqueue_style( 'wah-forms' );

		wp_enqueue_script( 'wah-forms' );
		wp_localize_script(
			'wah-forms',
			'wahforms_vars',
			array(
				'nonce' => wp_create_nonce( 'ajax-nonce-backend' ),
			)
		);
	}
}
