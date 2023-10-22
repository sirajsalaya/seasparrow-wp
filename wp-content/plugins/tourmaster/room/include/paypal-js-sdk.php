<?php 

    if( !function_exists('tourmaster_room_paypal_js_sdk_payment_form') ){
        function tourmaster_room_paypal_js_sdk_payment_form(){

            $service_fee = tourmaster_get_option('room_payment', 'paypal-service-fee', '');
            
            ob_start();
?>
<div class="tourmaster-paypal-jssdk-payment-form" >
<div id="paypal-button-container"></div>
<?php
    if( !empty($service_fee) ){
        echo '<div class="tourmaster-payment-paypal-service-fee-text" >';
        echo sprintf(esc_html__('Additional %s%% is charged for PayPal payment.', 'tourmaster'), $service_fee);
        echo '</div>';
    }
?>
</div>
<?php
            $ret = ob_get_contents();
            ob_end_clean();

            return $ret;
        }
    }

    if( !function_exists('tourmaster_room_include_paypal_jssdk') ){
        function tourmaster_room_include_paypal_jssdk($currency = ''){
            
            $ret = '';
            $payment_methods = tourmaster_get_option('room_payment', 'payment-method', array('booking', 'paypal', 'credit-card'));
			$paypal_method = tourmaster_get_option('room_payment', 'paypal-method', 'standard');
			if( in_array('paypal', $payment_methods) && $paypal_method == 'js-sdk' ){

                $client_id = tourmaster_get_option('room_payment', 'paypal-client-id', '');

                global $tourmaster_currency;
                if( !empty($currency) ){
                    $currency = json_decode($currency, true);
                    $currency_code = $currency['currency-code'];
                }else if( !empty($tourmaster_currency) ){
                    $currency_code = $tourmaster_currency['currency-code'];
                }
                if( empty($currency_code) ){
                    $currency_code = tourmaster_get_option('room_payment', 'paypal-currency-code', '');
                }

                echo '<script src="https://www.paypal.com/sdk/js?client-id=' . esc_attr($client_id) . '&currency=' . esc_attr(strtoupper($currency_code)) . '&disable-funding=card"></script>';
            }
        }
    }

    if( !function_exists('tourmaster_room_get_paypal_jssdk_payment_variable') ){
        function tourmaster_room_get_paypal_jssdk_payment_variable($price_breakdowns, $deposit_info, $currency){
            $ret = '';
            $payment_methods = tourmaster_get_option('room_payment', 'payment-method', array('booking', 'paypal', 'credit-card'));
			$paypal_method = tourmaster_get_option('room_payment', 'paypal-method', 'standard');
			if( in_array('paypal', $payment_methods) && $paypal_method == 'js-sdk' ){
                
                // currency code
                global $tourmaster_currency;
                if( !empty($currency) ){
                    if( !empty($currency) ){
                        $currency = json_decode($currency, true);
                        $multiplier = floatval($currency['exchange-rate']);
                        $currency_code = $currency['currency-code'];
                    }
                }else if( !empty($tourmaster_currency) ){
                    $multiplier = floatval($tourmaster_currency['exchange-rate']);
                    $currency_code = $tourmaster_currency['currency-code'];
                }
                if( empty($currency_code) ){
                    $multiplier = 1;
                    $currency_code = tourmaster_get_option('room_payment', 'paypal-currency-code', '');
                }
                
                $ret  = '<script>';
                $ret .= 'window.tourmaster_paypal_currency_code = "' . strtoupper($currency_code) . '"; ';

                // price
                $service_fee = tourmaster_get_option('room_payment', 'paypal-service-fee', '');
                $pay_amount = floatval($price_breakdowns['grand-total-price']);
                if( !empty($deposit_info['paid_amount']) ){
                    $pay_amount = $pay_amount - floatval($deposit_info['paid_amount']);
                }
                if( !empty($service_fee) ){
                    if( !empty($pay_amount) ){
                        $ret .= 'window.tourmaster_paypal_full_amount = "' . number_format($multiplier * floatval($pay_amount) * ((100 + floatval($service_fee)) / 100), 2, '.', '') . '"; ';
                    }
                    if( !empty($deposit_info['deposit_amount']) ){
                        $ret .= 'window.tourmaster_paypal_deposit_amount = "' . number_format($multiplier * floatval($deposit_info['deposit_amount']) * ((100 + floatval($service_fee)) / 100), 2, '.', '') . '"; ';
                    }
                }else{
                    if( !empty($pay_amount) ){
                        $ret .= 'window.tourmaster_paypal_full_amount = "' . number_format($multiplier * floatval($pay_amount), 2, '.', '') . '"; ';
                    }
                    if( !empty($deposit_info['deposit_amount']) ){
                        $ret .= 'window.tourmaster_paypal_deposit_amount = "' . number_format($multiplier * floatval($deposit_info['deposit_amount']), 2, '.', '') . '"; ';
                    }
                }

                // payment type
                $ret .= 'window.tourmaster_payment_type = "full"; ';
                $ret .= 'window.tourmaster_paypal_pay_amount = window.tourmaster_paypal_full_amount; ';
                $ret .= '</script>';
			}
            return $ret;
        }
    }

    if( !function_exists('tourmaster_room_set_paypal_jssdk_payment_status') ){
        function tourmaster_room_set_paypal_jssdk_payment_status($tid, $order_id){

            // query the selected order
            global $wpdb;
            $sql  = "SELECT total_price, contact_info, payment_info, currency FROM {$wpdb->prefix}tourmaster_room_order ";
            $sql .= $wpdb->prepare("WHERE id = %d", $tid);
            $order = $wpdb->get_row($sql);

            $payment_info = tourmaster_room_paypal_read_order($order_id, $order->currency);

            $payment_infos = empty($order->payment_info)? array(): json_decode($order->payment_info, true);
            $payment_infos[] = $payment_info;

            $order_status = tourmaster_room_payment_order_status($order->total_price, $payment_infos, true);
            
            $wpdb->update(
                "{$wpdb->prefix}tourmaster_room_order", 
                array('payment_info'=> json_encode($payment_infos), 'order_status' => $order_status), 
                array('id' => $tid),
                array('%s', '%s'),
                array('%d')
            );

            // send an email
            if( $order_status == 'deposit-paid' ){
                tourmaster_room_mail_notification('deposit-payment-made-mail', $tid, '', array('custom' => $payment_info));
                tourmaster_room_mail_notification('admin-deposit-payment-made-mail', $tid, '', array('custom' => $payment_info));
            }else if( $order_status == 'approved' || $order_status == 'online-paid' ){
                tourmaster_room_mail_notification('payment-made-mail', $tid, '', array('custom' => $payment_info));
                tourmaster_room_mail_notification('admin-online-payment-made-mail', $tid, '', array('custom' => $payment_info));
            }
            tourmaster_room_send_email_invoice($tid);

        }
    }

    if( !function_exists('tourmaster_room_paypal_read_order') ){
        function tourmaster_room_paypal_read_order($order_id, $currency = ''){
            $access_token = tourmaster_room_paypal_accesstoken();
            if( is_wp_error($access_token) ){
                return array(
                    'payment_method' => 'paypal',
                    'submission_date' => current_time('mysql'),
                    'error' => $access_token->get_error_message()
                );
            }

            $live_mode = tourmaster_get_option('room_payment', 'paypal-live-mode', 'disable');
            if( $live_mode == 'disable' ){
                $action_url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/' . $order_id;
            }else{
                $action_url = 'https://api-m.paypal.com/v2/checkout/orders/' . $order_id;
            }
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer ' . $access_token;
            
            $ch = curl_init($action_url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);

            // check for error
            if( curl_errno($ch) ){
                return new WP_Error('curl_order_error', curl_error($ch));
            }
            curl_close($ch); 

            $result = json_decode($result, true);

            $payment_info = array(
                'payment_method' => 'paypal',
                'submission_date' => current_time('mysql'),
                'transaction_id' => $order_id,
                'payment_status' => 'paid'
            );

            $paid_amount = floatval($result['purchase_units'][0]['amount']['value']);

            // apply currency
            if( !empty($currency) ){
                $currency = json_decode($currency, true);
                if( !empty($currency) ){
                    $paid_amount = $paid_amount / floatval($currency['exchange-rate']);
                }
            }

            $payment_info['amount'] = $paid_amount;
            $payment_info['paid_amount'] = $paid_amount;

            // service fee
            $service_fee = tourmaster_get_option('room_payment', 'paypal-service-fee', '0');
            if( !empty($service_fee) ){
                $payment_info['amount'] = floatval($payment_info['amount']) / (1 + (floatval($service_fee) / 100));
            }

            $payment_info['service_fee'] = $payment_info['paid_amount'] - $payment_info['amount'];
		    $payment_info['service_fee_rate'] = $service_fee;

            return $payment_info;
        }
    }
            
    // get access token
    if( !function_exists('tourmaster_room_paypal_accesstoken') ){
        function tourmaster_room_paypal_accesstoken(){
            
            $live_mode = tourmaster_get_option('room_payment', 'paypal-live-mode', 'disable');
            if( $live_mode == 'disable' ){
                $action_url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
            }else{
                $action_url = 'https://api-m.paypal.com/v1/oauth2/token';
            }
            
            $post_fields = 'grant_type=client_credentials'; 
            $password = tourmaster_get_option('room_payment', 'paypal-client-id', '') . ':' . tourmaster_get_option('room_payment', 'paypal-client-secret', '');
            $headers = array();
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            
            $ch = curl_init($action_url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
            curl_setopt($ch, CURLOPT_USERPWD, $password);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $result = curl_exec($ch);

            if( curl_errno($ch) ){
                return new WP_Error('curl_access_token_error', curl_error($ch));
            }
            curl_close($ch);

            $result = json_decode($result, true);
            return $result['access_token'];
            
        }
    }