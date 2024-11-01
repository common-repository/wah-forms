<?php
/**
 * WAHForms public AJAX
 *
 * @package WAHForms
 */

add_action( 'wp_ajax_wahforms_public_submit_form', 'wahforms_public_submit_form' );
add_action( 'wp_ajax_nopriv_wahforms_public_submit_form', 'wahforms_public_submit_form' );
/**
 * WAHForms handle form submit
 */
function wahforms_public_submit_form() {
	// Check for nonce security.
	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wahforms-public-ajax-nonce' ) ) {
		die( 'Busted! Security varification failed.' );
	}

	$response = array(
		'message'   => '',
		'error'     => false,
		'success'   => false,
		'lead_id'   => '',
		'redirect'  => '',
		'mail_sent' => false,
	);

	$form_data = wahforms_get_form_data( $_POST );

	$form_file       = isset( $_FILES ) ? $_FILES : array();
	$form_file_array = array();

	if ( $form_file ) {
		$form_file_array = wahforms_build_files_array( $form_file['file'] );
	}

	$form_id = isset( $form_data['wahforms_id'] ) ? sanitize_text_field( $form_data['wahforms_id'] ) : (int) $form_data['wahforms_id'];

	do_action( 'wahforms_before_save_lead', $form_id, $form_data, $form_file_array );
	$data                        = wahforms_save_lead( $form_data, $form_file_array );
	$response['lead_id']         = $data['lead_id'];
	$response['file_type_error'] = $data['file_type_error'];
	$response['lead_error']      = $data['lead_error'];
	do_action( 'wahforms_after_save_lead', $form_id, $form_data, $response['lead_id'], $form_file_array );

	do_action( 'wahforms_before_send_mail', $form_id, $form_data, $form_file_array );
	$response['mail_sent'] = wahforms_send_mail( $form_id, $form_data, $data['lead_id'] );
	do_action( 'wahforms_after_send_mail', $form_id, $form_data, $form_file_array );

	$wahform_fields = get_post_meta( $form_id, 'wahform_fields', true );

	$success_message = isset( $wahform_fields['settings']['mail_sent_message'] ) ? sanitize_text_field( $wahform_fields['settings']['mail_sent_message'] ) : __( 'Email sent successfully! Thank you.', 'wah-froms' );

	$error_message = isset( $wahform_fields['settings']['mail_error_message'] ) ? sanitize_text_field( $wahform_fields['settings']['mail_error_message'] ) : __( 'Email has not been sent. Please try again.', 'wah-froms' );
	if ( $response['mail_sent'] ) {
		$response['success'] = true;
		$response['message'] = '<span class="wahforms-ajax-success">' . esc_html( $success_message ) . '</span>';
	} else {
		$response['error']   = true;
		$response['message'] = '<span class="wahforms-ajax-error">' . esc_html( $error_message ) . '</span>';
	}

	wp_send_json( $response );
}
/**
 * Build files array
 *
 * @param  array $file_post  posted files.
 * @return array            array of files
 */
function wahforms_build_files_array( &$file_post ) {

	$file_ary   = array();
	$file_count = count( $file_post['name'] );
	$file_keys  = array_keys( $file_post );

	for ( $i = 0; $i < $file_count; $i++ ) {
		foreach ( $file_keys as $key ) {
			$file_ary[ $i ][ $key ] = $file_post[ $key ][ $i ];
		}
	}

	return $file_ary;
}
/**
 * WAHForms_send_mail
 *
 * @param  string $form_id    form ID.
 * @param  array  $form_data  submitted form data.
 * @param string $lead_id lead ID.
 * @return boolean            send mail status.
 */
function wahforms_send_mail( $form_id, $form_data, $lead_id ) {
	$lead_attachments = array();
	$attachments      = array();
	if ( $lead_id ) {
		$lead_attachments = get_post_meta( $lead_id, 'lead_attachments', true );
		if ( $lead_attachments ) {
			$lead_attachments = array_keys( $lead_attachments );

			foreach ( $lead_attachments as $att_id ) {
				$attachments[] = get_attached_file( $att_id );
			}
		}
	}

	$wahform_fields = get_post_meta( $form_id, 'wahform_fields', true );
	$fields         = $wahform_fields['fields'];
	$settings       = isset( $wahform_fields['settings'] ) ? $wahform_fields['settings'] : array();

	$mail_body = wahforms_get_email_body( $wahform_fields, $form_data );

	$to      = ( isset( $settings['mailto'] ) && $settings['mailto'] ) ? sanitize_email( $settings['mailto'] ) : sanitize_email( get_option( 'admin_email' ) );
	$subject = ( isset( $settings['subject'] ) && $settings['subject'] ) ? sanitize_text_field( $settings['subject'] ) : 'WAHForms #' . esc_html( $form_id );

	$email_body = isset( $settings['email_body'] ) && $settings['email_body'] ? wp_kses_post( $settings['email_body'] ) : '';

	$headers   = 'From: ' . sanitize_text_field( $settings['mailfrom_sender_name'] ) . ' <' . sanitize_email( $settings['mailfrom_sender_email'] ) . '>' . "\r\n";
	$headers  .= "MIME-Version: 1.0\r\n";
	$headers  .= "Content-Type: text/html; charset=UTF-8\r\n";
	$to        = 'vol4ikman@gmail.com';
	$mail_send = wp_mail( $to, $subject, $mail_body, $headers, $attachments );
	return $mail_send;
}
/**
 * WAHForms get email body content
 *
 * @param  array $wahform_fields               fields.
 * @param  array $form_data                    submitted data.
 * @return string                 email body.
 */
function wahforms_get_email_body( $wahform_fields, $form_data ) {
	return wp_kses_post( wahforms_extract_data( $wahform_fields, $form_data ) );
}
/**
 * Get post data
 *
 * @param  array $post_data  posted data array.
 */
function wahforms_get_form_data( $post_data ) {
	$form_data = array();
	if ( isset( $post_data['form'] ) && $post_data['form'] ) {
		parse_str( $post_data['form'], $form_data );
	}
	return $form_data;
}
/**
 * WAHForms save_lead
 *
 * @param  array $form_data  lead data.
 * @param array $form_file_array files.
 * @return string      lead post id.
 */
function wahforms_save_lead( $form_data, $form_file_array ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-includes/media.php';

	$response        = array(
		'lead_id'         => '',
		'file_type_error' => '',
		'lead_error'      => '',
	);
	$current_date    = gmdate( 'Y-m-d H:i:s' );
	$lead_post_title = 'Lead ' . $current_date;
	$lead_post       = array(
		'post_title'   => esc_html( $lead_post_title ),
		'post_type'    => 'wahforms_lead',
		'post_content' => '',
		'post_status'  => 'publish',
	);

	// Insert the post into the database.
	$lead_post_id = wp_insert_post( $lead_post );

	$wahform_fields = get_post_meta( $form_data['wahforms_id'], 'wahform_fields', true );
	if ( $wahform_fields ) {
		$wahform_fields = $wahform_fields['fields'];
		$attachment_ids = array();
		// validate allowed file types.
		if ( $form_file_array ) {
			foreach ( $form_file_array as $file ) {
				$uploaded_file = wp_handle_upload( $file, array( 'test_form' => false ) );
				if ( $uploaded_file ) {
					$attachment_id = wahforms_insert_attachment_from_url( $uploaded_file['url'] );
					if ( $attachment_id ) {
						$attachment_ids[ $attachment_id ] = $uploaded_file['url'];
					}
				}
			}
		}
	}

	if ( ! is_wp_error( $lead_post_id ) ) {
		$response['lead_id'] = $lead_post_id;
		update_post_meta( $lead_post_id, 'form_id', $form_data['wahforms_id'] );
		update_post_meta( $lead_post_id, 'lead_data', $form_data );
		if ( $attachment_ids ) {
			update_post_meta( $lead_post_id, 'lead_attachments', $attachment_ids );
		}
	} else {
		$response['lead_error'] = esc_html__( 'Error, Leas not saved.', 'wah-forms' );
	}

	return $response;

}

/**
 * Insert an attachment from a URL address.
 *
 * @param  string   $url            The URL address.
 * @param  int|null $parent_post_id The parent post ID (Optional).
 * @return int|false                The attachment ID on success. False on failure.
 */
function wahforms_insert_attachment_from_url( $url, $parent_post_id = null ) {

	if ( ! class_exists( 'WP_Http' ) ) {
		require_once ABSPATH . WPINC . '/class-http.php';
	}

	$http     = new WP_Http();
	$response = $http->request( $url );
	if ( 200 !== $response['response']['code'] ) {
		return false;
	}

	$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
	if ( ! empty( $upload['error'] ) ) {
		return false;
	}

	$file_path        = $upload['file'];
	$file_name        = basename( $file_path );
	$file_type        = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir    = wp_upload_dir();

	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Create the attachment.
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

	// Include image.php.
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Generate the attachment metadata.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment.
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;

}
