<?php
add_filter('tutor_dashboard/bottom_nav_items', 'site_setting_dashboard');
add_filter('tutor_dashboard/instructor_nav_items', 'instructor_navigation');
add_filter('tutor_dashboard/nav_items/settings/nav_items', 'dashboard_navigation');
add_action('tutor_dashboard/nav_items', 'practice_portal_link');

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
        $links['website-admin'] = array(
			'title' => 'Site Admin',
			'icon' => 'tutor-icon-gear',
			'url' => get_dashboard_url()
		);
    }
    return $links;
}

function practice_portal_link($links) {
	$user = get_current_user_id();
	if(current_user_can('tutor_instructor')) {
		$links['practice'] = array(
            'title' => __('Practice Portal', 'tutor'),
            'icon' => 'tutor-icon-external-link',
			'url' => 'https://app.iivapracticeportal.com/'
        );
	}
    return $links;
}