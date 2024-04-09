<?php
add_filter('tutor_dashboard/bottom_nav_items', 'site_setting_dashboard');
add_filter('tutor_dashboard/instructor_nav_items', 'instructor_navigation');
add_filter('tutor_dashboard/nav_items/settings/nav_items', 'dashboard_navigation');

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