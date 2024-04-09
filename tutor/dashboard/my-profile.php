<?php
/**
 * My Profile Page
 *
 * @package Tutor\Templates
 * @subpackage Dashboard
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

$uid  = get_current_user_id();
$user = get_userdata( $uid );

$profile_settings_link = tutor_utils()->get_tutor_dashboard_page_permalink( 'settings' );

$rdate = $user->user_registered;
$fname = $user->first_name;
$lname = $user->last_name;
$uname = $user->user_login;
$email = $user->user_email;

$job   = nl2br( wp_strip_all_tags( get_user_meta( $uid, '_tutor_profile_job_title', true ) ) );
$bio   = get_user_meta( $uid, '_tutor_profile_bio', true );

// Custom Fields
$phone = get_user_meta( $uid, '_phone_no', true);
$gender = get_user_meta( $uid, '_gender', true);
$dob = get_user_meta( $uid, '_dob', true);
$salutation = get_user_meta( $uid, '_salutation', true);
$course = get_user_meta( $uid, '_course', true);
$marital_status = get_user_meta( $uid, '_marital_status', true);
$father = get_user_meta( $uid, '_father', true);
$mother = get_user_meta( $uid, '_mother', true);
$religion = get_user_meta( $uid, '_religion', true);
$category = get_user_meta( $uid, '_category', true);
$domicile_state = get_user_meta( $uid, '_domicile_state', true);
$domicile_district = get_user_meta( $uid, '_domicile_district', true);
$state_const = get_user_meta( $uid, '_state_const', true);
$aadhar = get_user_meta( $uid, '_aadhar', true);
$address = get_user_meta( $uid, '_address', true);
$pin = get_user_meta( $uid, '_pin', true);

$profile_data = array(
	array( __( 'Registration Date', 'tutor' ), ( $rdate ? tutor_i18n_get_formated_date( tutor_utils()->get_local_time_from_unix( $rdate ) ) : '' ) ),
	array( __( 'First Name', 'tutor' ), ( $fname ? $fname : esc_html( '-' ) ) ),
	array( __( 'Last Name', 'tutor' ), ( $lname ? $lname : __( '-' ) ) ),
	array( __( 'Username', 'tutor' ), $uname ),
	array( __( 'Email', 'tutor' ), $email ),
	array( __( 'Skill/Occupation', 'tutor' ), ( $job ? $job : '-' ) ),
	array( __( 'Biography', 'tutor' ), $bio ? $bio : '-' ),
	array( __('Phone Number', 'tutor'), $phone ? $phone : '-' ),
	array( __('Gender', 'tutor'), $gender ? $gender : '-' ),
	array( __('Date of Birth', 'tutor'), $dob ? $dob : '-' ),
	array( __('Salutation', 'tutor'), $salutation ? $salutation : '-' ),
	array( __('Course', 'tutor'), $course ? $course : '-' ),
	array( __('Marital Status', 'tutor'), $marital_status ? $marital_status : '-' ),
	array( __('Father\'s Name', 'tutor'), $father ? $father : '-' ),
	array( __('Mother\'s Name', 'tutor'), $mother ? $mother : '-' ),
	array( __('Religion', 'tutor'), $religion ? $religion : '-' ),
	array( __('Category', 'tutor'), $category ? $category : '-' ),
	array( __('Domicile State', 'tutor'), $domicile_state ? $domicile_state : '-' ),
	array( __('Domicile District', 'tutor'), $domicile_district ? $domicile_district : '-' ),
	array( __('State Constituency', 'tutor'), $state_const ? $state_const : '-' ),
	array( __('Aadhaar Number', 'tutor'), $aadhar ? $aadhar : '-' ),
	array( __('Address', 'tutor'), $address ? $address : '-' ),
	array( __('Pin', 'tutor'), $pin ? $pin : '-' ),
);

?>

<div class="tutor-fs-5 tutor-fw-medium tutor-color-black tutor-mb-24"><?php esc_html_e( 'My Profile', 'tutor' ); ?></div>
<div class="tutor-dashboard-content-inner tutor-dashboard-profile-data">
	<?php
		foreach ( $profile_data as $key => $data ) :
			?>
		<div class="tutor-row tutor-mb-24">
			<div class="tutor-col-12 tutor-col-sm-5 tutor-col-lg-3">
				<span class="tutor-fs-6 tutor-color-secondary"><?php echo esc_html( $data[0] ); ?></span>
			</div>
			<div class="tutor-col-12 tutor-col-sm-7 tutor-col-lg-9">
				<?php
				echo 'Biography' === $data[0] ?
						'<span class="tutor-fs-6 tutor-color-secondary">' . wp_kses_post( wpautop( $data[1] ) ) . '</span>'
						: '<span class="tutor-fs-6 tutor-fw-medium tutor-color-black">' . esc_html( $data[1] ) . '</span>';
				?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
