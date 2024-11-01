<?php
/**
 * WAHForms Lead item
 *
 * @package WAHForms
 */

$lead_post        = isset( $lead['post'] ) ? $lead['post'] : array();
$lead_data        = isset( $lead['lead_data'] ) ? $lead['lead_data'] : array();
$lead_attachments = isset( $lead['lead_attachments'] ) ? $lead['lead_attachments'] : array();

$lead_files_to_find = array();
for ( $x = 1; $x <= 30; $x++ ) {
	$lead_files_to_find[] = 'file_hidden_index_' . $x;
}
?>
<li class="lead-item-preview" data-id="<?php echo esc_html( $lead_post->ID ); ?>">
	<div class="preview-inner">
		<div>
			<strong>#<?php echo esc_html( $lead_post->ID ); ?></strong> - <?php echo esc_html( get_the_title( $lead_post->ID ) ); ?>
		</div>
		<button type="button" class="button-secondary lead-details">
			<span class="dashicons dashicons-info-outline"></span> <?php esc_html_e( 'Details', 'wah-forms' ); ?>
		</button>
	</div>
	<div class="list-inner" style="display:none;">
		<div class="lead-option lead_input_name">
			<?php echo esc_html_e( 'Published:', 'wah-forms' ); ?>
		</div>
		<div class="lead-option lead_input_value">
			<?php echo esc_html( get_the_date( $date_format, $lead_post->ID ) ); ?>
			<br>[<?php echo esc_html( get_the_date( 'time', $lead_post->ID ) ); ?>]
		</div>
		<?php foreach ( $lead_data as $key => $lead_item ) : ?>
			<?php if ( ! in_array( $key, $lead_files_to_find, true ) ) : ?>
				<div class="lead-option lead_input_name">
					<?php echo esc_html( $key ); ?>:
				</div>
				<div class="lead-option lead_input_value">
					<?php echo esc_html( $lead_item ); ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php
		if ( $lead_attachments ) :
			foreach ( $lead_attachments as $attachment_id => $attachment_url ) :
				if ( $attachment_id ) :
					$att_title = get_the_title( $attachment_id );
					$att_type  = get_post_mime_type( $attachment_id );
					if ( ! $att_type ) {
						$download     = '';
						$download_url = '#';
						$att_type     = __( 'Unknown', 'wah-forms' );
						$disabled     = 'disabled';
					} else {
						$download     = 'download';
						$download_url = $attachment_url;
						$disabled     = '';
					}
					if ( ! $att_title ) {
						$att_title = __( 'File deleted from Media', 'wah-forms' );
					}
					$icon = '<span class="dashicons dashicons-download"></span>';
					if ( $att_type ) {
						if ( 'application/pdf' === $att_type ) {
							$icon = '<span class="dashicons dashicons-pdf"></span>';
						}
					}
					?>
					<div class="lead-option lead_input_name">
						<?php echo esc_html( $att_title ); ?> [<?php echo esc_html( $att_type ); ?>]
					</div>
					<div class="lead-option lead_input_value lead_attachment_value">
						<a href="<?php echo esc_url( $download_url ); ?>" title="<?php esc_html_e( 'Download attachment', 'wah-forms' ); ?>" class="button download-lead-attachment" <?php echo esc_html( $download ); ?> <?php echo esc_html( $disabled ); ?>>
							<?php echo wp_kses_post( $icon ); ?>
							<?php esc_html_e( 'Download', 'wah-forms' ); ?>
						</a>
					</div>
					<?php
				endif;
			endforeach;
		endif;
		?>
	</div>
</li>
