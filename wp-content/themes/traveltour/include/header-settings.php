<?php

	add_filter('gdlr_core_enable_header_post_type', 'traveltour_gdlr_core_enable_header_post_type');
	if( !function_exists('traveltour_gdlr_core_enable_header_post_type') ){
		function traveltour_gdlr_core_enable_header_post_type( $args ){
			return true;
		}
	}
	
	add_filter('gdlr_core_header_options', 'traveltour_gdlr_core_header_options', 10, 2);
	if( !function_exists('traveltour_gdlr_core_header_options') ){
		function traveltour_gdlr_core_header_options( $options, $with_default = true ){

			// get option
			$options = array(
				'top-bar' => traveltour_top_bar_options(),
				'top-bar-social' => traveltour_top_bar_social_options(),			
				'header' => traveltour_header_options(),
				'logo' => traveltour_logo_options(),
				'navigation' => traveltour_navigation_options(), 
				'fixed-navigation' => traveltour_fixed_navigation_options(), 
			);

			// set default
			if( $with_default ){
				foreach( $options as $slug => $option ){
					foreach( $option['options'] as $key => $value ){
						$options[$slug]['options'][$key]['default'] = traveltour_get_option('general', $key);
					}
				}
			} 
			
			return $options;
		}
	}
	
	add_filter('gdlr_core_header_color_options', 'traveltour_gdlr_core_header_color_options', 10, 2);
	if( !function_exists('traveltour_gdlr_core_header_color_options') ){
		function traveltour_gdlr_core_header_color_options( $options, $with_default = true ){

			$options = array(
				'header-color' => traveltour_header_color_options(), 
				'navigation-menu-color' => traveltour_navigation_color_options(), 
			);

			// set default
			if( $with_default ){
				foreach( $options as $slug => $option ){
					foreach( $option['options'] as $key => $value ){
						$options[$slug]['options'][$key]['default'] = traveltour_get_option('color', $key);
					}
				}
			}

			return $options;
		}
	}

	add_action('wp_head', 'traveltour_set_custom_header');
	if( !function_exists('traveltour_set_custom_header') ){
		function traveltour_set_custom_header(){
			traveltour_get_option('general', 'layout', '');
			
			$header_id = get_post_meta(get_the_ID(), 'gdlr_core_custom_header_id', true);
			if( empty($header_id) ){
				$header_id = traveltour_get_option('general', 'custom-header', '');
			}

			if( !empty($header_id) ){
				$option = 'traveltour_general';
				$header_options = get_post_meta($header_id, 'gdlr-core-header-settings', true);

				if( !empty($header_options) ){
					foreach( $header_options as $key => $value ){
						$GLOBALS[$option][$key] = $value;
					}
				}

				$header_css = get_post_meta($header_id, 'gdlr-core-custom-header-css', true);
				if( !empty($header_css) ){
					if( get_post_type() == 'page' ){
						$header_css = str_replace('.gdlr-core-page-id', '.page-id-' . get_the_ID(), $header_css);
					}else{
						$header_css = str_replace('.gdlr-core-page-id', '.postid-' . get_the_ID(), $header_css);
					}
					echo '<style type="text/css" >' . $header_css . '</style>';
				}
				

			}
		} // traveltour_set_custom_header
	}

	// override menu on page option
	add_filter('wp_nav_menu_args', 'traveltour_wp_nav_menu_args');
	if( !function_exists('traveltour_wp_nav_menu_args') ){
		function traveltour_wp_nav_menu_args($args){

			$traveltour_locations = array('main_menu', 'right_menu', 'top_bar_menu', 'mobile_menu');
			if( !empty($args['theme_location']) && in_array($args['theme_location'], $traveltour_locations) ){
				$menu_id = get_post_meta(get_the_ID(), 'gdlr-core-location-' . $args['theme_location'], true);
				
				if( !empty($menu_id) ){
					$args['menu'] = $menu_id;
					$args['theme_location'] = '';
				}
			}

			return $args;
		}
	}

	if( !function_exists('traveltour_top_bar_options') ){
		function traveltour_top_bar_options(){
			return array(
				'title' => esc_html__('Top Bar', 'traveltour'),
				'options' => array(

					'enable-top-bar' => array(
						'title' => esc_html__('Enable Top Bar', 'traveltour'),
						'type' => 'checkbox',
					),
					'enable-top-bar-divider' => array(
						'title' => esc_html__('Enable Top Bar Divider', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'disable'
					),
					'enable-top-bar-left-on-mobile' => array(
						'title' => esc_html__('Enable Top Bar Left On Mobile', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'disable'
					),
					'enable-top-bar-right-on-mobile' => array(
						'title' => esc_html__('Enable Top Bar Right On Mobile', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					'top-bar-width' => array(
						'title' => esc_html__('Top Bar Width', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'boxed' => esc_html__('Boxed ( Within Container )', 'traveltour'),
							'full' => esc_html__('Full', 'traveltour'),
							'custom' => esc_html__('Custom', 'traveltour'),
						),
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'wpml-flag-type' => array(
						'title' => esc_html__('WPML Flag Type (If Enabled)', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'icon' => esc_html__('Icon', 'traveltour'),
							'dropdown' => esc_html__('Dropdown', 'traveltour'),
						)
					),
					'top-bar-width-pixel' => array(
						'title' => esc_html__('Top Bar Width Pixel', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'default' => '1140px',
						'condition' => array( 'enable-top-bar' => 'enable', 'top-bar-width' => 'custom' ),
						'selector' => '.traveltour-top-bar-container.traveltour-top-bar-custom-container{ max-width: #gdlr#; }'
					),
					'top-bar-full-side-padding' => array(
						'title' => esc_html__('Top Bar Full ( Left/Right ) Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '100',
						'data-type' => 'pixel',
						'default' => '15px',
						'selector' => '.traveltour-top-bar-container.traveltour-top-bar-full{ padding-right: #gdlr#; padding-left: #gdlr#; }',
						'condition' => array( 'enable-top-bar' => 'enable', 'top-bar-width' => 'full' )
					),
					'top-bar-left-text' => array(
						'title' => esc_html__('Top Bar Left Text', 'traveltour'),
						'type' => 'textarea',
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'enable-top-bar-right-login' => array(
						'title' => esc_html__('Enable Top Bar Login', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'enable' => esc_html__('Right (With Border)', 'traveltour'),
							'left-no-border' => esc_html__('Left (No Border)', 'traveltour'),
							'disable' => esc_html__('Disable', 'traveltour')
						),
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'top-bar-right-text' => array(
						'title' => esc_html__('Top Bar Right Text', 'traveltour'),
						'type' => 'textarea',
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'top-bar-top-padding' => array(
						'title' => esc_html__('Top Bar Top Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '200',
 						'default' => '10px',
						'selector' => '.traveltour-top-bar{ padding-top: #gdlr#; }' . 
							'.traveltour-top-bar-right > div:before{ padding-top: #gdlr#; margin-top: -#gdlr#; }',
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'top-bar-bottom-padding' => array(
						'title' => esc_html__('Top Bar Bottom Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '200',
						'default' => '10px',
						'selector' => '.traveltour-top-bar{ padding-bottom: #gdlr#; }'  . 
							'.traveltour-top-bar-right > div:before{ padding-bottom: #gdlr#; margin-bottom: -#gdlr#; }',
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'top-bar-text-size' => array(
						'title' => esc_html__('Top Bar Text Size', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'default' => '15px',
						'selector' => '.traveltour-top-bar{ font-size: #gdlr#; }',
						'condition' => array( 'enable-top-bar' => 'enable' )
					),
					'top-bar-bottom-border' => array(
						'title' => esc_html__('Top Bar Bottom Border', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '10',
						'default' => '0',
						'selector' => '.traveltour-top-bar{ border-bottom-width: #gdlr#; }',
						'condition' => array( 'enable-top-bar' => 'enable' )
					),

				)
			);
		}
	}

	if( !function_exists('traveltour_top_bar_social_options') ){
		function traveltour_top_bar_social_options(){
			return array(
				'title' => esc_html__('Top Bar Social', 'traveltour'),
				'options' => array(
					'enable-top-bar-social' => array(
						'title' => esc_html__('Enable Top Bar Social', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					'top-bar-social-icon-type' => array(
						'title' => esc_html__('Icon Type', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'font-awesome' => esc_html__('Font Awesome', 'traveltour'),
							'font-awesome5' => esc_html__('Font Awesome 5', 'traveltour'),
						)
					),
					'top-bar-social-tiktok' => array(
						'title' => esc_html__('Top Bar Social Tiktok Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable', 'top-bar-social-icon-type' => 'font-awesome5' )
					),
					'top-bar-social-twitch' => array(
						'title' => esc_html__('Top Bar Social Twitch Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-discord' => array(
						'title' => esc_html__('Top Bar Social Discord Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable', 'top-bar-social-icon-type' => 'font-awesome5' )
					),
					'top-bar-social-delicious' => array(
						'title' => esc_html__('Top Bar Social Delicious Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-email' => array(
						'title' => esc_html__('Top Bar Social Email Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-deviantart' => array(
						'title' => esc_html__('Top Bar Social Deviantart Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-digg' => array(
						'title' => esc_html__('Top Bar Social Digg Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-facebook' => array(
						'title' => esc_html__('Top Bar Social Facebook Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-flickr' => array(
						'title' => esc_html__('Top Bar Social Flickr Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-lastfm' => array(
						'title' => esc_html__('Top Bar Social Lastfm Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-linkedin' => array(
						'title' => esc_html__('Top Bar Social Linkedin Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-pinterest' => array(
						'title' => esc_html__('Top Bar Social Pinterest Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-rss' => array(
						'title' => esc_html__('Top Bar Social RSS Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-skype' => array(
						'title' => esc_html__('Top Bar Social Skype Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-stumbleupon' => array(
						'title' => esc_html__('Top Bar Social Stumbleupon Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-tumblr' => array(
						'title' => esc_html__('Top Bar Social Tumblr Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-twitter' => array(
						'title' => esc_html__('Top Bar Social Twitter Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-vimeo' => array(
						'title' => esc_html__('Top Bar Social Vimeo Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-youtube' => array(
						'title' => esc_html__('Top Bar Social Youtube Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-instagram' => array(
						'title' => esc_html__('Top Bar Social Instagram Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
					'top-bar-social-snapchat' => array(
						'title' => esc_html__('Top Bar Social Snapchat Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-top-bar-social' => 'enable' )
					),
				)
			);
		}
	}

	if( !function_exists('traveltour_header_options') ){
		function traveltour_header_options(){
			return array(
				'title' => esc_html__('Header', 'traveltour'),
				'options' => array(

					'header-style' => array(
						'title' => esc_html__('Header Style', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'plain' => esc_html__('Plain', 'traveltour'),
							'bar' => esc_html__('Bar', 'traveltour'),
							'boxed' => esc_html__('Boxed', 'traveltour'),
							'side' => esc_html__('Side Menu', 'traveltour'),
							'side-toggle' => esc_html__('Side Menu Toggle', 'traveltour'),
						),
						'default' => 'plain',
					),
					'header-plain-style' => array(
						'title' => esc_html__('Header Plain Style', 'traveltour'),
						'type' => 'radioimage',
						'options' => array(
							'menu-right' => get_template_directory_uri() . '/images/header/plain-menu-right.jpg',
							'center-logo' => get_template_directory_uri() . '/images/header/plain-center-logo.jpg',
							'center-menu' => get_template_directory_uri() . '/images/header/plain-center-menu.jpg',
							'splitted-menu' => get_template_directory_uri() . '/images/header/plain-splitted-menu.jpg',
						),
						'default' => 'menu-right',
						'condition' => array( 'header-style' => 'plain' ),
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'header-plain-bottom-border' => array(
						'title' => esc_html__('Plain Header Bottom Border', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '10',
						'default' => '0',
						'selector' => '.traveltour-header-style-plain{ border-bottom-width: #gdlr#; }',
						'condition' => array( 'header-style' => array('plain') )
					),
					'header-bar-navigation-align' => array(
						'title' => esc_html__('Header Bar Style', 'traveltour'),
						'type' => 'radioimage',
						'options' => array(
							'left' => get_template_directory_uri() . '/images/header/bar-left.jpg',
							'center' => get_template_directory_uri() . '/images/header/bar-center.jpg',
							'center-logo' => get_template_directory_uri() . '/images/header/bar-center-logo.jpg',
						),
						'default' => 'center',
						'condition' => array( 'header-style' => 'bar' ),
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'header-background-style' => array(
						'title' => esc_html__('Header/Navigation Background Style', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'solid' => esc_html__('Solid', 'traveltour'),
							'transparent' => esc_html__('Transparent', 'traveltour'),
						),
						'default' => 'solid',
						'condition' => array( 'header-style' => array('plain', 'bar') )
					),
					'top-bar-background-opacity' => array(
						'title' => esc_html__('Top Bar Background Opacity', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'opacity',
						'default' => '50',
						'condition' => array( 'header-style' => 'plain', 'header-background-style' => 'transparent' ),
						'selector' => '.traveltour-header-background-transparent .traveltour-top-bar-background{ opacity: #gdlr#; }'
					),
					'header-background-opacity' => array(
						'title' => esc_html__('Header Background Opacity', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'opacity',
						'default' => '50',
						'condition' => array( 'header-style' => 'plain', 'header-background-style' => 'transparent' ),
						'selector' => '.traveltour-header-background-transparent .traveltour-header-background{ opacity: #gdlr#; }'
					),
					'navigation-background-opacity' => array(
						'title' => esc_html__('Navigation Background Opacity', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'opacity',
						'default' => '50',
						'condition' => array( 'header-style' => 'bar', 'header-background-style' => 'transparent' ),
						'selector' => '.traveltour-navigation-bar-wrap.traveltour-style-transparent .traveltour-navigation-background{ opacity: #gdlr#; }'
					),
					'header-boxed-style' => array(
						'title' => esc_html__('Header Boxed Style', 'traveltour'),
						'type' => 'radioimage',
						'options' => array(
							'menu-right' => get_template_directory_uri() . '/images/header/boxed-menu-right.jpg',
							'center-menu' => get_template_directory_uri() . '/images/header/boxed-center-menu.jpg',
							'splitted-menu' => get_template_directory_uri() . '/images/header/boxed-splitted-menu.jpg',
						),
						'default' => 'menu-right',
						'condition' => array( 'header-style' => 'boxed' ),
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'boxed-top-bar-background-opacity' => array(
						'title' => esc_html__('Top Bar Background Opacity', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'opacity',
						'default' => '0',
						'condition' => array( 'header-style' => 'boxed' ),
						'selector' => '.traveltour-header-boxed-wrap .traveltour-top-bar-background{ opacity: #gdlr#; }'
					),
					'boxed-top-bar-background-extend' => array(
						'title' => esc_html__('Top Bar Background Extend ( Bottom )', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0px',
						'data-max' => '200px',
						'default' => '0px',
						'condition' => array( 'header-style' => 'boxed' ),
						'selector' => '.traveltour-header-boxed-wrap .traveltour-top-bar-background{ margin-bottom: -#gdlr#; }'
					),
					'boxed-header-top-margin' => array(
						'title' => esc_html__('Header Top Margin', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0px',
						'data-max' => '200px',
						'default' => '0px',
						'condition' => array( 'header-style' => 'boxed' ),
						'selector' => '.traveltour-header-style-boxed{ margin-top: #gdlr#; }'
					),
					'header-side-style' => array(
						'title' => esc_html__('Header Side Style', 'traveltour'),
						'type' => 'radioimage',
						'options' => array(
							'top-left' => get_template_directory_uri() . '/images/header/side-top-left.jpg',
							'middle-left' => get_template_directory_uri() . '/images/header/side-middle-left.jpg',
							'top-right' => get_template_directory_uri() . '/images/header/side-top-right.jpg',
							'middle-right' => get_template_directory_uri() . '/images/header/side-middle-right.jpg',
						),
						'default' => 'top-left',
						'condition' => array( 'header-style' => 'side' ),
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'header-side-align' => array(
						'title' => esc_html__('Header Side Text Align', 'traveltour'),
						'type' => 'radioimage',
						'options' => 'text-align',
						'default' => 'left',
						'condition' => array( 'header-style' => 'side' )
					),
					'header-side-toggle-style' => array(
						'title' => esc_html__('Header Side Toggle Style', 'traveltour'),
						'type' => 'radioimage',
						'options' => array(
							'left' => get_template_directory_uri() . '/images/header/side-toggle-left.jpg',
							'right' => get_template_directory_uri() . '/images/header/side-toggle-right.jpg',
						),
						'default' => 'left',
						'condition' => array( 'header-style' => 'side-toggle' ),
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'header-side-toggle-menu-type' => array(
						'title' => esc_html__('Header Side Toggle Menu Type', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'left' => esc_html__('Left Slide Menu', 'traveltour'),
							'right' => esc_html__('Right Slide Menu', 'traveltour'),
							'overlay' => esc_html__('Overlay Menu', 'traveltour'),
						),
						'default' => 'overlay',
						'condition' => array( 'header-style' => 'side-toggle' )
					),
					'header-side-toggle-display-logo' => array(
						'title' => esc_html__('Display Logo', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable',
						'condition' => array( 'header-style' => 'side-toggle' )
					),
					'header-width' => array(
						'title' => esc_html__('Header Width', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'boxed' => esc_html__('Boxed ( Within Container )', 'traveltour'),
							'full' => esc_html__('Full', 'traveltour'),
							'custom' => esc_html__('Custom', 'traveltour'),
						),
						'condition' => array('header-style'=> array('plain', 'bar', 'boxed'))
					),
					'header-width-pixel' => array(
						'title' => esc_html__('Header Width Pixel', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'default' => '1140px',
						'condition' => array('header-style'=> array('plain', 'bar', 'boxed'), 'header-width' => 'custom'),
						'selector' => '.traveltour-header-container.traveltour-header-custom-container{ max-width: #gdlr#; }'
					),
					'header-full-side-padding' => array(
						'title' => esc_html__('Header Full ( Left/Right ) Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '100',
						'data-type' => 'pixel',
						'default' => '15px',
						'selector' => '.traveltour-header-container.traveltour-header-full{ padding-right: #gdlr#; padding-left: #gdlr#; }',
						'condition' => array('header-style'=> array('plain', 'bar', 'boxed'), 'header-width'=>'full')
					),
					'boxed-header-frame-radius' => array(
						'title' => esc_html__('Header Frame Radius', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '3px',
						'condition' => array( 'header-style' => 'boxed' ),
						'selector' => '.traveltour-header-boxed-wrap .traveltour-header-background{ border-radius: #gdlr#; -moz-border-radius: #gdlr#; -webkit-border-radius: #gdlr#; }'
					),
					'boxed-header-content-padding' => array(
						'title' => esc_html__('Header Content ( Left/Right ) Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '100',
						'data-type' => 'pixel',
						'default' => '30px',
						'selector' => '.traveltour-header-style-boxed .traveltour-header-container-item{ padding-left: #gdlr#; padding-right: #gdlr#; }' . 
							'.traveltour-navigation-right{ right: #gdlr#; } .traveltour-navigation-left{ left: #gdlr#; }',
						'condition' => array( 'header-style' => 'boxed' )
					),
					'navigation-text-top-margin' => array(
						'title' => esc_html__('Navigation Text Top Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '200',
						'default' => '0px',
						'condition' => array( 'header-style' => 'plain', 'header-plain-style' => 'splitted-menu' ),
						'selector' => '.traveltour-header-style-plain.traveltour-style-splitted-menu .traveltour-navigation .sf-menu > li > a{ padding-top: #gdlr#; } ' .
							'.traveltour-header-style-plain.traveltour-style-splitted-menu .traveltour-main-menu-left-wrap,' .
							'.traveltour-header-style-plain.traveltour-style-splitted-menu .traveltour-main-menu-right-wrap{ padding-top: #gdlr#; }'
					),
					'navigation-text-top-margin-boxed' => array(
						'title' => esc_html__('Navigation Text Top Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '200',
						'default' => '0px',
						'condition' => array( 'header-style' => 'boxed', 'header-boxed-style' => 'splitted-menu' ),
						'selector' => '.traveltour-header-style-boxed.traveltour-style-splitted-menu .traveltour-navigation .sf-menu > li > a{ padding-top: #gdlr#; } ' .
							'.traveltour-header-style-boxed.traveltour-style-splitted-menu .traveltour-main-menu-left-wrap,' .
							'.traveltour-header-style-boxed.traveltour-style-splitted-menu .traveltour-main-menu-right-wrap{ padding-top: #gdlr#; }'
					),
					'navigation-text-side-spacing' => array(
						'title' => esc_html__('Navigation Text Side ( Left / Right ) Spaces', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '30',
						'data-type' => 'pixel',
						'default' => '13px',
						'selector' => '.traveltour-navigation .sf-menu > li{ padding-left: #gdlr#; padding-right: #gdlr#; }',
						'condition' => array( 'header-style' => array('plain', 'bar', 'boxed') )
					),
					'navigation-left-offset' => array(
						'title' => esc_html__('Navigation Left Offset Spaces', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '0',
						'selector' => '.traveltour-navigation .traveltour-main-menu{ margin-left: #gdlr#; }'
					),
					'navigation-slide-bar' => array(
						'title' => esc_html__('Navigation Slide Bar', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'disable' => esc_html__('Disable', 'traveltour'),
							'enable' => esc_html__('Bar With Triangle Style', 'traveltour'),
							'style-2' => esc_html__('Bar Style', 'traveltour'),
							'style-2-left' => esc_html__('Bar Style Left', 'traveltour'),
							'style-dot' => esc_html__('Dot Style', 'traveltour')
						),
						'default' => 'enable',
						'condition' => array( 'header-style' => array('plain', 'bar', 'boxed') )
					),
					'navigation-slide-bar-width' => array(
						'title' => esc_html__('Navigation Slide Bar Width', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'condition' => array( 'header-style' => array('plain', 'bar', 'bar2', 'boxed'), 'navigation-slide-bar' => array('style-2', 'style-2-left') )
					),
					'navigation-slide-bar-height' => array(
						'title' => esc_html__('Navigation Slide Bar Height', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'selector' => '.traveltour-navigation .traveltour-navigation-slide-bar-style-2{ border-bottom-width: #gdlr#; }',
						'condition' => array( 'header-style' => array('plain', 'bar', 'bar2', 'boxed'), 'navigation-slide-bar' => array('style-2', 'style-2-left') )
					),
					'navigation-slide-bar-top-margin' => array(
						'title' => esc_html__('Navigation Slide Bar Top Margin', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '',
						'selector' => '.traveltour-navigation .traveltour-navigation-slide-bar{ margin-top: #gdlr#; }',
						'condition' => array( 'header-style' => array('plain', 'bar', 'boxed'), 'navigation-slide-bar' => array('enable', 'style-2', 'style-2-left', 'style-dot') )
					),
					'side-header-width-pixel' => array(
						'title' => esc_html__('Header Width Pixel', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '600',
						'default' => '340px',
						'condition' => array('header-style' => array('side', 'side-toggle')),
						'selector' => '.traveltour-header-side-nav{ width: #gdlr#; }' . 
							'.traveltour-header-side-content.traveltour-style-left{ margin-left: #gdlr#; }' .
							'.traveltour-header-side-content.traveltour-style-right{ margin-right: #gdlr#; }'
					),
					'side-header-side-padding' => array(
						'title' => esc_html__('Header Side Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '200',
						'default' => '70px',
						'condition' => array('header-style' => 'side'),
						'selector' => '.traveltour-header-side-nav.traveltour-style-side{ padding-left: #gdlr#; padding-right: #gdlr#; }' . 
							'.traveltour-header-side-nav.traveltour-style-left .sf-vertical > li > ul.sub-menu{ padding-left: #gdlr#; }' .
							'.traveltour-header-side-nav.traveltour-style-right .sf-vertical > li > ul.sub-menu{ padding-right: #gdlr#; }'
					),
					'navigation-text-top-spacing' => array(
						'title' => esc_html__('Navigation Text Top / Bottom Spaces', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '40',
						'data-type' => 'pixel',
						'default' => '16px',
						'selector' => ' .traveltour-navigation .sf-vertical > li{ padding-top: #gdlr#; padding-bottom: #gdlr#; }',
						'condition' => array( 'header-style' => array('side') )
					),
					'logo-right-text' => array(
						'title' => esc_html__('Header Right Text', 'traveltour'),
						'type' => 'textarea',
						'condition' => array('header-style' => 'bar'),
					),
					'logo-right-text-top-padding' => array(
						'title' => esc_html__('Header Right Text Top Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-type' => 'pixel',
						'data-min' => '0',
						'data-max' => '200',
						'default' => '30px',
						'condition' => array('header-style' => 'bar'),
						'selector' => '.traveltour-header-style-bar .traveltour-logo-right-text{ padding-top: #gdlr#; }'
					),

				)
			);
		}
	}

	if( !function_exists('traveltour_logo_options') ){
		function traveltour_logo_options(){
			return array(
				'title' => esc_html__('Logo', 'traveltour'),
				'options' => array(
					'logo' => array(
						'title' => esc_html__('Logo', 'traveltour'),
						'type' => 'upload'
					),
					'logo2x' => array(
						'title' => esc_html__('Logo 2x (Retina)', 'traveltour'),
						'type' => 'upload'
					),
					'mobile-logo' => array(
						'title' => esc_html__('Mobile Logo', 'traveltour'),
						'type' => 'upload',
						'description' => esc_html__('Leave this option blank to use the same logo.', 'traveltour'),
					),
					'mobile-user-login' => array(
						'title' => esc_html__('Mobile User Login', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					'logo-top-padding' => array(
						'title' => esc_html__('Logo Top Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '200',
						'data-type' => 'pixel',
						'default' => '20px',
						'selector' => '.traveltour-logo{ padding-top: #gdlr#; }',
						'description' => esc_html__('This option will be omitted on splitted menu option.', 'traveltour'),
					),
					'logo-bottom-padding' => array(
						'title' => esc_html__('Logo Bottom Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '200',
						'data-type' => 'pixel',
						'default' => '20px',
						'selector' => '.traveltour-logo{ padding-bottom: #gdlr#; }',
						'description' => esc_html__('This option will be omitted on splitted menu option.', 'traveltour'),
					),
					'logo-left-padding' => array(
						'title' => esc_html__('Logo Left Padding', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'selector' => '.traveltour-logo.traveltour-item-pdlr{ padding-left: #gdlr#; }',
						'description' => esc_html__('Leave this field blank for default value.', 'traveltour'),
					),
					'max-logo-width' => array(
						'title' => esc_html__('Max Logo Width', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '200px',
						'selector' => '.traveltour-logo-inner{ max-width: #gdlr#; }'
					),
				
				)
			);
		}
	}

	if( !function_exists('traveltour_navigation_options') ){
		function traveltour_navigation_options(){
			return array(
				'title' => esc_html__('Navigation', 'traveltour'),
				'options' => array(
					'main-navigation-top-padding' => array(
						'title' => esc_html__('Main Navigation Top Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '200',
						'data-type' => 'pixel',
						'default' => '25px',
						'selector' => '.traveltour-navigation{ padding-top: #gdlr#; }' . 
							'.traveltour-navigation-top{ top: #gdlr#; }'
					),
					'main-navigation-bottom-padding' => array(
						'title' => esc_html__('Main Navigation Bottom Padding', 'traveltour'),
						'type' => 'fontslider',
						'data-min' => '0',
						'data-max' => '200',
						'data-type' => 'pixel',
						'default' => '20px',
						'selector' => '.traveltour-navigation .sf-menu > li > a{ padding-bottom: #gdlr#; }'
					),
					'main-navigation-right-padding' => array(
						'title' => esc_html__('Main Navigation Right Padding', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'selector' => '.traveltour-navigation.traveltour-item-pdlr{ padding-right: #gdlr#; }' . 
							'.traveltour-header-style-plain.traveltour-style-center-menu .traveltour-main-menu-right-wrap{ margin-right: #gdlr#; }',
						'description' => esc_html__('Leave this field blank for default value.', 'traveltour'),
					),
					'enable-main-navigation-submenu-indicator' => array(
						'title' => esc_html__('Enable Main Navigation Submenu Indicator', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'disable',
					),
					'navigation-right-top-margin' => array(
						'title' => esc_html__('Navigation Right ( search/cart/button ) Top Margin ', 'traveltour'),
						'type' => 'text',
						'data-input-type' => 'pixel',
						'data-type' => 'pixel',
						'selector' => '.traveltour-main-menu-right-wrap{ margin-top: #gdlr# !important; }'
					),
					'enable-main-navigation-search' => array(
						'title' => esc_html__('Enable Main Navigation Search', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable',
					),
					'enable-main-navigation-cart' => array(
						'title' => esc_html__('Enable Main Navigation Cart ( Woocommerce )', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable',
						'description' => esc_html__('The icon only shows if the woocommerce plugin is activated', 'traveltour')
					),
					'enable-main-navigation-login' => array(
						'title' => esc_html__('Enable Main Navigation Login', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'disable',
					),
					'enable-main-navigation-sign-up' => array(
						'title' => esc_html__('Enable Main Navigation Sign Up', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable',
						'condition' => array( 'enable-main-navigation-login' => 'enable' ) 
					),
					'main-navigation-login-style' => array(
						'title' => esc_html__('Enable Main Navigation Login Style', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'' => esc_html__('Default', 'traveltour'),
							'button' => esc_html__('Button', 'traveltour'),
							'full-button' => esc_html__('Full Button', 'traveltour')
						),
						'condition' => array( 'enable-main-navigation-login' => 'enable' ) 
					),
					'main-navigation-currency-button-background' => array(
						'title' => esc_html__('Main Navigation Currency Button Background', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#333333',
						'selector' => '.traveltour-navigation-login-full-button .tourmaster-currency-switcher{ background: #gdlr#; }',
						'condition' => array( 'enable-main-navigation-login' => 'enable', 'main-navigation-login-style' => array('full-button') ) 
					),
					'main-navigation-currency-button-text' => array(
						'title' => esc_html__('Main Navigation Currency Button Text', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#fff',
						'selector' => '.traveltour-navigation-login-full-button .tourmaster-currency-switcher .tourmaster-head{ color: #gdlr#; }',
						'condition' => array( 'enable-main-navigation-login' => 'enable', 'main-navigation-login-style' => array('full-button') ) 
					),
					'main-navigation-login-button-background' => array(
						'title' => esc_html__('Main Navigation Login Button Background', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#0a0a0a',
						'selector' => '.traveltour-navigation-login-full-button .tourmaster-user-top-bar, .traveltour-navigation-login-button .tourmaster-user-top-bar.tourmaster-guest{ background: #gdlr#; }',
						'condition' => array( 'enable-main-navigation-login' => 'enable', 'main-navigation-login-style' => array('full-button', 'button') ) 
					),
					'main-navigation-login-button-text' => array(
						'title' => esc_html__('Main Navigation Login Button Text', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#fff',
						'selector' => '.traveltour-navigation-login-full-button .tourmaster-user-top-bar, .traveltour-navigation-login-button .tourmaster-user-top-bar.tourmaster-guest{ color: #gdlr#; }',
						'condition' => array( 'enable-main-navigation-login' => 'enable', 'main-navigation-login-style' => array('full-button', 'button') ) 
					),
					'enable-main-navigation-right-button' => array(
						'title' => esc_html__('Enable Main Navigation Right Button', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'disable',
						'description' => esc_html__('This option will be ignored on header side style', 'traveltour')
					),
					'main-navigation-right-button-text' => array(
						'title' => esc_html__('Main Navigation Right Button Text', 'traveltour'),
						'type' => 'text',
						'default' => esc_html__('Buy Now', 'traveltour'),
						'condition' => array( 'enable-main-navigation-right-button' => 'enable' ) 
					),
					'main-navigation-right-button-link' => array(
						'title' => esc_html__('Main Navigation Right Button Link', 'traveltour'),
						'type' => 'text',
						'condition' => array( 'enable-main-navigation-right-button' => 'enable' ) 
					),
					'main-navigation-right-button-link-target' => array(
						'title' => esc_html__('Main Navigation Right Button Link Target', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'_self' => esc_html__('Current Screen', 'traveltour'),
							'_blank' => esc_html__('New Window', 'traveltour'),
						),
						'condition' => array( 'enable-main-navigation-right-button' => 'enable' ) 
					),
					'right-menu-type' => array(
						'title' => esc_html__('Secondary/Mobile Menu Type', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'left' => esc_html__('Left Slide Menu', 'traveltour'),
							'right' => esc_html__('Right Slide Menu', 'traveltour'),
							'overlay' => esc_html__('Overlay Menu', 'traveltour'),
						),
						'default' => 'right'
					),
					'right-menu-style' => array(
						'title' => esc_html__('Secondary/Mobile Menu Style', 'traveltour'),
						'type' => 'combobox',
						'options' => array(
							'hamburger-with-border' => esc_html__('Hamburger With Border', 'traveltour'),
							'hamburger' => esc_html__('Hamburger', 'traveltour'),
						),
						'default' => 'hamburger-with-border'
					),
					
				) // logo-options
			);
		}
	}

	if( !function_exists('traveltour_fixed_navigation_options') ){
		function traveltour_fixed_navigation_options(){
			return array(
				'title' => esc_html__('Fixed Navigation', 'traveltour'),
				'options' => array(
					'enable-main-navigation-sticky' => array(
						'title' => esc_html__('Enable Fixed Navigation Bar', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable',
					),
					'enable-logo-on-main-navigation-sticky' => array(
						'title' => esc_html__('Enable Logo on Fixed Navigation Bar', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' )
					),
					'fixed-navigation-bar-logo' => array(
						'title' => esc_html__('Fixed Navigation Bar Logo', 'traveltour'),
						'type' => 'upload',
						'description' => esc_html__('Leave blank to show default logo', 'traveltour'),
						'condition' => array( 'enable-main-navigation-sticky' => 'enable', 'enable-logo-on-main-navigation-sticky' => 'enable' )
					),
					'fixed-navigation-bar-logo2x' => array(
						'title' => esc_html__('Fixed Navigation Bar Logo 2x (Retina)', 'traveltour'),
						'type' => 'upload',
						'description' => esc_html__('Leave blank to show default logo', 'traveltour'),
						'condition' => array( 'enable-main-navigation-sticky' => 'enable', 'enable-logo-on-main-navigation-sticky' => 'enable' )
					),
					'fixed-navigation-max-logo-width' => array(
						'title' => esc_html__('Fixed Navigation Max Logo Width', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' ),
						'selector' => '.traveltour-fixed-navigation.traveltour-style-slide .traveltour-logo-inner img{ max-height: none !important; }' .
							'.traveltour-animate-fixed-navigation.traveltour-header-style-plain .traveltour-logo-inner, ' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-boxed .traveltour-logo-inner{ max-width: #gdlr#; }' . 
							'.traveltour-mobile-header.traveltour-fixed-navigation .traveltour-logo-inner{ max-width: #gdlr#; }'
					),
					'fixed-navigation-logo-top-padding' => array(
						'title' => esc_html__('Fixed Navigation Logo Top Padding', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '20px',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' ),
						'selector' => '.traveltour-animate-fixed-navigation.traveltour-header-style-plain .traveltour-logo, ' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-boxed .traveltour-logo{ padding-top: #gdlr#; }'
					),
					'fixed-navigation-logo-bottom-padding' => array(
						'title' => esc_html__('Fixed Navigation Logo Bottom Padding', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '20px',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' ),
						'selector' => '.traveltour-animate-fixed-navigation.traveltour-header-style-plain .traveltour-logo, ' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-boxed .traveltour-logo{ padding-bottom: #gdlr#; }'
					),
					'fixed-navigation-top-padding' => array(
						'title' => esc_html__('Fixed Navigation Top Padding', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '30px',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' ),
						'selector' => '.traveltour-animate-fixed-navigation.traveltour-header-style-plain .traveltour-navigation, ' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-boxed .traveltour-navigation{ padding-top: #gdlr#; }' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-plain .traveltour-navigation-top, ' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-boxed .traveltour-navigation-top{ top: #gdlr#; }' .
							'.traveltour-animate-fixed-navigation.traveltour-navigation-bar-wrap .traveltour-navigation{ padding-top: #gdlr#; }'
					),
					'fixed-navigation-bottom-padding' => array(
						'title' => esc_html__('Fixed Navigation Bottom Padding', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '25px',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' ),
						'selector' => '.traveltour-animate-fixed-navigation.traveltour-header-style-plain .traveltour-navigation .sf-menu > li > a, ' . 
							'.traveltour-animate-fixed-navigation.traveltour-header-style-boxed .traveltour-navigation .sf-menu > li > a{ padding-bottom: #gdlr#; }' .
							'.traveltour-animate-fixed-navigation.traveltour-navigation-bar-wrap .traveltour-navigation .sf-menu > li > a{ padding-bottom: #gdlr#; }' .
							'.traveltour-animate-fixed-navigation .traveltour-main-menu-right{ margin-bottom: #gdlr#; }'
					),
					'enable-fixed-navigation-slide-bar' => array(
						'title' => esc_html__('Enable Fixed Navigation Slide Bar', 'traveltour'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					'fixed-navigation-slide-bar-top-margin' => array(
						'title' => esc_html__('Fixed Navigation Slide Bar Top Margin', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '',
						'selector' => '.traveltour-fixed-navigation .traveltour-navigation .traveltour-navigation-slide-bar{ margin-top: #gdlr#; }',
						'condition' => array('enable-fixed-navigation-slide-bar' => 'enable')
					),
					'fixed-navigation-right-top-margin' => array(
						'title' => esc_html__('Fixed Navigation Right (search/cart/button) Top margin', 'traveltour'),
						'type' => 'text',
						'data-type' => 'pixel',
						'data-input-type' => 'pixel',
						'default' => '',
						'condition' => array( 'enable-main-navigation-sticky' => 'enable' ),
						'selector' => '.traveltour-animate-fixed-navigation .traveltour-main-menu-right-wrap{ margin-top: #gdlr# !important; }'
					),
				)
			);
		}
	}

	if( !function_exists('traveltour_header_color_options') ){
		function traveltour_header_color_options(){

			return array(
				'title' => esc_html__('Header', 'traveltour'),
				'options' => array(
					
					'page-preload-background-color' => array(
						'title' => esc_html__('Page Preload Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector' => '.traveltour-page-preload{ background-color: #gdlr#; }'
					),
					'top-bar-background-color' => array(
						'title' => esc_html__('Top Bar Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#222222',
						'selector' => '.traveltour-top-bar-background{ background-color: #gdlr#; }'
					),
					'top-bar-bottom-border-color' => array(
						'title' => esc_html__('Top Bar Bottom Border Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector' => '.traveltour-body .traveltour-top-bar, .traveltour-top-bar-right > div:before{ border-color: #gdlr#; }'
					),
					'top-bar-text-color' => array(
						'title' => esc_html__('Top Bar Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector' => '.traveltour-top-bar{ color: #gdlr#; }'
					),
					'top-bar-link-color' => array(
						'title' => esc_html__('Top Bar Link Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector' => '.traveltour-body .traveltour-top-bar a{ color: #gdlr#; }'
					),
					'top-bar-link-hover-color' => array(
						'title' => esc_html__('Top Bar Link Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector' => '.traveltour-body .traveltour-top-bar a:hover{ color: #gdlr#; }'
					),
					'top-bar-icon-color' => array(
						'title' => esc_html__('Top Bar Icon Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#468ae7',
						'selector' => '.traveltour-top-bar i{ color: #gdlr#; }'
					),
					'header-background-color' => array(
						'title' => esc_html__('Header Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'data-type' => 'rgba',
						'default' => '#ffffff',
						'selector' => '.traveltour-header-background, .traveltour-sticky-menu-placeholder, .traveltour-header-style-boxed.traveltour-fixed-navigation{ background-color: #gdlr#; }' . 
							'.traveltour-sticky-navigation.traveltour-fixed-navigation .traveltour-header-background{ background: rgba(#gdlra#, 0.9); }'
					),
					'header-plain-bottom-border-color' => array(
						'title' => esc_html__('Header Bottom Border Color ( Header Plain Style )', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#e8e8e8',
						'selector' => '.traveltour-header-wrap.traveltour-header-style-plain{ border-color: #gdlr#; }'
					),
					'navigation-bar-background-color' => array(
						'title' => esc_html__('Navigation Bar Background Color ( Header Bar Style )', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#f4f4f4',
						'selector' => '.traveltour-navigation-background{ background-color: #gdlr#; }'
					),
					'navigation-bar-top-border-color' => array(
						'title' => esc_html__('Navigation Bar Top Border Color ( Header Bar Style )', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#e8e8e8',
						'selector' => '.traveltour-navigation-bar-wrap{ border-color: #gdlr#; }'
					),
					'navigation-slide-bar-color' => array(
						'title' => esc_html__('Navigation Slide Bar Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#2d9bea',
						'selector' => '.traveltour-navigation .traveltour-navigation-slide-bar, ' . 
							'.traveltour-navigation .traveltour-navigation-slide-bar-style-dot:before{ border-color: #gdlr#; }' . 
							'.traveltour-navigation .traveltour-navigation-slide-bar:before{ border-bottom-color: #gdlr#; }'
					),
					'logo-background-color' => array(
						'title' => esc_html__('Logo Background Color ( Header Side Menu Toggle Style )', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector' => '.traveltour-header-side-nav.traveltour-style-side-toggle .traveltour-logo{ background-color: #gdlr#; }'
					),
					'navigation-bar-right-icon-color' => array(
						'title' => esc_html__('Navigation Bar Right Icon Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#383838',
						'selector'=> '.traveltour-main-menu-search i, .traveltour-main-menu-cart i{ color: #gdlr#; }'
					),
					'woocommerce-cart-icon-number-background' => array(
						'title' => esc_html__('Woocommmerce Cart\'s Icon Number Background', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#bd584e',
						'selector'=> '.traveltour-main-menu-cart > .traveltour-top-cart-count{ background-color: #gdlr#; }'
					),
					'woocommerce-cart-icon-number-color' => array(
						'title' => esc_html__('Woocommmerce Cart\'s Icon Number Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.traveltour-main-menu-cart > .traveltour-top-cart-count{ color: #gdlr#; }'
					),
					'secondary-menu-icon-color' => array(
						'title' => esc_html__('Secondary Menu Icon Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#383838',
						'selector'=> '.traveltour-top-menu-button i, .traveltour-mobile-menu-button i{ color: #gdlr#; }' . 
							'.traveltour-mobile-button-hamburger:before, ' . 
							'.traveltour-mobile-button-hamburger:after, ' . 
							'.traveltour-mobile-button-hamburger span{ background: #gdlr#; }'
					),
					'secondary-menu-border-color' => array(
						'title' => esc_html__('Secondary Menu Border Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#dddddd',
						'selector'=> '.traveltour-main-menu-right .traveltour-top-menu-button, .traveltour-mobile-menu .traveltour-mobile-menu-button{ border-color: #gdlr#; }'
					),
					'search-overlay-background-color' => array(
						'title' => esc_html__('Search Overlay Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'data-type' => 'rgba',
						'default' => '#000000',
						'selector'=> '.traveltour-top-search-wrap{ background-color: #gdlr#; background-color: rgba(#gdlra#, 0.88); }'
					),
					'top-cart-background-color' => array(
						'title' => esc_html__('Top Cart Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#303030',
						'selector'=> '.traveltour-top-cart-content-wrap .traveltour-top-cart-content{ background-color: #gdlr#; }'
					),
					'top-cart-text-color' => array(
						'title' => esc_html__('Top Cart Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#b5b5b5',
						'selector'=> '.traveltour-top-cart-content-wrap .traveltour-top-cart-content span, ' .
							'.traveltour-top-cart-content-wrap .traveltour-top-cart-content span.woocommerce-Price-amount.amount{ color: #gdlr#; }'
					),
					'top-cart-view-cart-color' => array(
						'title' => esc_html__('Top Cart : View Cart Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.traveltour-top-cart-content-wrap .traveltour-top-cart-button,' .
							'.traveltour-top-cart-content-wrap .traveltour-top-cart-button:hover{ color: #gdlr#; }'
					),
					'top-cart-checkout-color' => array(
						'title' => esc_html__('Top Cart : Checkout Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#bd584e',
						'selector'=> '.traveltour-top-cart-content-wrap .traveltour-top-cart-checkout-button, ' .
							'.traveltour-top-cart-content-wrap .traveltour-top-cart-checkout-button:hover{ color: #gdlr#; }'
					),
					'breadcrumbs-text-color' => array(
						'title' => esc_html__('Breadcrumbs ( Plugin ) Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'data-type' => 'rgba',
						'default' => '#c0c0c0',
						'selector'=> '.traveltour-body .traveltour-breadcrumbs, .traveltour-body .traveltour-breadcrumbs a span{ color: #gdlr#; }'
					),
					'breadcrumbs-text-active-color' => array(
						'title' => esc_html__('Breadcrumbs ( Plugin ) Text Active Color', 'traveltour'),
						'type' => 'colorpicker',
						'data-type' => 'rgba',
						'default' => '#777777',
						'selector'=> '.traveltour-body .traveltour-breadcrumbs span, .traveltour-body .traveltour-breadcrumbs a:hover span{ color: #gdlr#; }'
					),

				) // header-options
			);

		}
	}

	if( !function_exists('traveltour_navigation_color_options') ){
		function traveltour_navigation_color_options(){

			return array(
				'title' => esc_html__('Menu / Navigation', 'traveltour'),
				'options' => array(

					'main-menu-text-color' => array(
						'title' => esc_html__('Main Menu Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#999999',
						'selector' => '.sf-menu > li > a, .sf-vertical > li > a{ color: #gdlr#; }'
					),
					'main-menu-text-hover-color' => array(
						'title' => esc_html__('Main Menu Text Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#333333',
						'selector' => '.sf-menu > li > a:hover, ' . 
							'.sf-menu > li.current-menu-item > a, ' .
							'.sf-menu > li.current-menu-ancestor > a, ' .
							'.sf-vertical > li > a:hover, ' . 
							'.sf-vertical > li.current-menu-item > a, ' .
							'.sf-vertical > li.current-menu-ancestor > a{ color: #gdlr#; }'
					),
					'sub-menu-background-color' => array(
						'title' => esc_html__('Sub Menu Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#2e2e2e',
						'selector'=> '.sf-menu > .traveltour-normal-menu li, .sf-menu > .traveltour-mega-menu > .sf-mega, ' . 
							'.sf-vertical ul.sub-menu li, ul.sf-menu > .menu-item-language li{ background-color: #gdlr#; }'
					),
					'sub-menu-text-color' => array(
						'title' => esc_html__('Sub Menu Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#bebebe',
						'selector'=> '.sf-menu > li > .sub-menu a, .sf-menu > .traveltour-mega-menu > .sf-mega a, ' . 
							'.sf-vertical ul.sub-menu li a{ color: #gdlr#; }'
					),
					'sub-menu-text-hover-color' => array(
						'title' => esc_html__('Sub Menu Text Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.sf-menu > li > .sub-menu a:hover, ' . 
							'.sf-menu > li > .sub-menu .current-menu-item > a, ' . 
							'.sf-menu > li > .sub-menu .current-menu-ancestor > a, '.
							'.sf-menu > .traveltour-mega-menu > .sf-mega a:hover, '.
							'.sf-menu > .traveltour-mega-menu > .sf-mega .current-menu-item > a, '.
							'.sf-vertical > li > .sub-menu a:hover, ' . 
							'.sf-vertical > li > .sub-menu .current-menu-item > a, ' . 
							'.sf-vertical > li > .sub-menu .current-menu-ancestor > a{ color: #gdlr#; }'
					),
					'sub-menu-text-hover-background-color' => array(
						'title' => esc_html__('Sub Menu Text Hover Background', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#393939',
						'selector'=> '.sf-menu > li > .sub-menu a:hover, ' . 
							'.sf-menu > li > .sub-menu .current-menu-item > a, ' . 
							'.sf-menu > li > .sub-menu .current-menu-ancestor > a, '.
							'.sf-menu > .traveltour-mega-menu > .sf-mega a:hover, '.
							'.sf-menu > .traveltour-mega-menu > .sf-mega .current-menu-item > a, '.
							'.sf-vertical > li > .sub-menu a:hover, ' . 
							'.sf-vertical > li > .sub-menu .current-menu-item > a, ' . 
							'.sf-vertical > li > .sub-menu .current-menu-ancestor > a{ background-color: #gdlr#; }'
					),
					'sub-mega-menu-title-color' => array(
						'title' => esc_html__('Sub Mega Menu Title Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.traveltour-navigation .sf-menu > .traveltour-mega-menu .sf-mega-section-inner > a{ color: #gdlr#; }'
					),
					'sub-mega-menu-divider-color' => array(
						'title' => esc_html__('Sub Mega Menu Divider Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#424242',
						'selector'=> '.traveltour-navigation .sf-menu > .traveltour-mega-menu .sf-mega-section{ border-color: #gdlr#; }'
					),
					'sub-menu-shadow-size' => array(
						'title' => esc_html__('Sub Menu Shadow Size', 'traveltour'),
						'type' => 'text',
						'data-input-type' => 'pixel',
					),
					'sub-menu-shadow-opacity' => array(
						'title' => esc_html__('Sub Menu Shadow Opacity', 'traveltour'),
						'type' => 'text',
						'default' => '0.15',
					),
					'sub-menu-shadow-color' => array(
						'title' => esc_html__('Sub Menu Shadow Color', 'traveltour'),
						'type' => 'colorpicker',
						'data-type' => 'rgba',
						'default' => '#000',
						'selector-extra' => true,
						'selector' => '.traveltour-navigation .sf-menu > .traveltour-normal-menu .sub-menu, .traveltour-navigation .sf-menu > .traveltour-mega-menu .sf-mega{ ' . 
							'box-shadow: 0px 0px <sub-menu-shadow-size>t rgba(#gdlra#, <sub-menu-shadow-opacity>t); ' .
							'-webkit-box-shadow: 0px 0px <sub-menu-shadow-size>t rgba(#gdlra#, <sub-menu-shadow-opacity>t); ' .
							'-moz-box-shadow: 0px 0px <sub-menu-shadow-size>t rgba(#gdlra#, <sub-menu-shadow-opacity>t); }',
					),
					'side-menu-text-color' => array(
						'title' => esc_html__('Side Menu Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#979797',
						'selector'=> '.mm-navbar .mm-title, .mm-navbar .mm-btn, ul.mm-listview li > a, ul.mm-listview li > span{ color: #gdlr#; }' . 
							'ul.mm-listview li a{ border-color: #gdlr#; }' .
							'.mm-arrow:after, .mm-next:after, .mm-prev:before{ border-color: #gdlr#; }'
					),
					'side-menu-text-hover-color' => array(
						'title' => esc_html__('Side Menu Text Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.mm-navbar .mm-title:hover, .mm-navbar .mm-btn:hover, ' .
							'ul.mm-listview li a:hover, ul.mm-listview li > span:hover, ' . 
							'ul.mm-listview li.current-menu-item > a, ul.mm-listview li.current-menu-ancestor > a, ul.mm-listview li.current-menu-ancestor > span{ color: #gdlr#; }'
					),
					'side-menu-background-color' => array(
						'title' => esc_html__('Side Menu Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#1f1f1f',
						'selector'=> '.mm-menu{ background-color: #gdlr#; }'
					),
					'side-menu-border-color' => array(
						'title' => esc_html__('Side Menu Border Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#626262',
						'selector'=> 'ul.mm-listview li{ border-color: #gdlr#; }'
					),
					'overlay-menu-background-color' => array(
						'title' => esc_html__('Overlay Menu Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'data-type' => 'rgba',
						'default' => '#000000',
						'selector'=> '.traveltour-overlay-menu-content{ background-color: #gdlr#; background-color: rgba(#gdlra#, 0.88); }'
					),
					'overlay-menu-border-color' => array(
						'title' => esc_html__('Overlay Menu Border Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#424242',
						'selector'=> '.traveltour-overlay-menu-content ul.menu > li, .traveltour-overlay-menu-content ul.sub-menu ul.sub-menu{ border-color: #gdlr#; }'
					),
					'overlay-menu-text-color' => array(
						'title' => esc_html__('Overlay Menu Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.traveltour-overlay-menu-content ul li a, .traveltour-overlay-menu-content .traveltour-overlay-menu-close{ color: #gdlr#; }'
					),
					'overlay-menu-text-hover-color' => array(
						'title' => esc_html__('Overlay Menu Text Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#a8a8a8',
						'selector'=> '.traveltour-overlay-menu-content ul li a:hover{ color: #gdlr#; }'
					),
					'anchor-bullet-background-color' => array(
						'title' => esc_html__('Anchor Bullet Background', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#777777',
						'selector'=> '.traveltour-bullet-anchor a:before{ background-color: #gdlr#; }'
					),
					'anchor-bullet-background-active-color' => array(
						'title' => esc_html__('Anchor Bullet Background Active', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#ffffff',
						'selector'=> '.traveltour-bullet-anchor a:hover, .traveltour-bullet-anchor a.current-menu-item{ border-color: #gdlr#; }' .
							'.traveltour-bullet-anchor a:hover:before, .traveltour-bullet-anchor a.current-menu-item:before{ background: #gdlr#; }'
					),
					'navigation-right-button-text-color' => array(
						'title' => esc_html__('Navigation Right Button Text Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#333333',
						'selector'=> '.traveltour-body .traveltour-main-menu-right-button{ color: #gdlr#; }'
					),
					'navigation-right-button-background-color' => array(
						'title' => esc_html__('Navigation Right Button Background Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '',
						'selector'=> '.traveltour-body .traveltour-main-menu-right-button{ background-color: #gdlr#; }'
					),
					'navigation-right-button-border-color' => array(
						'title' => esc_html__('Navigation Right Button Border Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#333333',
						'selector'=> '.traveltour-body .traveltour-main-menu-right-button{ border-color: #gdlr#; }'
					),
					'navigation-right-button-text-hover-color' => array(
						'title' => esc_html__('Navigation Right Button Text Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#555555',
						'selector'=> '.traveltour-body .traveltour-main-menu-right-button:hover{ color: #gdlr#; }'
					),
					'navigation-right-button-background-hover-color' => array(
						'title' => esc_html__('Navigation Right Button Background Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '',
						'selector'=> '.traveltour-body .traveltour-main-menu-right-button:hover{ background-color: #gdlr#; }'
					),
					'navigation-right-button-border-hover-color' => array(
						'title' => esc_html__('Navigation Right Button Border Hover Color', 'traveltour'),
						'type' => 'colorpicker',
						'default' => '#555555',
						'selector'=> '.traveltour-body .traveltour-main-menu-right-button:hover{ border-color: #gdlr#; }'
					),
					
										
				) // navigation-menu-options
			);	

		} // traveltour_navigation_color_options
	}