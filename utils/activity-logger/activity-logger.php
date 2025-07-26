<?php

require_once(plugin_dir_path( __FILE__ ).'/activity-log-page.php');

function activity_log_callback($log_file_name, $message, $debug_mode = 'no') {
  if($debug_mode === 'no') {
    return;
  }

  $log_file = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/' . $log_file_name . '.log';
  $current_time = current_time('mysql');
  $log_message = "[$current_time]: $message\n";
  file_put_contents($log_file, $log_message, FILE_APPEND);
}

function activity_log_on_order_creation($order_id, $posted_data) {

  $order_id_log = __('Order created: #', 'victoriabank-payment-gateway') . $order_id;
  $order_amount = '#' . $order_id . __(' Order added amount: ', 'victoriabank-payment-gateway') . $posted_data->data['total'] . ' ' . $posted_data->data['currency'];
  $order_billing = '#' . $order_id . __(' Order added billing name: ', 'victoriabank-payment-gateway') . $posted_data->data['billing']['first_name'] . ' ' . $posted_data->data['billing']['last_name'];
  $order_billing_address = '#' . $order_id . __(' Order added billing address: ', 'victoriabank-payment-gateway') . $posted_data->data['billing']['country'] . ', ' . $posted_data->data['billing']['city'] . ', ' . $posted_data->data['billing']['address_1'];
  $order_billing_postcode = '#' . $order_id . __(' Order added billing postcode: ', 'victoriabank-payment-gateway') . $posted_data->data['billing']['postcode'];
  $order_billing_phone = '#' . $order_id . __(' Order added billing phone: ', 'victoriabank-payment-gateway') . $posted_data->data['billing']['phone'];
  $order_billing_email = '#' . $order_id . __(' Order added billing email: ', 'victoriabank-payment-gateway') . $posted_data->data['billing']['email'];

  $order = wc_get_order($order_id);
  $payment_method = $order->get_payment_method();
  $gateway_options = get_option( 'woocommerce_' . $payment_method . '_settings' );

  $activities = [];

  array_push($activities, $order_id_log);
  array_push($activities, $order_amount);
  array_push($activities, $order_billing);
  array_push($activities, $order_billing_address);
  array_push($activities, $order_billing_postcode);
  array_push($activities, $order_billing_phone);
  array_push($activities, $order_billing_email);

  foreach($activities as $act){
    if($gateway_options['debugmode'] === 'yes') {
      activity_log_callback($posted_data->data['payment_method'], $act, $gateway_options['debugmode']);
    }
  }
}

add_action('woocommerce_new_order', 'activity_log_on_order_creation', 10, 2);