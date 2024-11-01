<?php

/* Loads the file for option sanitization */
add_action( 'init', 'wplanner_fb_load_sanitization' );

if ( !function_exists( 'wplanner_fb_load_sanitization' ) ) {

	function wplanner_fb_load_sanitization() {
	
	  require_once ( WP_PLANNER_SETTINGS . 'options-sanitize.php' );
	}
}


/*
 * Creates the settings in the database by looping through the array
 * we supplied in options.php. This is a neat way to do it since
 * we won't have to save settings for headers, descriptions, or arguments.
 *
 * Read more about the Settings API in the WordPress codex:
 * http://codex.wordpress.org/Settings_API
 *
 */
if ( !function_exists( 'wplanner_fb_init' ) ) {

	function wplanner_fb_init() {

	  // Include the required files
	  require_once ( WP_PLANNER_SETTINGS . 'options-interface.php' );
	  require_once ( WP_PLANNER_SETTINGS . 'options-medialibrary-uploader.php' );

	  // Loads the options array
	  require_once ( WP_PLANNER_SETTINGS . 'options.php' );

	  $wplannerfb_settings = get_option( 'wplannerfb' );

	  // Updates the unique option id in the database if it has changed
	  wplanner_fb_option_name();

	  // Gets the unique id, returning a default if it isn't defined
	  if ( isset($wplannerfb_settings['id']) ) {
		$option_name = $wplannerfb_settings['id'];
	  }
	  else {
		$option_name = 'wplannerfb';
	  }

	  // Registers the settings fields and callback
	  register_setting( 'wplannerfb', $option_name, 'wplanner_fb_validate' );
	}
}


/* Add a admin menu page. */
if ( !function_exists( 'wplanner_fb_add_page' ) ) {
	function wplanner_fb_add_page() {
		/*$wplannerfb_page = add_menu_page( __( 'Facebook Post Planner', WP_PLANNER_TEXTDOMAIN ), __( 'Facebook Post Planner', WP_PLANNER_TEXTDOMAIN ), 'manage_options', 'wplannerfb', 'wplanner_fb_page' );*/
		$wplannerfb_page = add_submenu_page('vbsocial-scheduler', __( 'Wordpress Posts Schedular', WP_PLANNER_TEXTDOMAIN ), __( 'Wordpress Posts Schedular', WP_PLANNER_TEXTDOMAIN ), 'manage_options', 'wplannerfb', 'wplanner_fb_page' );
	}
}


/* Loads the CSS */
if ( !function_exists( 'wplanner_fb_load_styles' ) ) {
	
	function wplanner_fb_load_styles() {
		wp_enqueue_style( 'admin-style', WP_PLANNER_URL . 'settings/css/wplanner-admin-style.css' );
		wp_enqueue_style( 'color-picker', WP_PLANNER_URL . 'settings/css/colorpicker.css' );
		wp_enqueue_style( 'jquery-ui-custom', WP_PLANNER_URL . 'settings/css/jquery-ui-1.8.17.custom.css' );
		wp_enqueue_style( 'jquery-ui-timepicker-addon', WP_PLANNER_URL . 'settings/css/jquery-ui-timepicker-addon.css' );
	}
}

/* Loads the javascript */
if ( !function_exists( 'wplanner_fb_load_scripts' ) ) {

	function wplanner_fb_load_scripts() {
		// Inline scripts from options-interface.php
		add_action( 'admin_head', 'wplanner_fb_admin_head' );
		
		// Enqueued scripts
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-sliderAccess-addon', WP_PLANNER_URL . 'settings/js/jquery-ui-sliderAccess-addon.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider') );
		wp_enqueue_script( 'jquery-ui-timepicker-addon', WP_PLANNER_URL . 'settings/js/jquery-ui-timepicker-addon.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider') );
		wp_enqueue_script( 'color-picker', WP_PLANNER_URL . 'settings/js/colorpicker.js', array('jquery') );
		wp_enqueue_script( 'options-fb', WP_PLANNER_URL . 'settings/js/options-fb.js', array('jquery') );
	}
}


if ( !function_exists( 'wplanner_fb_admin_head' ) ) {

	function wplanner_fb_admin_head() {
		// Hook to add custom scripts
		do_action( 'wplanner_fb_custom_scripts' );
	}
}


/*
 * Builds out the options panel.
 *
 * If we were using the Settings API as it was likely intended we would use
 * do_settings_sections here. But as we don't want the settings wrapped in a table,
 * we'll call our own custom wplanner_fb_fields. See options-interface.php
 * for specifics on how each individual field is generated.
 *
 * Nonces are provided using the settings_fields()
 *
 */

if ( !function_exists( 'wplanner_fb_page' ) ) {

	function wplanner_fb_page() {
	
		$return = wplanner_fb_fields();
		settings_errors();
		
		$current_time = explode(':', current_time('mysql'));
		$current_time = $current_time[0] .':'. $current_time[1];
		?>

		<div class="wrap">
			<div style="float: left; text-align:left; width:45%;">
				<img src="<?php echo WP_PLANNER_URL . 'settings/images/logo.png'; ?>" style="height: 48px; margin: 10px 10px 10px 0; float:left;">
				<h2 style="line-height: 60px;"><?php esc_attr_e( 'Wordpress Posts Schedular Options', WP_PLANNER_TEXTDOMAIN ); ?></h2>
			</div>
			<div style="float: right; text-align:right; width:45%;">
				<h2 style="line-height: 60px;">Current date / time: <span id="current_date-time"><?php echo $current_time; ?></span></h2>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					var refreshId = setInterval(function() {
						jQuery("#current_date-time").load('<?php echo WP_PLANNER_URL . 'settings/_current_date-time.php'; ?>?act=current_time&randval='+ Math.random());
					}, 20000);
					jQuery.ajaxSetup({ cache: false });
				});
				</script>
			</div>
			
			<div style="clear: both;"><br /></div>
			
			<h2 class="nav-tab-wrapper fb-pp_nav-tab-wrapper">
				<?php echo $return[1]; ?>
			</h2>

			<div class="metabox-holder">
			<div id="wplannerfb" class="postbox">
			<form action="options.php" method="post">
			<?php settings_fields( 'wplannerfb' ); ?>

			<?php echo $return[0]; /* Settings */ ?>

				<div id="wplannerfb-submit">
					<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save All Options', WP_PLANNER_TEXTDOMAIN ); ?>" />
					<div class="clear"></div>
				</div>
			</form>
			</div> <!-- / #container -->
			</div>
		</div> <!-- / .wrap -->
	<?php
	}
}


/**
 * Validate Options.
 *
 * This runs after the submit/reset button has been clicked and
 * validates the inputs.
 *
 * @uses $_POST['reset']
 * @uses $_POST['update']
 *
 */
if ( !function_exists( 'wplanner_fb_validate' ) ) {

	function wplanner_fb_validate( $input ) {

	  /*
	   * Restore Defaults.
	   *
	   * In the event that the user clicked the "Restore Defaults"
	   * button, the options defined in the theme's options.php
	   * file will be added to the option.
	   *
	   */

	  if ( isset( $_POST['reset'] ) ) {
		add_settings_error( 'wplannerfb', 'restore_defaults', __( 'Default options restored.', WP_PLANNER_TEXTDOMAIN ), 'updated fade' );
		return wplannerfb_get_default_values();
	  }

	  /* Udpdate Settings. */

	  if ( isset( $_POST['update'] ) ) {
		$clean = array();
		$options = wplanner_fb_options();
		
		foreach ( $options as $option ) {

		  if ( ! isset( $option['id'] ) ) {
			continue;
		  }

		  if ( ! isset( $option['type'] ) ) {
			continue;
		  }

		  $id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

		  // Set checkbox to false if it wasn't sent in the $_POST
		  if ( 'info' == $option['type'] && !isset( $input[$id] ) ) {
			$input[$id] = '0';
		  }
		  
		  // Set checkbox to false if it wasn't sent in the $_POST
		  if ( 'checkbox' == $option['type'] && !isset( $input[$id] ) ) {
			$input[$id] = '0';
		  }

		  // Set each item in the multicheck to false if it wasn't sent in the $_POST
		  if ( 'multicheck' == $option['type'] && !isset( $input[$id] ) ) {
			foreach ( $option['options'] as $key => $value ) {
			  $input[$id][$key] = '0';
			}
		  }
		  
		  // For a value to be submitted to database it must pass through a sanitization filter
		  if ( has_filter( 'wplanner_fb_sanitize_' . $option['type'] ) ) {
			$clean[$id] = apply_filters( 'wplanner_fb_sanitize_' . $option['type'], $input[$id], $option );
		  }
		}

		add_settings_error( 'wplannerfb', 'save_options', __( 'Options have been saved.', WP_PLANNER_TEXTDOMAIN ), 'updated fade' );
		return $clean;
	  }

	  /* Request Not Recognized. */
	  return wplannerfb_get_default_values();
	}
}


/**
 * Format Configuration Array.
 *
 * Get an array of all default values as set in
 * options.php. The 'id','std' and 'type' keys need
 * to be defined in the configuration array. In the
 * event that these keys are not present the option
 * will not be included in this function's output.
 *
 * @return    array     Rey-keyed options configuration array.
 *
 * @access    private
 *
 */
if ( !function_exists( 'wplannerfb_get_default_values' ) ) {

	function wplannerfb_get_default_values() {
	
	  $output = array();
	  $config = wplanner_fb_options();
	  foreach ( (array) $config as $option ) {
		if ( ! isset( $option['id'] ) ) {
		  continue;
		}
		if ( ! isset( $option['std'] ) ) {
		  continue;
		}
		if ( ! isset( $option['type'] ) ) {
		  continue;
		}
		if ( has_filter( 'wplanner_fb_sanitize_' . $option['type'] ) ) {
		  $output[$option['id']] = apply_filters( 'wplanner_fb_sanitize_' . $option['type'], $option['std'], $option );
		}
	  }
	  return $output;
	}
}

/**
 * Get Option.
 *
 * Helper function to return the option value.
 * If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 */
if ( ! function_exists( 'wplanner_fb_get_option' ) ) {

  function wplanner_fb_get_option( $name, $default = false ) {
  
    $config = get_option( 'wplannerfb' );

    if ( ! isset( $config['id'] ) ) {
      return $default;
    }

    $options = get_option( $config['id'] );

    if ( isset( $options[$name] ) ) {
      return $options[$name];
    }

    return $default;
  }
}
