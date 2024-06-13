<?php
/**
 * Constant for handling site setting form
 */
define('CLIENT', unserialize(get_option('client_data', true)));
define('CUSTOM_FIELDS', array(
    'salutation' => 'Salutation',
    'phone_no' => 'Phone Number',
    'gender' => 'Gender',
    'dob' => 'Date of Birth',
    'course' => 'Course',
    'marital_status' => 'Marital Status',
    'father' => 'Father\'s Name',
    'mother' => 'Mother\'s Name',
    'religion' => 'Religion',
    'category' => 'Category',
    'domicile_state' => 'Domicile State',
    'domicile_district' => 'Domicile district',
    'state_const' => 'State constituency',
    'aadhar' => 'Aadhar number',
    'address' => 'Permanent address',
    'pin' => 'Area pin code',
	'reference' => 'IIVA Reference Person Name'
));

/**
 * Actions & Filters
 */
add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);
add_shortcode('franchise_branding', 'franchise_branding');
add_shortcode('franchise_contact', 'franchise_contact');
add_action('wp_ajax_client_editing_website', 'client_editing_website_function');
add_filter('woocommerce_checkout_fields', 'prepopulate_billing_fields');

/**
 * Enqueue styles & scripts
 *
 * @return void
 */
function child_enqueue_styles()
{
	wp_register_style('bootstrap-style', get_stylesheet_directory_uri() . '/vendor/bootstrap.min.css', array(), time(), 'all');
	wp_register_script('bootstrap-script', get_stylesheet_directory_uri() . '/vendor/bootstrap.min.js', array('jquery'), time(), true);
	wp_enqueue_style('tutor-child', get_stylesheet_directory_uri() . '/style.css', array(), time(), 'all');

	wp_enqueue_media(); // for accessing WP media
	wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'), time(), true);
	wp_localize_script(
		'custom-script',
		'myAjax',
		array(
			'ajax_url' => admin_url('admin-ajax.php')
		)
	);
}

/**
 * Include Tutor LMS functions
 */
include('tutor-lms.php');
include('tutor-registration.php');

/**
 * Handle Site setting form request
 *
 * @return void
 */
function client_editing_website_function()
{
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'client_editing')) {
		$nonce_msg = __('nonce verification failed', 'tutor');
		wp_send_json_error($nonce_msg);
	}

	if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'client_editing')) {
		$data = $_POST['data'];

		if (!empty($data)) {
			$data = serialize($data);
			update_option('client_data', $data);
			$msg = __('Successful, please refresh to see changes', 'tutor');
			wp_send_json_success($msg, 200);
		} else {
			$error = __('No data received', 'tutor');
			wp_send_json_error($error);
		}
	}

	// Exit AJAX
	wp_die();
}

/**
 * Output franchise branding data
 *
 * @param array $atts
 * @return string
 */
function franchise_branding($atts)
{
	$logo = CLIENT['client_logo'];
	$name = CLIENT['client_name'];
	$desc = CLIENT['client_desc'];

	$atts = shortcode_atts(
		array(
			'color' => 'var(--link-color)',
			'desc' => false,
			'bg' => 'light'
		),
		$atts
	);

	ob_start();
	?>
	<a href="<?php echo home_url(); ?>" style="color: <?php echo esc_html($atts['color']); ?>">
		<div class="franchise-branding">
			<img src="<?php echo esc_html($logo) ?>" id="franchise_logo">
			<span id="frachise_name">
				<?php echo esc_html($name) ?>
			</span>
		</div>
	</a>
	<?php
	if ($atts['desc']) {
		?>
		<div class="site_desc" style="color: <?php echo $atts['bg'] === 'light' ? '#000000' : '#FFFFFF'; ?>;">
			<?php echo $desc; ?>
		</div>
		<?php
	}
	$html = ob_get_clean();
	return $html;
}

/**
 * Output franchise contact details
 *
 * @param array $atts
 * @return string
 */
function franchise_contact($atts)
{
	$atts = shortcode_atts(
		array(
			'light' => false,
		),
		$atts
	);

	$phone = CLIENT['client_phone'];
	$email = CLIENT['client_email'];
	$address = CLIENT['client_address'];

	$color = $atts['light'] ? '#FFFFFF' : '#000000';

	ob_start();

	if ($phone): ?>
		<div class="contact-detail-wrap">
			<a href="tel:<?php echo esc_html($phone); ?>" style="color : <?php echo $color; ?>">
				<i class="fas fa-phone-alt"></i>
				<span class="contact-detail">
					<?php echo esc_html($phone); ?>
				</span>
			</a>
		</div>
	<?php endif;

	if ($email): ?>
		<div class="contact-detail-wrap">
			<a href="mailto:<?php echo esc_html($email); ?>" style="color : <?php echo $color; ?>">
				<i class="fas fa-envelope"></i>
				<span class="contact-detail">
					<?php echo esc_html($email); ?>
				</span>
			</a>
		</div>
	<?php endif;

	if ($address): ?>
		<div class="contact-detail-wrap" style="color : <?php echo $color; ?>">
			<i class="fas fa-map-marker-alt"></i>
			<span class="contact-detail">
				<?php echo esc_html($address); ?>
			</span>
		</div>
	<?php endif;

	$contacts = ob_get_clean();
	return $contacts;
}

/**
 * Include tutor access capabilities
 */
include 'access.php';

/**
 * Include export report
 */
include 'export-report.php';

/**
 * Set default details for checkout fields from profile fields
 *
 * @param array $fields
 * @return array
 */
function prepopulate_billing_fields($fields)
{
    // Get the current user ID
    $user_id = get_current_user_id();

	if($user_id) {
		// Get user meta
		$user_info = get_user_meta($user_id);

		// Set default user details
		$fields['billing']['billing_first_name']['default'] = $user_info['first_name'][0];
		$fields['billing']['billing_last_name']['default'] = $user_info['last_name'][0];
		$fields['billing']['billing_phone']['default'] = $user_info['_phone_no'][0];
	}

    return $fields;
}