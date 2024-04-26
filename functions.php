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
    'pin' => 'Area pin code'
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

function add_export_users_menu_item() {
    add_submenu_page(
        'users.php',
        'Export Users',
        'Export Users',
        'manage_options',
        'export_users_page',
        'export_users_page_callback'
    );
}
add_action('admin_menu', 'add_export_users_menu_item');

function export_users_page_callback() {
    // Your HTML and form elements for the export page
    echo '<div class="wrap">';
    echo '<h2>Export Users</h2>';
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="export_users" value="true" />';
    echo '<input type="submit" value="Export Users" class="button-primary" />';
    echo '</form>';
    echo '</div>';
}

function handle_export_users() {
    if (isset($_POST['export_users']) && $_POST['export_users'] === 'true') {
        // Query all users
		$args = array(
            'role'         => 'subscriber',
            'number'       => -1,
        );
        $users = get_users($args);

		$csv_data = "ID,Username,Display Name,Email,Enrolled Courses,";
		foreach (CUSTOM_FIELDS as $key => $value) {
			$csv_data .= $value . ',';
		}
        // Prepare CSV data
        $csv_data .= "\n";
        foreach ($users as $user) {
			$csv_data .= "{$user->ID},{$user->user_login},{$user->display_name},{$user->user_email},";

			$enrolled_courses = array();
			if ( function_exists('tutor_utils') ) {
				$enrolled_courses = tutor_utils()->get_enrolled_courses_ids_by_user($user->ID);
				if(!empty($enrolled_courses) && is_array($enrolled_courses)) {
					foreach($enrolled_courses as $key => $course_id) {
						$enrolled_courses[$key] = get_the_title($course_id);
					}
					$enrolled_courses = implode('; ', $enrolled_courses);
				} else {
					$enrolled_courses = '-';
				}
            } else {
				$enrolled_courses = '-';
			}
			
			$csv_data .= "$enrolled_courses,";
            // You can add more user meta as needed
			foreach(CUSTOM_FIELDS as $key => $value) {
				$userdata = get_user_meta($user->ID, '_' . $key, true);
				$userdata = str_replace(',', ' ', $userdata);
				$csv_data .= $userdata . ',';
			}
			$csv_data .= "\n";
        }

        // Output CSV file
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=users-" . time() . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
		// echo '<pre>' . print_r($csv_data, true) . '</pre>';
        echo $csv_data;
        exit;
    }
}
add_action('admin_init', 'handle_export_users');

function prepopulate_billing_fields($fields) {
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