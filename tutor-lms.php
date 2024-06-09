<?php
add_filter('tutor_dashboard/bottom_nav_items', 'site_setting_dashboard');
add_filter('tutor_dashboard/instructor_nav_items', 'instructor_navigation');
add_filter('tutor_dashboard/nav_items/settings/nav_items', 'dashboard_navigation');
add_action('tutor_backend_profile_fields_after', 'tutor_custom_profile_fields');
add_action('tutor_dashboard/nav_items', 'practice_portal_link');
add_action('tutor_course/single/after/sidebar', 'school_contact_details');

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

/**
 * Add custom profile fields to WP user profile
 *
 * @return void
 */
function tutor_custom_profile_fields() 
{
    if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
        $user_id = absint($_GET['user_id']);
    } else {
        $user_id = get_current_user_id();
    }
    ob_start();

    foreach(CUSTOM_FIELDS as $key => $value) {
        $user_meta = esc_attr( get_user_meta( $user_id, '_' . $key, true ) );
        ?>
        <tr class="user-description-wrap">
            <th><label for="<?php esc_html_e($key) ?>"><?php esc_html_e( $value, 'tutor' ); ?></label></th>
            <td>
                <?php if($key === 'salutation') : ?>
                    <select name="<?php echo $key ?>">
                        <option value="mr" <?php echo $user_meta === 'mr' ? 'selected' : '' ?> >Mr.</option>
                        <option value="ms" <?php echo $user_meta === 'ms' ? 'selected' : '' ?> >Ms.</option>
                    </select>
                <?php elseif($key === 'gender') : ?>
                    <select name="<?php echo $key ?>">
                        <option value="Male" <?php echo $user_meta === 'Male' ? 'selected' : '' ?> >Male</option>
                        <option value="Female" <?php echo $user_meta === 'Female' ? 'selected' : '' ?> >Female</option>
                        <option value="Prefer not to say" <?php echo $user_meta === 'Prefer not to say' ? 'selected' : '' ?> >Prefer not to say</option>
                    </select>
                <?php elseif($key === 'course') : ?>
                    <select name="<?php echo $key ?>">
                        <option value="Abacus" <?php echo $user_meta === 'Abacus' ? 'selected' : '' ?> >Abacus</option>
                        <option value="Vedic Maths" <?php echo $user_meta === 'Vedic Maths' ? 'selected' : '' ?> >Vedic Maths</option>
                        <option value="Handwriting" <?php echo $user_meta === 'Handwriting' ? 'selected' : '' ?> >Handwriting</option>
                        <option value="Phonics" <?php echo $user_meta === 'Phonics' ? 'selected' : '' ?> >Phonics</option>
                        <option value="Art and Craft" <?php echo $user_meta === 'Art and Craft' ? 'selected' : '' ?> >Art and Craft</option>
                        <option value="Robotics" <?php echo $user_meta === 'Robotics' ? 'selected' : '' ?> >Robotics</option>
                        <option value="Calligraphy" <?php echo $user_meta === 'Calligraphy' ? 'selected' : '' ?> >Calligraphy</option>
                        <option value="Rubik's Cube" <?php echo $user_meta === 'Rubik\'s Cube' ? 'selected' : '' ?> >Rubik's Cube</option>
                    </select>
                <?php else : ?>
                    <input type="text" name="<?php esc_html_e($key) ?>" id="<?php esc_html_e($key) ?>" value="<?php echo $user_meta ?>" class="regular-text" />
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
    
    $profile = ob_get_clean();
    echo $profile;
}

/**
 * Add school contact number on course detail page
 *
 * @return string
 */
function school_contact_details() {
	ob_start();
	?>
	<div class="tutor-single-course-sidebar-more tutor-mt-24">
		<div>
			<h3 class="tutor-fs-6 tutor-fw-medium tutor-color-black tutor-mb-16">
				<?php esc_html_e( 'Contact AVAS', 'tutor' ); ?>
			</h3>
			<ul class="tutor-course-details-widget-list tutor-fs-6 tutor-color-black">
				<?php
    				$school_phone = get_field('school_number');
    				// echo '<pre>' . print_r($school_phone,true) . '</pre>';
    				if(empty($school_phone)) {
    				    $school_phone = '+918860633099';
    				}
					$school_phone = explode(',', $school_phone);
					foreach ( $school_phone as $phone ) : ?>
					<li class="tutor-d-flex tutor-mb-12">
						<span class="tutor-icon-mobile tutor-color-muted tutor-mt-2 tutor-mr-8 tutor-fs-8" area-hidden="true"></span>
						<span><a href="tel:<?php echo esc_html($phone) ?>" class="tutor-color-black"><?php echo esc_html( $phone ); ?></a></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
	echo ob_get_clean();
}