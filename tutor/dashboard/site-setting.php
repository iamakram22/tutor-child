<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
/**
 * enqueue bootstrap
 */
wp_enqueue_style('bootstrap');

/**
 * Show only if Tutor
 */
if (!current_user_can('tutor_instructor')) { ?>
	<div class="alert alert-danger container" role="alert">
		Access denied. You do not have permission to access this page <a href="<?php echo home_url(); ?>"
			class="alert-link">Go to Homepage</a>.
	</div>
	<?php exit;
}

if (!CLIENT) {
	define('CLIENT', unserialize(get_option('client_data', true)));
}

$logo = CLIENT['client_logo'];
$name = CLIENT['client_name'];
$phone = CLIENT['client_phone'];
$email = CLIENT['client_email'];
$address = CLIENT['client_address'];
$desc = CLIENT['client_desc'];
ob_start();
?>
<div class="container client_form">
	<form type="post" id="site_editing_form" enctype="multipart/form-data">
		<?php wp_nonce_field('client_editing', 'client_editing_nonce'); ?>
		<div class="input-group mb-3">
			<label class="input-group-text col-8 gap-1" for="open_media_gallery">Website Logo : <span id="img_name"
					class="text-truncate"></span></label>
			<input type="hidden" id="client_logo" name="selected_image_url" value="<?php echo esc_html($logo) ?>"
				readonly>
			<button id="open_media_gallery" class="btn btn-primary form-control col-4">Upload Logo</button>
		</div>

		<div class="input-group mb-3">
			<label class="input-group-text col-4" for="client_name">Website Name :</label>
			<input type="text" id="client_name" name="client_name" class="form-control col-8"
				value="<?php echo esc_html($name) ?>">
		</div>

		<div class="input-group mb-3">
			<label class="input-group-text col-4" for="client_phone">Your Phone Number :</label>
			<input type="tel" id="client_phone" name="client_phone" class="form-control col-8"
				value="<?php echo esc_html($phone) ?>">
		</div>

		<div class="input-group mb-3">
			<label class="input-group-text col-4" for="client_email">Your Email Address :</label>
			<input type="email" id="client_email" name="client_email" class="form-control col-8"
				value="<?php echo esc_html($email) ?>">
		</div>

		<div class="input-group mb-3">
			<label class="input-group-text col-4 align-items-start" for="client_address">Your Address :</label>
			<textarea id="client_address" name="client_address" class="form-control col-8"
				rows="2"><?php echo esc_html($address) ?></textarea>
		</div>

		<div class="input-group mb-0">
			<label class="input-group-text col-4 align-items-start" for="client_desc">Site description :</label>
			<textarea id="client_desc" name="client_desc" class="form-control col-8"
				rows="2"><?php echo esc_html($desc) ?></textarea>
		</div>
		<div class="form-text mb-3">This will be displayed under footer logo</div>

		<button type="submit" id="settings-submit" class="btn btn-primary">Save changes</button>
		<span id="result"></span>
	</form>
</div>