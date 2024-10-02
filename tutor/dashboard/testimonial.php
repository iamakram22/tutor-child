<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
/**
 * enqueue bootstrap
 */
wp_enqueue_style('bootstrap-style');
wp_enqueue_script('bootstrap-script');
wp_enqueue_style('dashboard-testimonials-style');
wp_enqueue_script('testimonials-script');

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
?>
<div class="container dashboard_form">
	<form type="post" id="testimonial_form">
		<?php wp_nonce_field('testimonial_form', 'testimonial_form_nonce'); ?>

		<div class="input-group mb-3">
			<label class="input-group-text col-4 align-items-start" for="testimonial_content">Testimonial Content :</label>
			<textarea id="testimonial_content" name="testimonial_content" class="form-control col-8" rows="4" required></textarea>
		</div>

		<div class="input-group mb-3">
			<label class="input-group-text col-4" for="testimonial_name">Name :</label>
			<input type="text" id="testimonial_name" name="testimonial_name" class="form-control col-8" required>
		</div>

		<div class="input-group mb-3">
			<label class="input-group-text col-4" for="testimonial_other">Designation :</label>
			<input type="text" id="testimonial_other" name="testimonial_other" class="form-control col-8">
		</div>

		<button type="submit" id="testimonial_submit" class="btn btn-primary">Save testimonial</button>
		<button id="testimonial_clear" class="btn btn-outline-secondary" title="Clear testimonial cache if you do not see the changes">Clear Cache</button>
		<span id="result"></span>
	</form>
</div>

<div id="testimonials-list" class="container mt-5">
</div>