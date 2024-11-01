<?php

/* Text */
add_filter( 'wplanner_fb_sanitize_text', 'sanitize_text_field' );


/* Textarea */
if ( !function_exists( 'wplanner_fb_sanitize_textarea' ) ) {

	function wplanner_fb_sanitize_textarea($input) {
	
		global $allowedposttags;
		$output = wp_kses( $input, $allowedposttags );
		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_textarea', 'wplanner_fb_sanitize_textarea' );


/* Info */
add_filter( 'wplanner_fb_sanitize_info', 'wplanner_fb_sanitize_allowedposttags' );


/* Select */
add_filter( 'wplanner_fb_sanitize_select', 'wplanner_fb_sanitize_enum', 10, 2 );


/* Radio */
add_filter( 'wplanner_fb_sanitize_radio', 'wplanner_fb_sanitize_enum', 10, 2 );


/* Images */
add_filter( 'wplanner_fb_sanitize_images', 'wplanner_fb_sanitize_enum', 10, 2 );


/* Checkbox */
if ( !function_exists( 'wplanner_fb_sanitize_checkbox' ) ) {

	function wplanner_fb_sanitize_checkbox( $input ) {
	
		if ( $input ) {
			$output = "1";
		} else {
			$output = "0";
		}
		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_checkbox', 'wplanner_fb_sanitize_checkbox' );


/* Multicheck */
if ( !function_exists( 'wplanner_fb_sanitize_multicheck' ) ) {

	function wplanner_fb_sanitize_multicheck( $input, $option ) {

		$output = '';
		if ( is_array( $input ) ) {
			foreach( $option['options'] as $key => $value ) {
				$output[$key] = "0";
			}
			foreach( $input as $key => $value ) {
				if ( array_key_exists( $key, $option['options'] ) && $value ) {
					$output[$key] = "1"; 
				}
			}
		}
		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_multicheck', 'wplanner_fb_sanitize_multicheck', 10, 2 );

/* Color Picker */
add_filter( 'wplanner_fb_sanitize_color', 'wplanner_fb_sanitize_hex' );


/* Uploader */
if ( !function_exists( 'wplanner_fb_sanitize_upload' ) ) {

	function wplanner_fb_sanitize_upload( $input ) {
	
		$output = '';
		$filetype = wp_check_filetype( $input );
		if ( $filetype["ext"] ) {
			$output = $input;
		}
		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_upload', 'wplanner_fb_sanitize_upload' );


/* Allowed Tags */
if ( !function_exists( 'wplanner_fb_sanitize_allowedtags' ) ) {

	function wplanner_fb_sanitize_allowedtags( $input ) {
	
		global $allowedtags;
		$output = wpautop( wp_kses( $input, $allowedtags ) );
		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_info', 'wplanner_fb_sanitize_allowedtags' );


/* Allowed Post Tags */
if ( !function_exists( 'wplanner_fb_sanitize_allowedposttags' ) ) {

	function wplanner_fb_sanitize_allowedposttags($input) {
	
		global $allowedposttags;
		$output = wpautop( wp_kses( $input, $allowedposttags ) );
		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_info', 'wplanner_fb_sanitize_allowedposttags' );


/* Check that the key value sent is valid */
if ( !function_exists( 'wplanner_fb_sanitize_enum' ) ) {

	function wplanner_fb_sanitize_enum( $input, $option ) {
	
		$output = '';
		if ( array_key_exists( $input, $option['options'] ) ) {
			$output = $input;
		}
		return $output;
	}
}


/* Background */
if ( !function_exists( 'wplanner_fb_sanitize_background' ) ) {

	function wplanner_fb_sanitize_background( $input ) {
		$output = wp_parse_args( $input, array(
			'color' => '',
			'image'  => '',
			'repeat'  => __( 'repeat', WP_PLANNER_TEXTDOMAIN ),
			'position' => __( 'top center', WP_PLANNER_TEXTDOMAIN ),
			'attachment' => __( 'scroll', WP_PLANNER_TEXTDOMAIN )
		) );

		$output['color'] = apply_filters( 'wplanner_fb_sanitize_hex', $input['color'] );
		$output['image'] = apply_filters( 'wplanner_fb_sanitize_upload', $input['image'] );
		$output['repeat'] = apply_filters( 'wplanner_fb_background_repeat', $input['repeat'] );
		$output['position'] = apply_filters( 'wplanner_fb_background_position', $input['position'] );
		$output['attachment'] = apply_filters( 'wplanner_fb_background_attachment', $input['attachment'] );

		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_background', 'wplanner_fb_sanitize_background' );


if ( !function_exists( 'wplanner_fb_sanitize_background_repeat' ) ) {

	function wplanner_fb_sanitize_background_repeat( $value ) {
	
		$recognized = wplanner_fb_recognized_background_repeat();
		if ( array_key_exists( $value, $recognized ) ) {
			return $value;
		}
		return apply_filters( 'wplanner_fb_default_background_repeat', current( $recognized ) );
	}
}
add_filter( 'wplanner_fb_background_repeat', 'wplanner_fb_sanitize_background_repeat' );


if ( !function_exists( 'wplanner_fb_sanitize_background_position' ) ) {

	function wplanner_fb_sanitize_background_position( $value ) {
	
		$recognized = wplanner_fb_recognized_background_position();
		if ( array_key_exists( $value, $recognized ) ) {
			return $value;
		}
		return apply_filters( 'wplanner_fb_default_background_position', current( $recognized ) );
	}
}
add_filter( 'wplanner_fb_background_position', 'wplanner_fb_sanitize_background_position' );


if ( !function_exists( 'wplanner_fb_sanitize_background_attachment' ) ) {

	function wplanner_fb_sanitize_background_attachment( $value ) {
	
		$recognized = wplanner_fb_recognized_background_attachment();
		if ( array_key_exists( $value, $recognized ) ) {
			return $value;
		}
		return apply_filters( 'wplanner_fb_default_background_attachment', current( $recognized ) );
	}
}
add_filter( 'wplanner_fb_background_attachment', 'wplanner_fb_sanitize_background_attachment' );


/* Typography */
if ( !function_exists( 'wplanner_fb_sanitize_typography' ) ) {

	function wplanner_fb_sanitize_typography( $input ) {

		$output = wp_parse_args( $input, array(
			'size'  => '',
			'face'  => '',
			'style' => '',
			'color' => ''
		) );

		$output['size']  = apply_filters( 'wplanner_fb_font_size', $output['size'] );
		$output['face']  = apply_filters( 'wplanner_fb_font_face', $output['face'] );
		$output['style'] = apply_filters( 'wplanner_fb_font_style', $output['style'] );
		$output['color'] = apply_filters( 'wplanner_fb_color', $output['color'] );

		return $output;
	}
}
add_filter( 'wplanner_fb_sanitize_typography', 'wplanner_fb_sanitize_typography' );


if ( !function_exists( 'wplanner_fb_sanitize_font_size' ) ) {

	function wplanner_fb_sanitize_font_size( $value ) {

		$recognized = wplanner_fb_recognized_font_sizes();
		$value = preg_replace('/px/','', $value);
		if ( in_array( (int) $value, $recognized ) ) {
			return (int) $value;
		}
		return (int) apply_filters( 'wplanner_fb_default_font_size', $recognized );
	}
}
add_filter( 'wplanner_fb_font_face', 'wplanner_fb_sanitize_font_face' );


if ( !function_exists( 'wplanner_fb_sanitize_font_style' ) ) {

	function wplanner_fb_sanitize_font_style( $value ) {

		$recognized = wplanner_fb_recognized_font_styles();
		if ( array_key_exists( $value, $recognized ) ) {
			return $value;
		}
		return apply_filters( 'wplanner_fb_default_font_style', current( $recognized ) );
	}
}
add_filter( 'wplanner_fb_font_style', 'wplanner_fb_sanitize_font_style' );


if ( !function_exists( 'wplanner_fb_sanitize_font_face' ) ) {

	function wplanner_fb_sanitize_font_face( $value ) {
	
		$recognized = wplanner_fb_recognized_font_faces();
		if ( array_key_exists( $value, $recognized ) ) {
			return $value;
		}
		return apply_filters( 'wplanner_fb_default_font_face', current( $recognized ) );
	}
}
add_filter( 'wplanner_fb_font_face', 'wplanner_fb_sanitize_font_face' );


/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
if ( !function_exists( 'wplanner_fb_recognized_background_repeat' ) ) {
 
	function wplanner_fb_recognized_background_repeat() {
	
		$default = array(
			'no-repeat' => __( 'No Repeat', WP_PLANNER_TEXTDOMAIN ),
			'repeat-x'  => __( 'Repeat Horizontally', WP_PLANNER_TEXTDOMAIN ),
			'repeat-y'  => __( 'Repeat Vertically', WP_PLANNER_TEXTDOMAIN ),
			'repeat'    => __( 'Repeat All', WP_PLANNER_TEXTDOMAIN )
			);
		return apply_filters( 'wplanner_fb_recognized_background_repeat', $default );
	}
}


/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
if ( !function_exists( 'wplanner_fb_recognized_background_position' ) ) {
 
	function wplanner_fb_recognized_background_position() {
	
		$default = array(
			'top left'      => __( 'Top Left', WP_PLANNER_TEXTDOMAIN ),
			'top center'    => __( 'Top Center', WP_PLANNER_TEXTDOMAIN ),
			'top right'     => __( 'Top Right', WP_PLANNER_TEXTDOMAIN ),
			'center left'   => __( 'Middle Left', WP_PLANNER_TEXTDOMAIN ),
			'center center' => __( 'Middle Center', WP_PLANNER_TEXTDOMAIN ),
			'center right'  => __( 'Middle Right', WP_PLANNER_TEXTDOMAIN ),
			'bottom left'   => __( 'Bottom Left', WP_PLANNER_TEXTDOMAIN ),
			'bottom center' => __( 'Bottom Center', WP_PLANNER_TEXTDOMAIN ),
			'bottom right'  => __( 'Bottom Right', WP_PLANNER_TEXTDOMAIN )
			);
		return apply_filters( 'wplanner_fb_recognized_background_position', $default );
	}
}


/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
if ( !function_exists( 'wplanner_fb_recognized_background_attachment' ) ) {
 
	function wplanner_fb_recognized_background_attachment() {
	
		$default = array(
			'scroll' => __( 'Scroll Normally', WP_PLANNER_TEXTDOMAIN ),
			'fixed'  => __( 'Fixed in Place', WP_PLANNER_TEXTDOMAIN )
			);
		return apply_filters( 'wplanner_fb_recognized_background_attachment', $default );
	}
}


/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */
if ( !function_exists( 'wplanner_fb_sanitize_hex' ) ) {
 
	function wplanner_fb_sanitize_hex( $hex, $default = '' ) {
	
		if ( wplanner_fb_validate_hex( $hex ) ) {
			return $hex;
		}
		return $default;
	}
}


/**
 * Get recognized font sizes.
 *
 * Returns an indexed array wplanner_fb all recognized font sizes.
 * Values are integers and represent a range wplanner_fb sizes from
 * smallest to largest.
 *
 * @return   array
 */
if ( !function_exists( 'wplanner_fb_recognized_font_sizes' ) ) {
 
	function wplanner_fb_recognized_font_sizes() {
	
		$sizes = range( 9, 71 );
		$sizes = apply_filters( 'wplanner_fb_recognized_font_sizes', $sizes );
		$sizes = array_map( 'absint', $sizes );
		return $sizes;
	}
}


/**
 * Get recognized font faces.
 *
 * Returns an array wplanner_fb all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
if ( !function_exists( 'wplanner_fb_recognized_font_faces' ) ) {

	function wplanner_fb_recognized_font_faces() {
	
		$default = array(
			'arial'     => __( 'Arial', WP_PLANNER_TEXTDOMAIN ),
			'verdana'   => __( 'Verdana, Geneva', WP_PLANNER_TEXTDOMAIN ),
			'trebuchet' => __( 'Trebuchet', WP_PLANNER_TEXTDOMAIN ),
			'georgia'   => __( 'Georgia', WP_PLANNER_TEXTDOMAIN ),
			'times'     => __( 'Times New Roman', WP_PLANNER_TEXTDOMAIN ),
			'tahoma'    => __( 'Tahoma, Geneva', WP_PLANNER_TEXTDOMAIN ),
			'palatino'  => __( 'Palatino', WP_PLANNER_TEXTDOMAIN ),
			'helvetica' => __( 'Helvetica*', WP_PLANNER_TEXTDOMAIN )
			);
		return apply_filters( 'wplanner_fb_recognized_font_faces', $default );
	}
}


/**
 * Get recognized font styles.
 *
 * Returns an array wplanner_fb all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
if ( !function_exists( 'wplanner_fb_recognized_font_styles' ) ) {

	function wplanner_fb_recognized_font_styles() {
	
		$default = array(
			'normal'      => __( 'Normal', WP_PLANNER_TEXTDOMAIN ),
			'italic'      => __( 'Italic', WP_PLANNER_TEXTDOMAIN ),
			'bold'        => __( 'Bold', WP_PLANNER_TEXTDOMAIN ),
			'bold italic' => __( 'Bold Italic', WP_PLANNER_TEXTDOMAIN )
			);
		return apply_filters( 'wplanner_fb_recognized_font_styles', $default );
	}
}


/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */
if ( !function_exists( 'wplanner_fb_validate_hex' ) ) {
 
	function wplanner_fb_validate_hex( $hex ) {
	
		$hex = trim( $hex );
		/* Strip recognized prefixes. */
		if ( 0 === strpos( $hex, '#' ) ) {
			$hex = substr( $hex, 1 );
		}
		elseif ( 0 === strpos( $hex, '%23' ) ) {
			$hex = substr( $hex, 3 );
		}
		/* Regex match. */
		if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
			return false;
		}
		else {
			return true;
		}
	}
}