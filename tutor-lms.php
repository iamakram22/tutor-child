<?php
add_filter('tutor_dashboard/bottom_nav_items', 'site_setting_dashboard');
add_filter('tutor_dashboard/instructor_nav_items', 'instructor_navigation');
add_filter('tutor_dashboard/nav_items/settings/nav_items', 'dashboard_navigation');
add_filter('tutor_dashboard/nav_items', 'some_links_dashboard');

/**
 * Modify instructor dashboard navigations
 * 
 * @return array
 */
function instructor_navigation($links)
{
    $options = ['withdraw'];
    foreach ($options as $option) {
        unset($links[$option]);
    }
    return $links;
}

/**
 * Modify Tutor dashboard navigations
 * 
 * @return array
 */
function dashboard_navigation($nav)
{
    $options = ['withdrawal'];
    foreach ($options as $option) {
        unset($nav[$option]);
    }
    return $nav;
}

/**
 * Add custom navigation to Tutor dashboard
 *
 * @param array $links
 * @return array
 */
function site_setting_dashboard($links)
{
    if (current_user_can('tutor_instructor')) {
        $links['site-setting'] = array(
            'title' => __('Site Settings', 'tutor'),
            'icon' => 'tutor-icon-website'
        );
    }
    return $links;
}

function some_links_dashboard($links)
{
	$links['invoice'] = array(
		'title' => __('Invoices', 'tutor'),
		'icon' => 'tutor-icon-file-text',
	);
	return $links;
}

add_filter('tutor_student_registration_required_fields', 'required_phone_no_callback');
if ( ! function_exists('required_phone_no_callback')){
    function required_phone_no_callback($atts){
        $atts['phone_no'] = 'Phone Number field is required';
        return $atts;
    }
}
add_action('user_register', 'add_phone_after_user_register');
add_action('profile_update', 'add_phone_after_user_register');
if ( ! function_exists('add_phone_after_user_register')) {
    function add_phone_after_user_register($user_id){
        if ( ! empty($_POST['phone_no'])) {
            $phone_number = sanitize_text_field($_POST['phone_no']);
            update_user_meta($user_id, 'phone_number', $phone_number);
        }
    }
}