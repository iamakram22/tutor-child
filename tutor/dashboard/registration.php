<?php
/**
 * Tutor registration template
 *
 * @package Tutor\Templates
 * @subpackage Dashboard
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

wp_enqueue('bootstrap-style');
?>

<?php if ( ! get_option( 'users_can_register', false ) ) : ?> 
	<?php
		$args = array(
			'image_path'  => tutor()->url . 'assets/images/construction.png',
			'title'       => __( 'Oooh! Access Denied', 'tutor' ),
			'description' => __( 'You do not have access to this area of the application. Please refer to your system  administrator.', 'tutor' ),
			'button'      => array(
				'text'  => __( 'Go to Home', 'tutor' ),
				'url'   => get_home_url(),
				'class' => 'tutor-btn',
			),
		);
		tutor_load_template( 'feature_disabled', $args );
		?>
<?php else : ?>

	<div id="tutor-registration-wrap">

		<?php do_action( 'tutor_before_student_reg_form' ); ?>

		<form method="post" enctype="multipart/form-data" id="tutor-registration-form">
			<input type="hidden" name="tutor_course_enroll_attempt" value="<?php echo isset( $_GET['enrol_course_id'] ) ? (int) $_GET['enrol_course_id'] : ''; ?>">
			<?php do_action( 'tutor_student_reg_form_start' ); ?>

			<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
			<input type="hidden" value="tutor_register_student" name="tutor_action"/>

			<?php
			$validation_errors = apply_filters( 'tutor_student_register_validation_errors', array() );
			if ( is_array( $validation_errors ) && count( $validation_errors ) ) :
				?>
				<div class="tutor-alert tutor-warning tutor-mb-12">
					<ul class="tutor-required-fields">
						<?php foreach ( $validation_errors as $validation_error ) : ?>
							<li>
								<?php echo esc_html( $validation_error ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			
			<div class="tutor-form-row">
				<div class="tutor-form-group">
					<?php esc_html_e( 'Salutation', 'tutor' ); ?>
					<div class="d-flex">
						<label class="pr-2">
							<input type="radio" name="salutation" value="mr" required>
							Mr.
						</label>
						<label>
							<input type="radio" name="salutation" value="ms" required>
							Ms.
						</label>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label>
							<?php esc_html_e( 'First Name', 'tutor' ); ?>
						</label>

						<input type="text" name="first_name" value="<?php echo esc_attr( tutor_utils()->input_old( 'first_name' ) ); ?>" placeholder="<?php esc_attr_e( 'First Name', 'tutor' ); ?>" required autocomplete="given-name">
					</div>
				</div>

				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label>
							<?php esc_html_e( 'Last Name', 'tutor' ); ?>
						</label>

						<input type="text" name="last_name" value="<?php echo esc_attr( tutor_utils()->input_old( 'last_name' ) ); ?>" placeholder="<?php esc_attr_e( 'Last Name', 'tutor' ); ?>" required autocomplete="family-name">
					</div>
				</div>

			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label>
							<?php esc_html_e( 'User Name', 'tutor' ); ?>
						</label>

						<input type="text" name="user_login" class="tutor_user_name" value="<?php echo esc_attr( tutor_utils()->input_old( 'user_login' ) ); ?>" placeholder="<?php esc_html_e( 'User Name', 'tutor' ); ?>" required autocomplete="username">
					</div>
				</div>

				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label>
							<?php esc_html_e( 'E-Mail', 'tutor' ); ?>
						</label>

						<input type="text" name="email" value="<?php echo esc_attr( tutor_utils()->input_old( 'email' ) ); ?>" placeholder="<?php esc_html_e( 'E-Mail', 'tutor' ); ?>" required autocomplete="email">
					</div>
				</div>

			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<div class="tutor-password-strength-checker">
							<div class="tutor-password-field">
								<label>
									<?php esc_html_e( 'Password', 'tutor' ); ?>
								</label>

								<input class="password-checker" id="tutor-new-password" type="password" name="password" value="<?php echo esc_attr( tutor_utils()->input_old( 'password' ) ); ?>" placeholder="<?php esc_html_e( 'Password', 'tutor' ); ?>" required autocomplete="new-password" style="margin-bottom: 0;">
								<span class="show-hide-btn"></span>
							</div>

							<div class="tutor-password-strength-hint">
								<div class="indicator">
									<span class="weak"></span>
									<span class="medium"></span>
									<span class="strong"></span>
								</div>
								<div class="text tutor-fs-7 tutor-color-muted"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label>
							<?php esc_html_e( 'Password confirmation', 'tutor' ); ?>
						</label>

						<div class="tutor-form-wrap">
							<span class="tutor-validation-icon tutor-icon-mark tutor-color-success tutor-form-icon tutor-form-icon-reverse" style="display: none;"></span>
							<input type="password" name="password_confirmation" value="<?php echo esc_attr( tutor_utils()->input_old( 'password_confirmation' ) ); ?>" placeholder="<?php esc_html_e( 'Password Confirmation', 'tutor' ); ?>" required autocomplete="new-password" style="margin-bottom: 0;">
						</div>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Phone Number', 'tutor'); ?>
						</label>

						<input type="text" name="phone_no" value="<?php echo tutor_utils()->input_old('phone_no'); ?>" placeholder="<?php _e('Phone Number', 'tutor'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Gender', 'tutor'); ?>
						</label>
						<select name="gender" class="form-select" value="<?php echo tutor_utils()->input_old('gender'); ?>">
							<option value="Male">Male</option>
							<option value="Female">Female</option>
							<option value="Prefer not to say">Prefer not to say</option>
						</select>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Date of Birth', 'tutor'); ?>
						</label>
						<input type="date" name="dob" class="form-control" value="<?php echo tutor_utils()->input_old('dob'); ?>" max="<?php echo date('Y-m-d', strtotime('-1 days')); ?>" required>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Course', 'tutor'); ?>
						</label>
						<select name="course" class="form-select" value="<?php echo tutor_utils()->input_old('course'); ?>">
							<option value="Abacus">Abacus</option>
							<option value="Vedic Maths">Vedic Maths</option>
							<option value="Handwriting">Handwriting</option>
							<option value="Phonics">Phonics</option>
							<option value="Art and Craft">Art and Craft</option>
							<option value="Robotics">Robotics</option>
							<option value="Calligraphy">Calligraphy</option>
							<option value="Rubik's Cube">Rubik's Cube</option>
						</select>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Marital status', 'tutor'); ?>
						</label>
						<select name="marital_status" class="form-select" value="<?php echo tutor_utils()->input_old('marital_status'); ?>">
							<option value="Married">Married</option>
							<option value="Single/Unmarried">Single/Unmarried</option>
							<option value="Divorced">Divorced</option>
						</select>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Father\'s name', 'tutor'); ?>
						</label>
						<input type="text" name="father" class="form-control" value="<?php echo tutor_utils()->input_old('father'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Mother\'s name', 'tutor'); ?>
						</label>
						<input type="text" name="mother" class="form-control" value="<?php echo tutor_utils()->input_old('mother'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Religion', 'tutor'); ?>
						</label>
						<input type="text" name="religion" class="form-control" value="<?php echo tutor_utils()->input_old('religion'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Category', 'tutor'); ?>
						</label>
						<input type="text" name="category" class="form-control" value="<?php echo tutor_utils()->input_old('category'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Domicile state', 'tutor'); ?>
						</label>
						<input type="text" name="domicile_state" class="form-control" value="<?php echo tutor_utils()->input_old('domicile_state'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Domicile district', 'tutor'); ?>
						</label>
						<input type="text" name="domicile_district" class="form-control" value="<?php echo tutor_utils()->input_old('domicile_district'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('State constituency', 'tutor'); ?>
						</label>
						<input type="text" name="state_const" class="form-control" value="<?php echo tutor_utils()->input_old('state_const'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Aadhar number', 'tutor'); ?>
						</label>
						<input type="text" inputmode="numeric" name="aadhar" class="form-control" value="<?php echo tutor_utils()->input_old('aadhar'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Permanent address', 'tutor'); ?>
						</label>
						<input type="text" name="address" class="form-control" value="<?php echo tutor_utils()->input_old('address'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label>
							<?php _e('Area pin code', 'tutor'); ?>
						</label>
						<input type="number" name="pin" class="form-control" value="<?php echo tutor_utils()->input_old('pin'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
					<?php
						// providing register_form hook.
						do_action( 'tutor_student_reg_form_middle' );
						do_action( 'register_form' );
					?>
					</div>
				</div>
			</div>    

			<?php do_action( 'tutor_student_reg_form_end' ); ?>

			<?php
				$tutor_toc_page_link = tutor_utils()->get_toc_page_link();
			?>
			<?php if ( null !== $tutor_toc_page_link ) : ?>
				<div class="tutor-mb-24">
					<?php esc_html_e( 'By signing up, I agree with the website\'s', 'tutor' ); ?> <a target="_blank" href="<?php echo esc_url( $tutor_toc_page_link ); ?>" title="<?php esc_html_e( 'Terms and Conditions', 'tutor' ); ?>"><?php esc_html_e( 'Terms and Conditions', 'tutor' ); ?></a>
				</div>
			<?php endif; ?>

			<div>
				<button type="submit" name="tutor_register_student_btn" value="register" class="tutor-btn tutor-btn-primary tutor-btn-block"><?php esc_html_e( 'Register', 'tutor' ); ?></button>
			</div>
			<?php do_action( 'tutor_after_register_button' ); ?>
			
		</form>
		<?php do_action( 'tutor_after_registration_form_wrap' ); ?>
		
	</div>
	<?php do_action( 'tutor_after_student_reg_form' ); ?>
<?php endif; ?>
