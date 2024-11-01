<?php
/**
 * WAH Forms admin init
 *
 * @package WAHForms
 */

add_action( 'post_updated', 'wahforms_save_post_callback', 10, 3 );

/**
 * WAHForms save_post_callback
 *
 * @param  string $post_id                   Post ID.
 * @param  object $post_after                Post before update.
 * @param  object $post_before               Post after update.
 */
function wahforms_save_post_callback( $post_id, $post_after, $post_before ) {
	$this_post = get_post( $post_id );
	$post_type = $this_post->post_type;
	if ( 'wah_forms' === $post_type ) {
		$wahform_fields = isset( $_POST['wahform_fields'] ) ? array_map( 'wahforms_sanitize_array', (array) $_POST['wahform_fields'] ) : array();
		update_post_meta( $post_id, 'wahform_fields', $wahform_fields );
	}
}
/**
 * Wahforms_sanitize_array
 *
 * @param  array $array  array params.
 * @return array        sanitized array
 */
function wahforms_sanitize_array( $array ) {
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $item_key => $item_val ) {
				$item_val = sanitize_text_field( $item_val );
			}
		} else {
			if ( 'email_body' === $key ) {
				$value = wp_kses_post( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}
	}
	return $array;
}
/**
 * WAHForms admin page contents
 */
function wah_forms_admin_page_contents() {
	$wf_settings_submit = isset( $_POST['wf_settings_submit'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_settings_submit'] ) ) : '';

	$wf_lead_date_format         = isset( $_POST['wahforms_lead_date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['wahforms_lead_date_format'] ) ) : '';
	$wf_validation_before_submit = isset( $_POST['wahforms_validation_before_submit'] ) ? sanitize_text_field( wp_unslash( $_POST['wahforms_validation_before_submit'] ) ) : '';

	if ( $wf_settings_submit && 'Y' === $wf_settings_submit ) {
		wahforms_save_option( 'wahforms_lead_date_format', $wf_lead_date_format );
		wahforms_save_option( 'wahforms_validation_before_submit', $wf_validation_before_submit ? true : false );
	} else {
		$wf_lead_date_format         = wahforms_get_option( 'wahforms_lead_date_format' );
		$wf_validation_before_submit = wahforms_get_option( 'wahforms_validation_before_submit' );
	}
	?>
	<h1>
		<span class="dashicons dashicons-schedule"></span> <?php esc_html_e( 'WAH Forms settings.', 'wah-forms' ); ?>
	</h1>
	<div class="wah-forms-settings-dashboard">
		<div class="dashboard-header">
			<?php esc_html_e( 'General settings', 'wah-forms' ); ?>
		</div>
		<div class="dashboard-options">
			<h3>How it works?</h3>
			<ol>
				<li>WAH Forms create a custom post type <span class="dashicons dashicons-feedback"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wah_forms' ) ); ?>">WAH Forms</a></li>
				<li>Each post type create a custom fully customizable contact form.</li>
				<li>You can render WAH Forms with shortcode functionality.</li>
				<li>Form submittion runs with AJAX and save submitted data to WAH Forms <span class="dashicons dashicons-groups"></span> Leads.</li>
			</ol>
			<hr>

			<form action="" method="post">
				<input type="hidden" name="wf_settings_submit" value="Y">
				<div class="option-controls">
					<input type="checkbox" name="wahforms_validation_before_submit" id="wahforms_validation_before_submit"
						<?php echo $wf_validation_before_submit ? 'checked' : ''; ?>>
					<label for="wahforms_validation_before_submit">
						<?php esc_html_e( 'Enable WAHForms fields validation before submit?', 'wah-forms' ); ?>
					</label>
				</div>
				<div class="option-controls">
					<label for="wahforms_lead_date_format">
						<?php esc_html_e( 'Select date format for "Leads" screen:', 'wah-forms' ); ?>
					</label>
					<select name="wahforms_lead_date_format" id="wahforms_lead_date_format">
						<option value="ymd" <?php echo selected( $wf_lead_date_format, 'ymd' ); ?>>Y-m-d H:i:s</option>
						<option value="dmy" <?php echo selected( $wf_lead_date_format, 'dmy' ); ?>>d/m/Y H:i:s</option>
					</select>
				</div>
				<input type="submit" class="button button-primary button-large" value="<?php esc_html_e( 'Save WAHForms settings', 'wah-forms' ); ?>">
			</form>
		</div>
	</div>
	<?php
}
/**
 * WAHForms builder HTML
 */
function wahforms_builder() {
	global $post;
	$post_id = isset( $post->ID ) ? esc_html( $post->ID ) : '';

	if ( $post_id ) {
		// update_post_meta( $post_id, 'wahform_fields', array() );
		$wahform_fields = get_post_meta( $post_id, 'wahform_fields', true );

		$wahform_settings = isset( $wahform_fields['settings'] ) ? $wahform_fields['settings'] : array();
		// print_r( $wahform_fields );
	}
	?>
	<div id="wahforms-builder-metabox" data-id="<?php echo esc_html( $post_id ); ?>">

		<div class="settings-item contact-form-shortcode">
			<div class="left-column">
				<p><strong><?php echo esc_html_e( 'WAHForms contact form shortcode:', 'wah-forms' ); ?></strong></p>
				<input type="text" class="copy-wahforms-shortcode" readonly
					value='[wahforms id="<?php echo esc_html( $post_id ); ?>"]'>
				<p><small>**<?php esc_html_e( 'Important notice! The "name" attribute for each field should be unique', 'wah-forms' ); ?></small></p>
			</div>
			<div class="right-column">
				<div class="submit-wahforms-wrapper">
					<button type="submit" id="submit-wahforms" class="button button-primary button-large">
						<span class="dashicons dashicons-cloud-saved"></span>
						Save form
					</button>
				</div>
			</div>
		</div>

		<div id="wahforms-admin-tabs">

			<ul>
				<?php echo wp_kses_post( wahforms_render_admin_tabs() ); ?>
			</ul>

			<div id="wahforms-tab-1" class="form-fields-tab-content">
				<?php wahforms_render_admin_fields_builder( $wahform_fields ); ?>
			</div>

			<div id="wahforms-tab-2" class="forms-settings-tab-content">
				<?php
					$allowed_html = wahforms_admin_settings_get_allowed_html();
					echo wp_kses( wahforms_render_admin_form_settings( $post_id, $wahform_settings ), $allowed_html );
				?>
			</div>

			<div id="wahforms-tab-3" class="email-body-tab-content">
				<p><strong>Click on input tag (and it will be copied):</strong></p>
				<?php
				if ( isset( $wahform_fields['fields'] ) && $wahform_fields['fields'] ) :
					echo '<ul class="wahforms-email-body-tags-list">';
					foreach ( $wahform_fields['fields'] as $wahform_field ) :
						?>
						<li data-index="<?php echo esc_html( $wahform_field['hidden_index'] ); ?>">
							<?php echo esc_html( $wahform_field['input_name'] ); ?>:
							<code>[wfinput type="<?php echo esc_html( $wahform_field['field_type'] ); ?>" index="<?php echo esc_html( $wahform_field['hidden_index'] ); ?>"]</code>
						</li>
						<?php
					endforeach;
					echo '</ul>';
				endif;
				?>
				<p><strong>Paste input tag inside this area.</strong></p>
				<p><em>Row text or HTML code are acceptable. Please do NOT insert PHP or JavaScript code, it will be removed.</em></p>
				<p>
					<textarea id="wahforms-email-body" name="wahform_fields[settings][email_body]" rows="8" cols="80"><?php echo isset( $wahform_settings['email_body'] ) ? wahforms_sanitize_email_body( $wahform_settings['email_body'] ) : ''; ?></textarea>
				</p>
				<div class="additional-links">
					<a href="#" class="button">How to use hidden mail tags</a>
					<div class="additional-links-content" style="display:none;">
						<ol>
							<li>
								<code>[wfinput type="submit_date"]</code> - <?php esc_html_e( 'adding form submitted date & hours', 'wah-forms' ); ?>
							</li>
							<li>
								<code>[wfinput type="submit_url"]</code> - <?php esc_html_e( 'adding page url to the form', 'wah-forms' ); ?>
							</li>
						</ol>
					</div>
				</div>
			</div>

			<div id="wahforms-tab-4" class="leads-tab-content">
				<h3><?php esc_html_e( 'Saved leads', 'wah-forms' ); ?></h3>
				<?php
					$leads = wahforms_get_leads_by_id( $post_id );
				?>
				<?php if ( count( $leads ) >= 1 ) : ?>
					<p><?php esc_html_e( 'Total leads:', 'wah-forms' ); ?> <?php echo count( $leads ); ?></p>
					<div class="wahforms-leads-container">
						<p>
							<button type="button" class="wahform-btn show-all-leads"
								data-wahformid="<?php echo esc_html( $post_id ); ?>"
								data-paged="1">
								<span class="dashicons dashicons-list-view"></span> <?php echo esc_html__( 'Show leads', 'wah-forms' ); ?>
							</button>
						</p>
						<div class="wahforms-leads-wrapper"></div>
						<div class="wahforms-leads-load-more">
							<button class="button load-more-leads" data-wahformid="<?php echo esc_html( $post_id ); ?>">
								<span class="dashicons dashicons-update"></span>
								<?php echo esc_html__( 'Load more', 'wah-forms' ); ?>
							</button>
						</div>
					</div>
				<?php else : ?>
					<?php esc_html_e( 'There is no leads yet.', 'wah-forms' ); ?>
				<?php endif; ?>
			</div>

			<div id="wahforms-tab-5" class="additional-settings-tab-content">
				<ol>
					<li>Action "<code>wahforms_append_fields</code>" - append inputs to form. Passing form ID.</li>
					<li>Filter "<code>wahforms_render_admin_tabs_html</code>" - return WAHForms admin tabs</li>
				</ol>
			</div>
		</div>

	</div>

	<?php
}
/**
 * WAHForms sanitize_email_body
 *
 * @param  string $email_body_content text/html.
 */
function wahforms_sanitize_email_body( $email_body_content ) {
	if ( $email_body_content ) {
		echo wp_kses_post( $email_body_content );
	} else {
		echo '';
	}
}
/**
 * WAHForms_render_admin_tabs
 */
function wahforms_render_admin_tabs() {
	ob_start();
	?>
		<li>
			<a href="#wahforms-tab-1">
				<span class="dashicons dashicons-menu"></span>
				<?php esc_html_e( 'Form fields', 'wah-forms' ); ?>
			</a>
		</li>
		<li>
			<a href="#wahforms-tab-2">
				<span class="dashicons dashicons-admin-settings"></span>
				<?php esc_html_e( 'Form settings', 'wah-forms' ); ?>
			</a>
		</li>
		<li>
			<a href="#wahforms-tab-3">
				<span class="dashicons dashicons-email-alt"></span>
				<?php esc_html_e( 'Email body', 'wah-forms' ); ?>
			</a>
		</li>
		<li>
			<a href="#wahforms-tab-4">
				<span class="dashicons dashicons-groups"></span>
				<?php esc_html_e( 'Leads', 'wah-forms' ); ?>
			</a>
		</li>
		<li>
			<a href="#wahforms-tab-5">
				<span class="dashicons dashicons-screenoptions"></span>
				<?php esc_html_e( 'Additional settings', 'wah-forms' ); ?>
			</a>
		</li>
	<?php
	$html = ob_get_clean();
	return apply_filters( 'wahforms_render_admin_tabs_html', $html );
}
/**
 * WAHForms get_leads_by_id
 *
 * @param  string  $form_id form id.
 * @param integer $paged page number.
 * @return array          [description]
 */
function wahforms_get_leads_by_id( $form_id, $paged = 0 ) {
	global $post;
	$args = array(
		'post_type'  => 'wahforms_lead',
		'meta_key'   => 'form_id', // phpcs:ignore
		'meta_value' => $form_id,	// phpcs:ignore
	);
	if ( $paged ) {
		$args['paged']          = $paged;
		$args['posts_per_page'] = 10;
	} else {
		$args['posts_per_page'] = -1;
	}
	$leads_results = array();
	$leads         = new WP_Query( $args );
	if ( $leads->have_posts() ) {
		while ( $leads->have_posts() ) :
			$leads->the_post();
			$leads_results[] = array(
				'post'             => $post,
				'lead_data'        => get_post_meta( $post->ID, 'lead_data', true ),
				'lead_attachments' => get_post_meta( $post->ID, 'lead_attachments', true ),
			);
			endwhile;
		wp_reset_postdata();
	}
	return $leads_results;
}
/**
 * WAHForms render_admin_fields_builder
 *
 * @param  array $wahform_fields wahforms params.
 */
function wahforms_render_admin_fields_builder( $wahform_fields ) {
	$allowed_html = wahforms_admin_settings_get_allowed_html(); // helpers.php.
	$field_types  = wahforms_get_field_types();                 // helpers.php.
	ob_start();
	?>
	<div class="wahforms-builder-wrapper">
		<div class="wahforms-builder-sidebar">
			<h3 class="sidebar-title"><?php esc_html_e( 'Drag the field to the right', 'wah-forms' ); ?></h3>
			<ul id="wahforms-builder-items">
			<?php foreach ( $field_types as $item_type ) : ?>
					<li class="draggable-item">
						<?php echo wp_kses_post( wahforms_render_sidebar_builder_item( $item_type ) ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="wahforms-builder-form-fields">
			<div class="wahforms-builder-form-fields-inner" id="wahforms-builder-content">
				<ul id="sortable-parent">
				<?php
				if ( $wahform_fields ) {
					echo wp_kses( wahforms_render_saved_fields( $wahform_fields ), $allowed_html );
				}
				?>
				</ul>
			</div>
		</div>

	</div>
		<?php
		echo wp_kses( ob_get_clean(), $allowed_html );
}
/**
 * WAHForms render admin form settings
 *
 * @param  string $post_id                        post id.
 * @param  array  $wahform_settings               form settings array.
 * @return string                   form settings html
 */
function wahforms_render_admin_form_settings( $post_id, $wahform_settings ) {
	ob_start();
	?>
	<div class="wahforms-shortcode">
		<?php if ( $post_id ) : ?>
			<div class="wahforms-settings-row">
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Mail to:', 'wah-forms' ); ?></p>
					<p class="description"><small><?php echo esc_html_e( 'For example: mymail@mail.com', 'wah-forms' ); ?></small></p>
					<input type="email" name="wahform_fields[settings][mailto]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['mailto'] ) : ''; ?>" required>
				</div>
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Subject:', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'HTML not allowed', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][subject]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['subject'] ) : ''; ?>">
				</div>
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Mail from "Sender name":', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'For example: WAH Contact form', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][mailfrom_sender_name]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['mailfrom_sender_name'] ) : ''; ?>" required>
				</div>
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Mail from "Sender email":', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'For example: contact@mail.com', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][mailfrom_sender_email]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['mailfrom_sender_email'] ) : ''; ?>" required>
				</div>
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Submit button title:', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'HTML not allowed', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][submit_title]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['submit_title'] ) : ''; ?>">
				</div>
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Submit button class:', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'HTML not allowed, class without dot (.)', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][submit_class]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['submit_class'] ) : ''; ?>">
				</div>

				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Email sent message:', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'HTML not allowed', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][mail_sent_message]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['mail_sent_message'] ) : ''; ?>">
				</div>
				<div class="settings-item">
					<p class="no-margin"><?php echo esc_html_e( 'Email error message', 'wah-forms' ); ?></p>
					<p class="description">
						<small><?php echo esc_html_e( 'HTML not allowed', 'wah-forms' ); ?></small>
					</p>
					<input type="text" name="wahform_fields[settings][mail_error_message]"
						value="<?php echo $wahform_settings ? esc_html( $wahform_settings['mail_error_message'] ) : ''; ?>">
				</div>
			</div>
		<?php else : ?>
			<p><?php echo esc_html_e( 'Before creating the form, please save the post.', 'wah-forms' ); ?></p>
		<?php endif; ?>
	</div>
		<?php
		return ob_get_clean();
}
/**
 * Render saved form fields
 *
 * @param  array $wahform_fields  fields array.
 */
function wahforms_render_saved_fields( $wahform_fields ) {
	$html = '';
	if ( $wahform_fields && isset( $wahform_fields['fields'] ) && is_array( $wahform_fields['fields'] ) ) {
		ob_start();
		foreach ( $wahform_fields['fields'] as $key => $item ) {
			$field_type = isset( $item['field_type'] ) ? sanitize_text_field( $item['field_type'] ) : '';
			wahforms_render_admin_init_field_item( $field_type, $key, $item );
		}
		$html = ob_get_clean();
		return $html;
	}
}
/**
 * Render builder item
 *
 * @param  string $type  field type.
 */
function wahforms_render_sidebar_builder_item( $type ) {
	if ( 'text' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Text', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'tel' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Phone', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'number' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Number', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'textarea' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Textarea', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'selectbox' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Selectbox', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'checkbox' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Checkbox', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'radio' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Radio buttons', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'checkboxes' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Checkboxes', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'hidden' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Hidden field', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'url' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'URL', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'email' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'Email', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	} elseif ( 'file' === $type ) {
		?>
		<div class="wahforms-builder-item" data-field="<?php echo esc_html( $type ); ?>">
			<div class="title">
			<?php esc_html_e( 'File', 'wah-forms' ); ?>
				<button class="add-item" type="button" disabled>
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			<div class="wahforms-builder-item-options"></div>
		</div>
			<?php
	}

}
