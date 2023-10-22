<?php
	/* a template for displaying the top bar */

	if( traveltour_get_option('general', 'enable-top-bar', 'enable') == 'enable' ){

		$top_bar_width = traveltour_get_option('general', 'top-bar-width', 'boxed');
		$top_bar_divider = traveltour_get_option('general', 'enable-top-bar-divider', 'disable');
		$top_bar_container_class = '';

		if( $top_bar_width == 'boxed' ){
			$top_bar_container_class = 'traveltour-container ';
		}else if( $top_bar_width == 'custom' ){
			$top_bar_container_class = 'traveltour-top-bar-custom-container ';
		}else{
			$top_bar_container_class = 'traveltour-top-bar-full ';
		}
		
		$top_bar_login = traveltour_get_option('general', 'enable-top-bar-right-login', 'enable');
		$top_bar_login = ($top_bar_login == 'enable')? 'right-with-border': $top_bar_login;
		echo '<div class="traveltour-top-bar ' . ($top_bar_divider == 'enable'? 'traveltour-with-divider': '') . '" >';
		echo '<div class="traveltour-top-bar-background" ></div>';
		echo '<div class="traveltour-top-bar-container clearfix ' . esc_attr($top_bar_container_class) . '" >';
		
		
		echo '<div class="traveltour-top-bar-left traveltour-item-pdlr traveltour-' . esc_attr($top_bar_login) . '">';
		$left_text = traveltour_get_option('general', 'top-bar-left-text', '');
		if( !empty($left_text) ){
			echo '<div class="traveltour-top-bar-left-text">';
			echo gdlr_core_text_filter($left_text);
			echo '</div>';
		}
		if( ($top_bar_login == 'left-no-border') && function_exists('tourmaster_user_top_bar') ){
			echo tourmaster_user_top_bar();
		}
		echo '</div>';

		$right_text = traveltour_get_option('general', 'top-bar-right-text', '');
		$top_bar_social = traveltour_get_option('general', 'enable-top-bar-social', 'enable');
		echo '<div class="traveltour-top-bar-right traveltour-item-pdlr traveltour-' . esc_attr($top_bar_login) . '">';
		if( !empty($right_text) ){
			echo '<div class="traveltour-top-bar-right-text">';
			echo gdlr_core_text_filter($right_text);
			echo '</div>';
		}

		if( $top_bar_social == 'enable' ){
			echo '<div class="traveltour-top-bar-right-social" >';
			get_template_part('header/header', 'social');
			echo '</div>';	
		}

		$wpml_flag_type = traveltour_get_option('general', 'wpml-flag-type', 'icon');
		$language_flag = traveltour_get_wpml_flag($wpml_flag_type);
		if( !empty($language_flag) ){
			echo '<div class="traveltour-top-bar-wpml-flag" >' . gdlr_core_escape_content($language_flag) . '</div>';
		}
		if( ($top_bar_login == 'right-with-border') && function_exists('tourmaster_user_top_bar') ){
			echo tourmaster_user_top_bar();
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';

	}  // top bar
?>