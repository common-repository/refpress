<?php
/**
 * Login and register page
 *
 * This template can be overridden by copying it to yourtheme/refpress/partial/login-register.php.
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="refpress-account-login-register-wrap">

    <div class="refpress-account-login-wrap">

        <h2> <?php _e( 'Login', 'refpress' ); ?> </h2>

        <?php
		$args = array(
			'echo'                      => true,
			// Default 'redirect' value takes the user back to the request URI.
			'redirect'                  => refpress_account_uri(),
			'form_id'                   => 'refpressLoginForm',
			'label_username'            => __( 'Username or Email Address', 'refpress' ),
			'label_password'            => __( 'Password', 'refpress' ),
			'label_remember'            => __( 'Remember Me', 'refpress' ),
			'label_log_in'              => __( 'Log In', 'refpress' ),
			'label_create_new_account'  => __( 'Register', 'refpress' ),
			'id_username'               => 'user_login',
			'id_password'               => 'user_pass',
			'id_remember'               => 'rememberme',
			'id_submit'                 => 'wp-submit',
			'remember'                  => true,
			'value_username'            => refpress_input_old( 'log' ),
			// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
			'value_remember'            => false,
			'wp_lostpassword_url'       => apply_filters( 'refpress_lostpassword_url', wp_lostpassword_url() ),
			'wp_lostpassword_label'     => __( 'Forgot Password?', 'refpress' ),
		);

		$args = apply_filters( 'refpress_login_form_args', $args );

		?>

        <?php do_action("refpress_before_login_form"); ?>

        <form name="<?php echo $args['form_id']?>" id="<?php echo $args['form_id']?>" method="post">

			<?php do_action("refpress_login_form_start");?>
			<?php refpress_nonce_field();?>
			<?php refpress_generate_field_action( 'user_login' ); ?>
			<?php refpress_form_errors( 'user_login' ); ?>


            <div class="login-username refpress-login-form-group">
                <label for="<?php echo esc_attr( $args['id_username'] )?>"> <?php echo $args[ 'label_username' ]; ?> </label>

                <div class="refpress-login-form-field-wrap">
                    <input type="text" placeholder="<?php echo esc_html( $args['label_username'] )?>" name="log" id="<?php echo esc_attr( $args['id_username'] )?>" class="input" value="<?php echo esc_attr( $args['value_username'] )?>" size="20" />

                </div>
            </div>

            <div class="login-password refpress-login-form-group">
                <label for="<?php echo esc_attr( $args['id_password'] )?>"> <?php echo $args[ 'label_password' ]; ?> </label>

                <div class="refpress-login-form-field-wrap">
                    <input type="password" placeholder="<?php echo esc_html( $args['label_password'] )?>" name="pwd" id="<?php echo esc_attr( $args['id_password'] )?>" class="input" value="" size="20"/>
                </div>
            </div>

			<?php
			do_action("refpress_login_form_middle");
			do_action("login_form");
			apply_filters("login_form_middle",'','');
			?>


            <div class="refpress-login-rememeber-wrap">
				<?php  if($args['remember']):?>
                    <p class="login-remember">
                        <label>
                            <input name="rememberme" type="checkbox" id="<?php echo esc_attr( $args['id_remember'] )?>"
                                   value="forever"
								<?php echo $args['value_remember'] ? 'checked' : '';?>
                            >
							<?php echo esc_html($args['label_remember']);?>
                        </label>
                    </p>
				<?php endif;?>

            </div>

			<?php do_action("refpress_login_form_end");?>

            <p class="login-submit">
                <input type="submit" name="wp-submit" id="<?php echo esc_attr( $args['id_submit'] )?>" class="button-primary" value="<?php echo esc_attr( $args['label_log_in'] )?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url( $args['redirect'] )?>" />
            </p>

            <p class="forgot-password-wrap">
                <a href="<?php echo esc_url($args['wp_lostpassword_url'])?>">
		            <?php echo esc_html($args['wp_lostpassword_label']);?>
                </a>
            </p>

        </form>

		<?php
		do_action("refpress_after_login_form");
		?>

    </div>

    <div class="refpress-account-register-wrap">

        <h2> <?php _e( 'Register', 'refpress' ); ?> </h2>

        <form method="post" enctype="multipart/form-data">

			<?php refpress_nonce_field(); ?>
			<?php refpress_generate_field_action( 'user_register' ); ?>
			<?php refpress_form_errors(  'user_register'); ?>

            <fieldset>

                <legend> <?php _e( 'Personal Information', 'refpress' ); ?> </legend>

                <div class="refpress-register-form-group">
                    <label>
			            <?php _e('Full Name', 'refpress'); ?>
                    </label>

                    <div class="refpress-register-field-wrap">
                        <input type="text" name="full_name" class="refpress-form-control" value="<?php echo refpress_input_old('full_name'); ?>" placeholder="<?php _e('Full Name', 'refpress'); ?>">
                    </div>
                </div>

                <div class="refpress-register-field-wrap">
                    <label>
			            <?php _e('User Name', 'refpress'); ?>
                    </label>

                    <div class="refpress-register-field-wrap">
                        <input type="text" name="user_login" class="refpress_user_name refpress-form-control" value="<?php echo refpress_input_old('user_login'); ?>" placeholder="<?php _e('User Name', 'refpress'); ?>">
                    </div>
                </div>

                <div class="refpress-register-field-wrap">
                    <label>
			            <?php _e('E-Mail', 'refpress'); ?>
                    </label>

                    <div class="refpress-register-field-wrap">
                        <input type="text" name="email" class="refpress-form-control" value="<?php echo refpress_input_old('email'); ?>" placeholder="<?php _e('E-Mail', 'refpress'); ?>">
                    </div>
                </div>

                <div class="refpress-register-field-wrap">
                    <label>
			            <?php _e('Password', 'refpress'); ?>
                    </label>

                    <div class="refpress-register-field-wrap">
                        <input type="password" name="password" class="refpress-form-control" value="<?php echo refpress_input_old('password'); ?>" placeholder="<?php _e('Password', 'refpress'); ?>">
                    </div>
                </div>

                <div class="refpress-register-field-wrap">
                    <label>
			            <?php _e('Password confirmation', 'refpress'); ?>
                    </label>

                    <div class="refpress-register-field-wrap">
                        <input type="password" name="password_confirmation" class="refpress-form-control" value="<?php echo refpress_input_old('password_confirmation'); ?>" placeholder="<?php _e('Password Confirmation', 'refpress'); ?>">
                    </div>
                </div>
            </fieldset>



            <fieldset>
                <legend>
		            <?php _e( 'Promotional Information', 'refpress' ); ?>
                </legend>

            <div class="refpress-register-field-wrap">
                <label>
			        <?php _e('How you will promot us?', 'refpress'); ?>
                </label>

                <div class="refpress-register-field-wrap">
                    <textarea name="promotional_strategies" class="refpress-form-control" placeholder="<?php _e( 'Write your promotional strategies', 'refpress' ) ?>"><?php echo refpress_input_old('promotional_strategies'); ?></textarea>
                </div>
            </div>

            <div class="refpress-register-field-wrap">
                <label>
			        <?php _e('Promotional Properties', 'refpress'); ?>
                </label>

                <div class="refpress-register-field-wrap">
                    <textarea name="promotional_properties" class="refpress-form-control" placeholder="<?php _e( 'Multiple URLs should be in separate lines.', 'refpress' ); ?>"><?php echo refpress_input_old('promotional_properties'); ?></textarea>

                    <p class="repress-form-help"> <?php _e( 'Enter URLs where you will promote us. It could be websites, apps, YouTube channels, social accounts, pages, groups, or anything else. Multiple URLs should be in separate lines.', 'refpress' ); ?> </p>
                </div>
            </div>

            </fieldset>


            <div class="refpress-register-footer">

	            <?php
	            $privacy_policy_text = refpress_get_privacy_policy_text();
	            if ( ! empty( $privacy_policy_text ) ) {
		            echo "<p> {$privacy_policy_text} </p>";
	            }
	            ?>

                <div class="refpress-register-field-wrap refpress-reg-form-btn-wrap">
                    <button type="submit" name="refpress_register_instructor_btn" value="register" class="refpress-btn  refpress-btn-primary">
			            <?php _e('Create Account', 'refpress'); ?>
                    </button>
                </div>

            </div>

        </form>

    </div>

</div>
