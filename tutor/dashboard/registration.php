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
				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php esc_html_e( 'First Name', 'tutor' ); ?> <span class="text-danger">*</span>
						</label>

						<input type="text" name="first_name" class="tutor-form-control" value="<?php echo esc_attr( tutor_utils()->input_old( 'first_name' ) ); ?>" required autocomplete="given-name">
					</div>
				</div>

				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php esc_html_e( 'Last Name', 'tutor' ); ?> <span class="text-danger">*</span>
						</label>

						<input type="text" name="last_name" class="tutor-form-control" value="<?php echo esc_attr( tutor_utils()->input_old( 'last_name' ) ); ?>" required autocomplete="family-name">
					</div>
				</div>

			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php esc_html_e( 'Username', 'tutor' ); ?> <span class="text-danger">*</span>
						</label>

						<input type="text" name="user_login" class="tutor_user_name tutor-form-control" value="<?php echo esc_attr( tutor_utils()->input_old( 'user_login' ) ); ?>" required autocomplete="username">
					</div>
				</div>

				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php esc_html_e( 'E-Mail', 'tutor' ); ?> <span class="text-danger">*</span>
						</label>

						<input type="text" name="email" class="tutor-form-control" value="<?php echo esc_attr( tutor_utils()->input_old( 'email' ) ); ?>" required autocomplete="email">
					</div>
				</div>

			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-6">
					<div class="tutor-form-group">
						<div class="tutor-password-strength-checker">
							<div class="tutor-password-field">
								<label class="tutor-form-label tutor-color-secondary">
									<?php esc_html_e( 'Password', 'tutor' ); ?> <span class="text-danger">*</span>
								</label>

								<input class="password-checker" id="tutor-new-password" type="password" name="password" class="tutor-form-control" value="<?php echo esc_attr( tutor_utils()->input_old( 'password' ) ); ?>" required autocomplete="new-password" style="margin-bottom: 0;">
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
						<label class="tutor-form-label tutor-color-secondary">
							<?php esc_html_e( 'Password confirmation', 'tutor' ); ?> <span class="text-danger">*</span>
						</label>

						<div class="tutor-form-wrap">
							<span class="tutor-validation-icon tutor-icon-mark tutor-color-success tutor-form-icon tutor-form-icon-reverse" style="display: none;"></span>
							<input type="password" name="password_confirmation" class="tutor-form-control" value="<?php echo esc_attr( tutor_utils()->input_old( 'password_confirmation' ) ); ?>" required autocomplete="new-password" style="margin-bottom: 0;">
						</div>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php _e('Phone Number', 'tutor'); ?> <span class="text-danger">*</span>
						</label>

						<input type="text" name="phone_no" class="tutor-form-control" value="<?php echo tutor_utils()->input_old('phone_no'); ?>">
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php _e('Father\'s name', 'tutor'); ?> <span class="text-danger">*</span>
						</label>
						<input type="text" name="father" class="tutor-form-control" value="<?php echo tutor_utils()->input_old('father'); ?>" required>
					</div>
				</div>
			</div>

			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php _e('Address', 'tutor'); ?> <span class="text-danger">*</span>
						</label>
						<input type="text" name="address" class="tutor-form-control" value="<?php echo tutor_utils()->input_old('address'); ?>" required>
					</div>
				</div>
			</div>
			
			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php _e('School Name', 'tutor'); ?> <span class="text-danger">*</span>
						</label>
						<input type="text" name="school" class="tutor-form-control" value="<?php echo tutor_utils()->input_old('school'); ?>" required>
					</div>
				</div>
			</div>
			
			<div class="tutor-form-row">
				<div class="tutor-form-col-12">
					<div class="tutor-form-group">
						<label class="tutor-form-label tutor-color-secondary">
							<?php _e('Class and Section', 'tutor'); ?> <span class="text-danger">*</span>
						</label>
						<input type="text" name="class" class="tutor-form-control" value="<?php echo tutor_utils()->input_old('class'); ?>" required>
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
