<?php
/**
 * Constant for handling site setting form
 */
define('CLIENT', unserialize(get_option('client_data', true)));

/**
 * Actions & Filters
 */
add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);
add_shortcode('franchise_branding', 'franchise_branding');
add_shortcode('franchise_contact', 'franchise_contact');
add_shortcode('franchise_testimonials', 'franchise_testimonials');
add_action('wp_ajax_client_editing_website', 'client_editing_website_from_dashboard');
add_action('wp_ajax_create_testimonial', 'create_testimonial_from_dashboard');
add_action('wp_ajax_load_testimonials', 'load_testimonials');
add_action('wp_ajax_delete_testimonial', 'delete_testimonial');
add_filter('woocommerce_checkout_fields', 'prepopulate_billing_fields');
add_action('init', 'register_custom_post_types');
add_action('wp_footer', 'load_bootstrap_homepage_testimonials');
add_action('wp_ajax_load_franchise_testimonials', 'load_franchise_testimonials');
add_action('wp_ajax_nopriv_load_franchise_testimonials', 'load_franchise_testimonials');
add_action('wp_ajax_clear_testimonial_transient', 'clear_testimonial_transient');

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
			'ajax_url' => admin_url('admin-ajax.php'),
			'spinner' => includes_url('images/spinner.gif')
		)
	);
	
	// Dashboard Testimonials
	wp_register_style('dashboard-testimonials-style', get_stylesheet_directory_uri() . '/assets/css/dashboard-testimonials.css', array('tutor-child'), time(), 'all');
	wp_register_script('testimonials-script', get_stylesheet_directory_uri() . '/assets/js/testimonials.js', array('jquery', 'custom-script'), time(), true);
}

/**
 * Include Tutor LMS functions
 */
include('tutor-lms.php');

/**
 * Register custom post types
 *
 * @return void
 */
function register_custom_post_types() {
	register_post_type('testimonial',
        array(
            'labels'      => array(
                'name'          => __('Testimonials'),
                'singular_name' => __('Testimonial'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'revisions'),
			'rewrite' => array('slug' => 'testimonial'),
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-star-empty',
        )
    );
}

/**
 * Handle Site setting form request
 *
 * @return void
 */
function client_editing_website_from_dashboard(): void
{
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'client_editing')) {
		$nonce_msg = __('nonce verification failed', 'tutor');
		wp_send_json_error($nonce_msg);
	}

	if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'client_editing')) {
		$data = $_POST['data'];

		if (!empty($data)) {
			$data['last_update'] = current_time('mysql');
			update_option('blogname', $data['client_name']);
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
 * Create testimonial post
 *
 * @return mixed
 */
function create_testimonial_from_dashboard() : void
{
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'testimonial_form')) {
		$nonce_msg = __('nonce verification failed', 'tutor');
		wp_send_json_error($nonce_msg);
	}

	if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'testimonial_form')) {
		$data = $_POST['data'];

		if(!empty($data)) {
			$testimonial_content = $data['testimonial_content'];
			$testimonial_name = $data['testimonial_name'];
			$testimonial_other = $data['testimonial_other'];
			// $testimonial_content .= '<div class="testimonial_other">'. $testimonial_other . '</div>';

			$testimonial_post = array(
				'post_title' => $testimonial_name,
				'post_content' => $testimonial_content,
				'post_status' => 'publish',
				'post_type' => 'testimonial'
			);

			$create_post = wp_insert_post($testimonial_post);

			if ($create_post) {
				// Save the custom fields (name, other information)
                update_post_meta($create_post, 'testimonial_name', $testimonial_name);
                update_post_meta($create_post, 'testimonial_other', $testimonial_other);
				delete_transient('testimonial_carousel_cache');

				$msg = __('Testimonial added sucessfully','tutor');
				wp_send_json_success($msg);
			}
		} else {
			$error = __('No data received', 'tutor');
			wp_send_json_error($error);
		}
	}

	// Exit AJAX
	wp_die();
}

function load_testimonials(): void {
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $args = array(
        'post_type'      => 'testimonial',
        'posts_per_page' => 5,
        'paged'          => $paged,
    );
    $testimonial_query = new WP_Query($args);

    if ($testimonial_query->have_posts()) {
        $output = '';
		$output .= '<div id="message"></div>';
        while ($testimonial_query->have_posts()) {
            $testimonial_query->the_post();
			$id = get_the_ID();
			// Retrieve the custom fields
			$testimonial_name = get_post_meta($id, 'testimonial_name', true);
			$testimonial_other = get_post_meta($id, 'testimonial_other', true);

			$output .= '<div class="testimonial mb-2"><div class="content-wrap">';
				$output .= '<div class="content">' . get_the_content() . '</div>';
					$output .= '<div class="name"><strong>'. $testimonial_name .'</strong></div>';
					$output .= '<div class="other"><em>'. $testimonial_other .'</em></div>';
				$output .= '</div>';
				$output .= '<div class="cross" data-post_id="'. $id .'" title="Delete this testimonial"><i class="fas fa-times"></i></div>';
            $output .= '</div>';
        }

        // Add pagination
        $max_pages = $testimonial_query->max_num_pages;
		if($max_pages > 1) {
			$output .= '<div class="pagination pt-2">';
			
			// Previous button
			if ($paged > 1) {
				$output .= '<li class="page-item">
					<button class="page-link" data-page="'. ($paged - 1) .'">&laquo; Previous</button>
				</li>';
			} else {
				$output .= '<li class="page-item disabled">
					<span class="page-link">&laquo; Previous</span>
				</li>';
			}

			// Page numbers
			for ($i = 1; $i <= $max_pages; $i++) {
				$active = $i == $paged ? 'active' : '';
				$output .= '<li class="page-item ' . $active . '">
					<button class="page-link" data-page="'. $i .'">'. $i .'</button>
				</li>';
			}

			// Next button
			if ($paged < $max_pages) {
				$output .= '<li class="page-item">
					<button class="page-link" data-page="'. ($paged + 1) .'">Next &raquo;</button>
				</li>';
			} else {
				$output .= '<li class="page-item disabled">
					<span class="page-link">Next &raquo;</span>
				</li>';
			}
		}

        $output .= '</div>';

        wp_reset_postdata();
        wp_send_json_success($output);
    } else {
        wp_send_json_error('No testimonials found.');
    }

    wp_die();
}

function delete_testimonial(): void {
    // Check if the ID was sent in the request
    if (isset($_POST['id'])) {
        $post_id = intval($_POST['id']);

        // Attempt to delete the post
        $deleted = wp_delete_post($post_id, true);

        if ($deleted) {
			delete_transient('testimonial_carousel_cache');
            wp_send_json_success('Testimonial deleted successfully.');
        } else {
            wp_send_json_error('Failed to delete testimonial. Please try again.');
        }
    } else {
        wp_send_json_error('No testimonial ID received.');
    }

    wp_die();
}

/**
 * clear testimonial transient
 * @return void
 */
function clear_testimonial_transient(): void {
	delete_transient('testimonial_carousel_cache');
	wp_send_json_success('Cache cleared!');
	wp_die();
}

/**
 * Load bootstrap
 * @return void
 */
function load_bootstrap_homepage_testimonials(): void {
	if(is_front_page()) {
		wp_enqueue_style('bootstrap-style');
		wp_enqueue_script('bootstrap-script');
	}
}

/**
 * Display testimonials of franchise
 *
 * @param array $atts
 * @return string
 */
function franchise_testimonials($atts): bool|string {
	$atts = shortcode_atts(array(
		'limit' => 10,
		'indicators' => true,
		'show_title' => false
	), $atts);

    // Query to get the testimonials
    $args = array(
        'post_type'      => 'testimonial',
        'posts_per_page' => $atts['limit'],
    );
    $testimonial_query = new WP_Query($args);

    if ($testimonial_query->have_posts()) {
        ob_start();
        
        ?>
        <div id="testimonialCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel">
			<?php if($atts['show_title']): ?>
				<h3 class="text-center fw-bold">Testimonials</h3>
			<?php endif; ?>
			<?php if($atts['indicators']): ?>
			<div class="carousel-indicators">
				<button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Testimonial 1"></button>
				<?php
					for($i = 1; $i < $testimonial_query->post_count; $i++) {
						?>
						<button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="<?php echo $i ?>" aria-label="Testimonial <?php echo $i?>"></button>
						<?php
					}
				?>
			</div>
			<?php endif; ?>
            <div class="carousel-inner">
                <?php
                $counter = 0;
                while ($testimonial_query->have_posts()) {
                    $testimonial_query->the_post();
					// Retrieve the custom fields
                    $testimonial_name = get_post_meta(get_the_ID(), 'testimonial_name', true);
                    $testimonial_other = get_post_meta(get_the_ID(), 'testimonial_other', true);

                    $active_class = ($counter === 0) ? 'active' : '';
                    ?>
                    <div class="carousel-item <?php echo $active_class; ?>" data-bs-interval="5000">
						<div class="testimonial-content"><?php the_content(); ?></div>
						<div class="testimonial-cite">
							<h6><?php echo $testimonial_name ?? '-'; ?></h6>
							<p><?php echo $testimonial_other ?? '-'; ?></p>
						</div>
                    </div>
                    <?php
                    $counter++;
                }
                wp_reset_postdata(); // Reset the post data
                ?>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev testimonial-nav" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next testimonial-nav" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <?php
        $testimonials = ob_get_clean();
		return $testimonials;
    }

	return false;
}

/**
 * Load testimonials on homepage ajax
 * @return void
 */
function load_franchise_testimonials(): void {
    // Try to fetch from transient
    $testimonial_content = get_transient('testimonial_carousel_cache');

    // If no cached content, generate it
    if ($testimonial_content === false) {
        $testimonial_content = franchise_testimonials([]);
        
        if (!empty($testimonial_content)) {
            set_transient('testimonial_carousel_cache', $testimonial_content, DAY_IN_SECONDS);
        } else {
            wp_send_json_error('No testimonials found.');
            wp_die();
        }
    }
    wp_send_json_success($testimonial_content);
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

	// Return nothing if client_data is not set
	if(!CLIENT) return false;

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
		$fields['billing']['billing_address_1']['default'] = $user_info['_address'][0];
	}

    return $fields;
}