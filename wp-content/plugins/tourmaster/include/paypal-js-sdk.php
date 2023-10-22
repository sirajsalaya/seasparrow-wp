<?php 

    if( !function_exists('tourmaster_paypal_js_sdk_payment_form') ){
        function tourmaster_paypal_js_sdk_payment_form(){

            $service_fee = tourmaster_get_option('payment', 'paypal-service-fee', '');
            
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

    if( !function_exists('tourmaster_get_paypal_jssdk_payment_variable') ){
        function tourmaster_get_paypal_jssdk_payment_variable($tour_price, $payment_type, $booking_detail){
            $ret = '';
            $enable_payment = tourmaster_get_option('payment', 'enable-payment', 'enable');
			$payment_methods = tourmaster_get_option('payment', 'payment-method', array('booking', 'paypal', 'credit-card'));
			$paypal_method = tourmaster_get_option('payment', 'paypal-method', 'standard');
			if( $enable_payment == 'enable' && in_array('paypal', $payment_methods) && $paypal_method == 'js-sdk' ){
                
                // currency code
                global $tourmaster_currency;
                if( !empty($booking_detail['tid']) ){
                    $result = tourmaster_get_booking_data(array('id'=>$booking_detail['tid']), array('single'=>true));
                    $order_currency = json_decode($result->currency, true);
                    if( !empty($order_currency) ){
                        $multiplier = floatval($order_currency['exchange-rate']);
                        $currency_code = $order_currency['currency-code'];
                    }
                }else if( !empty($tourmaster_currency) ){
                    $multiplier = floatval($tourmaster_currency['exchange-rate']);
                    $currency_code = $tourmaster_currency['currency-code'];
                }
                if( empty($currency_code) ){
                    $multiplier = 1;
                    $currency_code = tourmaster_get_option('payment', 'paypal-currency-code', '');
                }

                $client_id = tourmaster_get_option('payment', 'paypal-client-id', '');
                $ret  = '<script src="https://www.paypal.com/sdk/js?client-id=' . esc_attr($client_id) . '&currency=' . esc_attr(strtoupper($currency_code)) . '&disable-funding=card"></script>';
                $ret .= '<script>';

                $ret .= 'window.tourmaster_paypal_currency_code = "' . strtoupper($currency_code) . '"; ';

                // price
                $service_fee = tourmaster_get_option('payment', 'paypal-service-fee', '');
                if( !empty($service_fee) ){
                    if( !empty($tour_price['pay-amount']) ){
                        $ret .= 'window.tourmaster_paypal_full_amount = "' . number_format($multiplier * floatval($tour_price['pay-amount']) * ((100 + floatval($service_fee)) / 100), 2, '.', '') . '"; ';
                    }
                    if( !empty($tour_price['deposit-price']) ){
                        $ret .= 'window.tourmaster_paypal_deposit_amount = "' . number_format($multiplier * floatval($tour_price['deposit-price']) * ((100 + floatval($service_fee)) / 100), 2, '.', '') . '"; ';
                    }
                }else{
                    if( !empty($tour_price['pay-amount']) ){
                        $ret .= 'window.tourmaster_paypal_full_amount = "' . number_format($multiplier * floatval($tour_price['pay-amount']), 2, '.', '') . '"; ';
                    }
                    if( !empty($tour_price['deposit-price']) ){
                        $ret .= 'window.tourmaster_paypal_deposit_amount = "' . number_format($multiplier * floatval($tour_price['deposit-price']), 2, '.', '') . '"; ';
                    }
                }

                // payment type
                if( $payment_type == 'partial' ){
                    $ret .= 'window.tourmaster_payment_type = "partial"; ';
                    $ret .= 'window.tourmaster_paypal_pay_amount = window.tourmaster_paypal_deposit_amount; ';
                }else{
                    $ret .= 'window.tourmaster_payment_type = "full"; ';
                    $ret .= 'window.tourmaster_paypal_pay_amount = window.tourmaster_paypal_full_amount; ';
                }
                $ret .= '</script>';
			}
            return $ret;
        }
    }

    if( !function_exists('tourmaster_set_paypal_jssdk_payment_status') ){
        function tourmaster_set_paypal_jssdk_payment_status($tid, $order_id){

			$result = tourmaster_get_booking_data(array('id'=>$tid), array('single'=>true));
            $payment_info = tourmaster_paypal_read_order($order_id);

            // apply currency
            if( !empty($result->currency) ){
                $currency = json_decode($result->currency, true);
                if( !empty($currency) ){
                    $payment_info['amount'] = $payment_info['amount'] / floatval($currency['exchange-rate']);
                }
            }

            $pricing_info = json_decode($result->pricing_info, true);
            $mail_type = 'payment-made-mail';
            $admin_mail_type = 'admin-online-payment-made-mail';

            if( !empty($pricing_info['deposit-price']) && tourmaster_compare_price($pricing_info['deposit-price'], $payment_info['amount']) ){
                $order_status = 'deposit-paid';
                if( !empty($pricing_info['deposit-price-raw']) ){
                    $payment_info['deposit_amount'] = $pricing_info['deposit-price-raw'];
                }
                if( !empty($pricing_info['deposit-paypal-service-rate']) ){
                    $payment_info['deposit_paypal_service_rate'] = $pricing_info['deposit-paypal-service-rate'];
                }
                if( !empty($pricing_info['deposit-paypal-service-fee']) ){
                    $payment_info['deposit_paypal_service_fee'] = $pricing_info['deposit-paypal-service-fee'];
                }
                $mail_type = 'deposit-payment-made-mail';
                $admin_mail_type = 'admin-deposit-payment-made-mail';
            }else if( tourmaster_compare_price($pricing_info['pay-amount'], $payment_info['amount']) ){
                $order_status = 'online-paid';
                if( !empty($pricing_info['pay-amount-raw']) ){
                    $payment_info['pay_amount'] = $pricing_info['pay-amount-raw'];
                }
                if( !empty($pricing_info['pay-amount-paypal-service-rate']) ){
                    $payment_info['pay_paypal_service_rate'] = $pricing_info['pay-amount-paypal-service-rate'];
                }
                if( !empty($pricing_info['pay-amount-paypal-service-fee']) ){
                    $payment_info['pay_paypal_service_fee'] = $pricing_info['pay-amount-paypal-service-fee'];
                }
            }else if( $payment_info['amount'] > $pricing_info['total-price'] ){
                $order_status = 'online-paid';
            }else{
                $order_status = 'deposit-paid';
                $mail_type = 'deposit-payment-made-mail';
                $admin_mail_type = 'admin-deposit-payment-made-mail';
            }

            // get old payment info
            $payment_infos = json_decode($result->payment_info, true);
            $payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);
            $payment_infos[] = $payment_info;

            tourmaster_update_booking_data( 
                array(
                    'payment_info' => json_encode($payment_infos),
                    'payment_date' => current_time('mysql'),
                    'order_status' => $order_status,
                ),
                array('id' => $tid),
                array('%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );

            tourmaster_mail_notification($mail_type, $tid, '', array(
                'custom' => array(
                    'payment-method' => $payment_info['payment_method'],
                    'payment-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
                    'submission-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
                    'submission-amount' => tourmaster_money_format($payment_info['amount']),
                    'transaction-id' => $payment_info['transaction_id']
                )
            ));
            tourmaster_mail_notification($admin_mail_type, $tid, '', array(
                'custom' => array(
                    'payment-method' => $payment_info['payment_method'],
                    'payment-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
                    'submission-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
                    'submission-amount' => tourmaster_money_format($payment_info['amount']),
                    'transaction-id' => $payment_info['transaction_id']
                )
            ));
            tourmaster_send_email_invoice($tid);

        }
    }
    
    if( !function_exists('tourmaster_paypal_read_order') ){
        function tourmaster_paypal_read_order($order_id){
            $access_token = tourmaster_paypal_accesstoken();
            if( is_wp_error($access_token) ){
                return array(
                    'payment_method' => 'paypal',
                    'submission_date' => current_time('mysql'),
                    'error' => $access_token->get_error_message()
                );
            }

            $live_mode = tourmaster_get_option('payment', 'paypal-live-mode', 'disable');
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

            return array(
                'payment_method' => 'paypal',
                'submission_date' => current_time('mysql'),
                'transaction_id' => $order_id,
                'amount' => $result['purchase_units'][0]['amount']['value'],
                'payment_status' => 'paid'
            );
        }
    }
            
    // get access token
    if( !function_exists('tourmaster_paypal_accesstoken') ){
        function tourmaster_paypal_accesstoken(){
            
            $live_mode = tourmaster_get_option('payment', 'paypal-live-mode', 'disable');
            if( $live_mode == 'disable' ){
                $action_url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
            }else{
                $action_url = 'https://api-m.paypal.com/v1/oauth2/token';
            }
            
            $post_fields = 'grant_type=client_credentials'; 
            $password = tourmaster_get_option('payment', 'paypal-client-id', '') . ':' . tourmaster_get_option('payment', 'paypal-client-secret', '');
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

