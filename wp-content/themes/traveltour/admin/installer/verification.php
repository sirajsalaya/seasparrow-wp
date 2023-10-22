<?php

	define('TRAVELTOUR_ITEM_ID', 19423474);
	define('TRAVELTOUR_PURCHASE_VERFIY_URL', 'https://goodlayers.com/licenses/wp-json/verify/purchase_code'); 
	define('TRAVELTOUR_PLUGIN_VERSION_URL', 'https://goodlayers.com/licenses/wp-json/version/plugin');
	define('TRAVELTOUR_PLUGIN_UPDATE_URL', 'https://goodlayers.com/licenses/wp-content/plugins/goodlayers-verification/download/');
	
	// define('TRAVELTOUR_PURCHASE_VERFIY_URL', 'http://localhost/traveltour/wp-json/verify/purchase_code'); 
	// define('TRAVELTOUR_PLUGIN_VERSION_URL', 'http://localhost/traveltour/wp-json/version/plugin'); 
	// define('TRAVELTOUR_PLUGIN_UPDATE_URL', 'http://localhost/Gdl%20Theme/plugins/goodlayers-verification/download/');

	if( !function_exists('traveltour_is_purchase_verified') ){
		function traveltour_is_purchase_verified(){
			$purchase_code = traveltour_get_purchase_code(); 
			return empty($purchase_code)? false: true;
		}
	}
	if( !function_exists('traveltour_get_purchase_code') ){
		function traveltour_get_purchase_code(){
			return get_option('envato_purchase_code_' . TRAVELTOUR_ITEM_ID, '');
		}
	}
	if( !function_exists('traveltour_get_download_url') ){
		function traveltour_get_download_url($file){
			$download_key = get_option('traveltour_download_key', '');
			$purchase_code = traveltour_get_purchase_code();
			if( empty($download_key) ) return false;

			return add_query_arg(array(
				'purchase_code' => $purchase_code,
				'download_key' => $download_key,
				'file' => $file
			), TRAVELTOUR_PLUGIN_UPDATE_URL);
		}
	}

	# delete_option('envato_purchase_code_' . TRAVELTOUR_ITEM_ID);
	# delete_option('traveltour_download_key');
	if( !function_exists('traveltour_verify_purchase') ){
		function traveltour_verify_purchase($purchase_code, $register){
			$response = wp_remote_post(TRAVELTOUR_PURCHASE_VERFIY_URL, array(
				'body' => array(
					'register' => $register,
					'item_id' => TRAVELTOUR_ITEM_ID,
					'website' => get_site_url(),
					'purchase_code' => $purchase_code
				)
			));

			if( is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200 ){
				throw new Exception(wp_remote_retrieve_response_message($response));
			}

			$data = json_decode(wp_remote_retrieve_body($response), true);
			if( $data['status'] == 'success' ){
				update_option('envato_purchase_code_' . TRAVELTOUR_ITEM_ID, $purchase_code);
				update_option('traveltour_download_key', $data['download_key']);
				return true;
			}else{
				update_option('envato_purchase_code_' . TRAVELTOUR_ITEM_ID, '');
				update_option('traveltour_download_key', '');

				if( !empty($data['message']) ){
					throw new Exception($data['message']);
				}else{
					throw new Exception(esc_html__('Unknown Error', 'traveltour'));
				}
				
			}

		} // traveltour_verify_purchase
	}

	// delete_option('traveltour_daily_schedule');
	// delete_option('traveltour-plugins-version');
	add_action('init', 'traveltour_admin_schedule');
	if( !function_exists('traveltour_admin_schedule') ){
		function traveltour_admin_schedule(){
			if( !is_admin() ) return;

			$current_date = date('Y-m-d');
			$daily_schedule = get_option('traveltour_daily_schedule', '');
			if( $daily_schedule != $current_date ){
				update_option('traveltour_daily_schedule', $current_date);
				do_action('traveltour_daily_schedule');
			}
		}
	}

	# update version from server
	add_action('traveltour_daily_schedule', 'traveltour_plugin_version_update');
	if( !function_exists('traveltour_plugin_version_update') ){
		function traveltour_plugin_version_update(){
			$response = wp_remote_get(TRAVELTOUR_PLUGIN_VERSION_URL);

			if( !is_wp_error($response) && !empty($response['body']) ){
				update_option('traveltour-plugins-version', json_decode($response['body'], true));
			}
		}
	}