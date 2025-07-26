<?php
function get_merchant_data($field_name) {
  global $wpdb;

  $table = $wpdb->prefix . 'vb_payments_settings';

  $sql = "SELECT * FROM ". $table;

  $merchant_data = $wpdb->get_results($sql);

  foreach($merchant_data as $setting) {
      if($setting->name === $field_name) {
          return $setting->value;
      }   
  }
}

function set_body_data($order, $transaction_type) {
  $payment_method = $order->get_payment_method();
  $options = get_option( 'woocommerce_' . $payment_method . '_settings' );
  
  $encryption_type = get_merchant_data('Encryption method');

  $amount = $order->get_total();
  $currency = get_woocommerce_currency();
  $orderId = format_order_id($order->id);
  $email = $order->get_billing_email();
  $merch_name = get_merchant_data('Merchant name');
  $merch_url = get_merchant_data('Merchant URL');
  $merch_id = $options['merchant_id'];
  $terminal_id = $options['terminal_id'];
  $merch_address = get_merchant_data('Merchant address');
  $merch_gmt = get_option('gmt_offset');
  $merch_timestamp = date("YmdHis");
  $nonce = bin2hex(random_bytes(14));
  $back_ref = wc_get_checkout_url() . 'order-received/' . $order->id . '/?key=' . $order->get_order_key();
  $p_sign = P_SIGN_ENCRYPT($orderId, $merch_timestamp, $transaction_type, $amount, $nonce, 'MD5');
  $language = explode("_", get_locale())[0];

  $body = array(
      'AMOUNT' => $amount,
      'CURRENCY' => $currency,
      'ORDER' => $orderId,
      'MERCH_NAME' => $merch_name,
      'MERCH_URL' => $merch_url,
      'MERCHANT' => $merch_id,
      'TERMINAL' => $terminal_id,
      'EMAIL' => $email,
      'MERCH_ADDRESS' => $merch_address,
      'COUNTRY' => 'MD',
      'MERCH_GMT' => $merch_gmt,
      'TIMESTAMP' => $merch_timestamp,
      'NONCE' => $nonce,
      'BACKREF' => $back_ref,
      'P_SIGN' => $p_sign,
      'LANG' => $language
  );

  return $body;
}

function format_order_id($order_id) {
  $length = strlen($order_id);

  if ($length < 6) {
      $missing_digits = 6 - $length;
      $padding = str_repeat('0', $missing_digits);
      $order = $padding . $order_id;
  }

  return $order;
}
?>