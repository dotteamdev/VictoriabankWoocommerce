<?php
/**
 * Plugin Name: Victoriabank payment
 * Text Domain: victoriabank-payment-gateway
 * Domain Path: /languages/
 * Description: Plugin for adding to your WooCommerce shop Victoriabank payments.
 * Author: Victoriabank
 * Author URI: https://www.victoriabank.md/
 * Version: 1.0.0
 */

if(!defined('ABSPATH')) {
	exit;
}

include(plugin_dir_path( __FILE__ ).'payment-types/vb-visa-master.php');
include(plugin_dir_path( __FILE__ ).'payment-types/vb-star-card-rate.php');
include(plugin_dir_path( __FILE__ ).'payment-types/vb-puncte-star.php');

include(plugin_dir_path( __FILE__ ).'security/P_SIGN_ENCRYPT.php');
include(plugin_dir_path( __FILE__ ).'security/P_SIGN_DECRYPT.php');

require_once(plugin_dir_path( __FILE__ ).'/utils/activity-logger/activity-logger.php');
require_once(plugin_dir_path( __FILE__ ).'/utils/activity-logger/responses.php');

require_once(plugin_dir_path( __FILE__ ).'/utils/payment/payment-data-collector.php');
require_once(plugin_dir_path( __FILE__ ).'/utils/payment/payment-page-builder.php');

require_once(plugin_dir_path( __FILE__ ).'/utils/unsuccessful-payment-page/unsuccessful-payment-page-builder.php');

require_once(plugin_dir_path( __FILE__ ).'/utils/actions.php');
require_once(plugin_dir_path( __FILE__ ).'/utils/trtypes.php');

const PLUGIN_DIR = ABSPATH . 'wp-content/plugins/vb-payment-plugin';

function enqueue_styles() {
  wp_enqueue_style( 'general_settings_style', plugins_url('settings.css', __FILE__));
  wp_enqueue_script( 'general_settings_script', plugins_url('settings.js', __FILE__));
}
add_action( 'admin_enqueue_scripts', 'enqueue_styles' );

add_filter( 'plugin_action_links_vb-payment-plugin/vb-payment-plugin.php', 'vb_payments_plugin_settings_link' );

function vb_payments_plugin_settings_link( $links ) {
	$url = esc_url( add_query_arg(
		'page',
		'vb-payments-plugin-settings',
		get_admin_url() . 'admin.php'
	) );

	$settings_link = "<a href='$url'>" . __('Settings', 'victoriabank-payment-gateway') . '</a>';

	array_unshift(
		$links,
		$settings_link
	);
	return $links;
}

function vb_payments_plugin_setting_page() {
    add_options_page(
        __('Victoriabank payments settings', 'victoriabank-payment-gateway'), 
        __('VB payments settings', 'victoriabank-payment-gateway'), 
        'manage_options', 
        'vb-payments-plugin-settings', 
        'settings_form'
    );
}
 
add_action('admin_menu', 'vb_payments_plugin_setting_page');

function settings_form() {
    $default_tab = 'merchant';
    $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <nav class="tab">
                <a href="?page=vb-payments-plugin-settings&tab=merchant" class="tablinks <?php if($tab==='merchant' || $tab===null):?>active<?php endif; ?>"><?php echo __('Merchant data', 'victoriabank-payment-gateway') ?></a>
                <a href="?page=vb-payments-plugin-settings&tab=connection" class="tablinks <?php if($tab==='connection'):?>active<?php endif; ?>"><?php echo __('Connection settings', 'victoriabank-payment-gateway') ?></a>
                <a href="?page=vb-payments-plugin-settings&tab=notification" class="tablinks <?php if($tab==='notification'):?>active<?php endif; ?>"><?php echo __('Notification settings', 'victoriabank-payment-gateway') ?></a>
                <a href="?page=vb-payments-plugin-settings&tab=payment-settings" class="tablinks <?php if($tab==='payment-settings'):?>active<?php endif; ?>"><?php echo __('Payment settings', 'victoriabank-payment-gateway') ?></a>
            </nav>

            <?php switch($tab) :
              case 'merchant':
                echo '<div id="merchant" class="tabcontent" style="display: block">';
                echo '<h2>'. __('Merchant data', 'victoriabank-payment-gateway') .'</h2>';
                require_once('forms/merchant-form.php');
                echo '</div>';
                break;
              case 'connection':
                echo '<div id="connection" class="tabcontent">';
			          echo '<h2>'. __('Connection settings', 'victoriabank-payment-gateway') .'</h2>';
                require_once('forms/connection-form.php');
                echo '</div>';
                break;
              case 'notification':
                echo '<div id="notification" class="tabcontent">';
			          echo '<h2>'. __('Notification of payments', 'victoriabank-payment-gateway') .'</h2>';
                require_once('forms/notification-form.php');
                echo '</div>';
                break;
              case 'payment-settings':
                echo '<div id="payment-settings" class="tabcontent">';
                echo '<h2>'. __('Payment settings', 'victoriabank-payment-gateway') .'</h2>';
                require_once('forms/payment-settings-form.php');
                echo '</div>';
                break;
            endswitch; ?>
        </div>
    <?php
}

function create_settings_table() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
    
  $table_name = $wpdb->prefix . 'vb_payments_settings';

  $sql = "CREATE TABLE " . $table_name . " (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    value VARCHAR(100) NOT NULL
    ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

function create_transactions_table() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  $transaction_table_name = $wpdb->prefix . 'vb_transactions';

  $sql = "CREATE TABLE " . $transaction_table_name . " (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id int(20) NOT NULL,
    action int(1) NOT NULL,
    rc int(2) NOT NULL,
    approval VARCHAR(6) NOT NULL,
    rrn VARCHAR(12) NOT NULL,
    int_ref VARCHAR(32) NOT NULL,
    timestamp_bank VARCHAR(14) NOT NULL,
    nonce VARCHAR(64) NOT NULL,
    status ENUM ('processing','completed','refunded') NOT NULL,
    eci VARCHAR(2) NULL
    ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

function init_plugin() {
  global $wpdb;
  global $woocommerce;

  create_settings_table();
  create_transactions_table();
  create_checkout_page();
  create_email_template('customer-processing-order', 'new-order-by-card-payment');
  create_email_template('customer-refunded-order', 'order-refunded');
  create_unsuccessful_payment_page();
  
  setcookie("set_defaults", "true");

  $base_url = '';

  if (substr(home_url(), -1) === '/') {
      $base_url = rtrim(home_url(), '/');
  } else {
      $base_url = home_url();
  }

  $data['Callback URL'] = $base_url . '/finalize-order/';
  $data['WooCommerce version'] = $woocommerce->version;

  save_notification_settings_in_db_t($wpdb->prefix . 'vb_payments_settings', $data, $wpdb);
}

register_activation_hook( __FILE__, 'init_plugin');

function create_email_template($template_name, $source_template) {
  $email_template_name = '';
  $template_path = get_stylesheet_directory() . '/woocommerce/emails/' . $template_name . '.php';

  if (file_exists($template_path)) {
    $email_template_name = get_stylesheet_directory() . '/woocommerce/emails/' . $template_name . '.php';
  } else {
    $email_template_name = ABSPATH . '/wp-content/plugins/woocommerce/templates/emails/' . $template_name . '.php';
  }

  $email_template = fopen($email_template_name, "w");
  file_put_contents($email_template_name, '');

  $content = file_get_contents(PLUGIN_DIR . '/utils/email/' . $source_template . '.php');
  fwrite($email_template, $content);
  fclose($email_template);
}

function check_woocommerce_version() {
  global $woocommerce;
  global $wpdb;

  if (class_exists('WooCommerce')) {
      $current_version = $woocommerce->version;
      $previous_version = get_merchant_data('WooCommerce version');

      if (version_compare($current_version, $previous_version, '>')) {
        create_email_template('customer-processing-order', 'new-order-by-card-payment');
        create_email_template('customer-refunded-order', 'order-refunded');

        $data['WooCommerce version'] = $woocommerce->version;

        save_notification_settings_in_db_t($wpdb->prefix . 'vb_payments_settings', $data, $wpdb);
      } else {
          return;
      }
  }
}

add_action('init', 'check_woocommerce_version');

load_plugin_textdomain( 'victoriabank-payment-gateway', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

function victoriabank_payment_gateway_load_translation() {
  $plugin_dir = basename( dirname( __FILE__ ) ) . '/languages';
  load_plugin_textdomain( 'victoriabank-payment-gateway', false, $plugin_dir );
}

add_action( 'plugins_loaded', 'victoriabank_payment_gateway_load_translation' );

function save_data_local_storage($order_id) {
  setcookie('order_number', $order_id);
}

add_action('woocommerce_new_order', 'save_data_local_storage');

function check_payment() {
  global $wpdb;
  global $woocommerce;

  if (
      !empty($_SERVER['HTTP_REFERER']) && !empty($_POST) &&
      (strpos($_SERVER['HTTP_REFERER'], 'https://ecomt.victoriabank.md/') !== false ||
      strpos($_SERVER['HTTP_REFERER'], 'https://vb059.vb.md/') !== false ||
      strpos($_SERVER['HTTP_REFERER'], 'https://vb059.vb.md:8443/') !== false)
  ) {

    if(strpos($_POST['RC'], '-') !== false || ((int) $_POST['ACTION'] !== 0 && (int) $_POST['ACTION'] !== 1)) {
      $woocommerce->cart->empty_cart();

      $order = wc_get_order((int) $_POST['ORDER']);
      $order->update_status('failed');

      redirect_to_unsuccess_page();
      return;
    }

    $encryption_type = get_merchant_data('Encryption method');

    $check = P_SIGN_DECRYPT($_POST['P_SIGN'], $_POST['ACTION'], $_POST['RC'], $_POST['RRN'], $_POST['ORDER'], $_POST['AMOUNT'], 'MD5');

    sleep(15);

    $order = wc_get_order((int) $_POST['ORDER']);
    $payment_method = $order->get_payment_method();
    $gateway_options = get_option( 'woocommerce_' . $payment_method . '_settings' );

    $existent_order = get_transaction_data($_POST['ORDER']);

    if(!$existent_order) {
      $transaction_data = array(
        'order_id' => $_POST['ORDER'],
        'action' => $_POST['ACTION'],
        'rc' => $_POST['RC'],
        'approval' => $_POST['APPROVAL'],
        'rrn' => $_POST['RRN'],
        'int_ref' => $_POST['INT_REF'],
        'timestamp_bank' => $_POST['TIMESTAMP'],
        'nonce' => $_POST['NONCE'],
        'status' => 'processing',
        'eci' => isset($_POST['ECI']) ? $_POST['ECI'] : null
      );
  
      $wpdb->insert(
          $wpdb->prefix . 'vb_transactions',
          $transaction_data
      );
    }

    $existent_order = get_transaction_data($_POST['ORDER']);
    if($gateway_options['transactiontype'] === '1' && $existent_order->status !== 'completed') {
      $test_mode_enabled = $gateway_options['testmode'];
      $gateway_url = $test_mode_enabled === 'yes' ? 'https://ecomt.victoriabank.md/cgi-bin/cgi_link' : 'https://vb059.vb.md/cgi-bin/cgi_link';
      
      $timestamp = date("YmdHis");
      $nonce = bin2hex(random_bytes(14));

      $body = array(
        'AMOUNT' => $_POST['AMOUNT'],
        'CURRENCY' => $_POST['CURRENCY'],
        'ORDER' => $_POST['ORDER'],
        'DESC' => getProductNames($order),
        'MERCH_NAME' => $_POST['MERCH_NAME'],
        'MERCH_URL' => $_POST['MERCH_URL'],
        'MERCHANT' => $_POST['MERCHANT'],
        'TERMINAL' => $_POST['TERMINAL'],
        'EMAIL' => $_POST['EMAIL'],
        'TRTYPE' => 21,
        'COUNTRY' => $_POST['COUNTRY'],
        'NONCE' => $nonce,
        'BACKREF' => wc_get_checkout_url() . 'order-received/' . $order->id . '/?key=' . $order->get_order_key(),
        'MERCH_GMT' => $_POST['MERCH_GMT'],
        'TIMESTAMP' => $timestamp,
        'P_SIGN' => P_SIGN_ENCRYPT($_POST["ORDER"], $timestamp, 21, $_POST['AMOUNT'], $nonce, 'MD5'),
        'LANG' => $_POST['LANG'],
        'MERCH_ADDRESS' => $_POST['MERCH_ADDRESS'],
        'RRN' => $_POST['RRN'],
        'INT_REF' => $_POST['INT_REF'],
        'ACCNT_SEL' => $order->get_payment_method() === 'vb_puncte_star' ? '08' : ''
      );

      $options = array(
          'body' => $body,
      );

      $response = wp_remote_post($gateway_url, $options);

      $error_message = '';
      $timedout_error = 'cURL error 28: Operation timed out after';
      if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        activity_log_callback($payment_method, '#' . $_POST['ORDER'] . ' Error: ' . $error_message, $gateway_options['debugmode']);
      }

      if(!is_wp_error($response) || str_contains($error_message, $timedout_error)) {
        $wpdb->update(
          $wpdb->prefix . 'vb_transactions',
          array('status' => 'completed'),
          array('order_id' => $_POST['ORDER']),
          array('%s'),
          array('%d')
        );

        $order->update_status('completed');
      }
    }

    if($check === 'OK') {                
	    $woocommerce->cart->empty_cart();

      $order->payment_complete();
    }

    $action = getActionName($_POST['ACTION']);
    $trtype = getTrTypeName($_POST['TRTYPE']);

    if ($gateway_options['debugmode'] === 'yes') {
      if ($check === 'OK' && $_POST['ACTION'] === 0) {
          activity_log_callback($payment_method, '#' . ltrim($_POST['ORDER'], '0') . __(' Transaction "', 'victoriabank-payment-gateway') . $trtype . __('" was made successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
      } else {
          activity_log_callback($payment_method, '#' . ltrim($_POST['ORDER'], '0') . __(' Transaction "', 'victoriabank-payment-gateway') . $trtype . __('" wasn\'t finalized successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
          activity_log_callback($payment_method, '#' . ltrim($_POST['ORDER'], '0') . __(' Response code value : ', 'victoriabank-payment-gateway') . ResponseCodes::getValueByName(strpos($_POST['RC'], '-') !== false ? str_replace("-", "Dif", $_POST['RC']) : "Dif" . ltrim($_POST['RC'], '0')), $gateway_options['debugmode']);
      }
      activity_log_callback($payment_method, '#' . ltrim($_POST['ORDER'], '0') . __(' Action status : ', 'victoriabank-payment-gateway') . Actions::getValueByName($action), $gateway_options['debugmode']);
    }
  }
}
add_action('wp', 'check_payment');

function redirect_to_unsuccess_page() {
  $base_url = '';

  if (substr(home_url(), -1) === '/') {
      $base_url = rtrim(home_url(), '/');
  } else {
      $base_url = home_url();
  }

  wp_redirect($base_url . '/unsuccessful-payment/', 301);
}

function handle_on_woocommerce_order_refunded($order_id, $refund_id) {
  global $wpdb;

  $order = wc_get_order($order_id);

  $payment_method = $order->get_payment_method();
  $gateway_options = get_option('woocommerce_' . $payment_method . '_settings');

  $gateway_url = $gateway_options['testmode'] === 'yes' ? 'https://ecomt.victoriabank.md/cgi-bin/cgi_link' : 'https://vb059.vb.md/cgi-bin/cgi_link';

  $timestamp = date("YmdHis");
  $nonce = bin2hex(random_bytes(14));

  $data = set_body_data($order, $gateway_options['transactiontype']);

  $transaction = get_transaction_data($data["ORDER"]);

  $refund_amount = $order->get_refunds()[0]->amount;

  $update_transaction = false;
  if ($transaction && ($transaction->status === 'processing' || $transaction->status === 'completed')) {
      $update_transaction = true;
  }

  if ($update_transaction) {
      $wpdb->update(
          $wpdb->prefix . 'vb_transactions',
          array('status' => 'refunded'),
          array('order_id' => $data["ORDER"]),
          array('%s'),
          array('%d')
      );

      $body = array(
          'AMOUNT' => $refund_amount,
          'CURRENCY' => $payment_method = $order->get_payment_method() === 'vb_puncte_star' ? '06S' : $data['CURRENCY'],
          'ORDER' => $data["ORDER"],
          'DESC' => getProductNames($order),
          'MERCH_NAME' => $data['MERCH_NAME'],
          'MERCH_URL' => $data['MERCH_URL'],
          'MERCHANT' => $data['MERCHANT'],
          'TERMINAL' => $data['TERMINAL'],
          'EMAIL' => $data['EMAIL'],
          'TRTYPE' => 24,
          'COUNTRY' => $data['COUNTRY'],
          'NONCE' => $nonce,
          'BACKREF' => wc_get_checkout_url() . 'order-received/' . $order->id . '/?key=' . $order->get_order_key(),
          'MERCH_GMT' => $data['MERCH_GMT'],
          'TIMESTAMP' => $timestamp,
          'P_SIGN' => P_SIGN_ENCRYPT($data["ORDER"], $timestamp, 24, $refund_amount, $nonce, 'MD5'),
          'LANG' => $data['LANG'],
          'MERCH_ADDRESS' => $data['MERCH_ADDRESS'],
          'RRN' => $transaction->rrn,
          'INT_REF' => $transaction->int_ref,
          'ACCNT_SEL' => $payment_method = $order->get_payment_method() === 'vb_puncte_star' ? '08' : ''
      );

      $response = wp_remote_post($gateway_url, array(
        'body' => $body,
      ));

      $action = getActionName($response['ACTION']);
    
      if (is_wp_error($response)) {
          $error_message = $response->get_error_message();
          activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Error: ', 'victoriabank-payment-gateway') . $error_message, $gateway_options['debugmode']);
      } else {
          if($response['RC'] && $response['RC'] !== '00') {
              activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Response code value : ', 'victoriabank-payment-gateway') . ResponseCodes::getValueByName(strpos($_POST['RC'], '-') !== false ? str_replace("-", "Dif", $_POST['RC']) : "Dif" . ltrim($_POST['RC'], '0')), $gateway_options['debugmode']);
          }
      }
      activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Action status : ', 'victoriabank-payment-gateway') . Actions::getValueByName($action), $gateway_options['debugmode']);
  }
}

add_action('woocommerce_order_refunded', 'handle_on_woocommerce_order_refunded', 10, 2);

function on_payment_complete($order_id, $old_status, $new_status, $order) {
  global $wpdb;

  $table_name = $wpdb->prefix . 'vb_transactions';
  $field_name = 'status';

  $payment_method = $order->get_payment_method();
  $gateway_options = get_option('woocommerce_' . $payment_method . '_settings');

  $gateway_url = $gateway_options['testmode'] === 'yes' ? 'https://ecomt.victoriabank.md/cgi-bin/cgi_link' : 'https://vb059.vb.md/cgi-bin/cgi_link';

  $timestamp = date("YmdHis");
  $nonce = bin2hex(random_bytes(14));

  $data = set_body_data($order, $gateway_options['transactiontype']);

  $transaction = get_transaction_data($order_id);
  $encryption_type = get_merchant_data('Encryption method');

  $update_transaction = false;
  $new_value = '';

  if (
      ($transaction && $transaction->status === 'processing' && $new_status === 'completed' && $old_status !== 'completed') ||
      ($new_status === 'refunded' && $old_status !== 'refunded')
  ) {
      if ($new_status === 'completed') {
          $trtype = 21;
          $new_value = 'completed';
          activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Transaction "Sales completion" was made successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
      } else if ($new_status === 'refunded') {
          $trtype = 24;
          $new_value = 'refunded';
          activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Transaction "Refund" was made successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
      }

      $update_transaction = true;
  }

  if ($update_transaction) {
      $wpdb->update(
          $table_name,
          array($field_name => $new_value),
          array('order_id' => $data['ORDER']),
          array('%s'),
          array('%d')
      );

      $body = array(
          'AMOUNT' => $data['AMOUNT'],
          'CURRENCY' => $payment_method = $order->get_payment_method() === 'vb_puncte_star' ? '06S' : $data['CURRENCY'],
          'ORDER' => $data['ORDER'],
          'DESC' => getProductNames($order),
          'MERCH_NAME' => $data['MERCH_NAME'],
          'MERCH_URL' => $data['MERCH_URL'],
          'MERCHANT' => $data['MERCHANT'],
          'TERMINAL' => $data['TERMINAL'],
          'EMAIL' => $data['EMAIL'],
          'TRTYPE' => $trtype,
          'COUNTRY' => $data['COUNTRY'],
          'NONCE' => $nonce,
          'BACKREF' => wc_get_checkout_url() . 'order-received/' . $order->id . '/?key=' . $order->get_order_key(),
          'MERCH_GMT' => $data['MERCH_GMT'],
          'TIMESTAMP' => $timestamp,
          'P_SIGN' => P_SIGN_ENCRYPT($data["ORDER"], $timestamp, $trtype, $data['AMOUNT'], $nonce, 'MD5'),
          'LANG' => $data['LANG'],
          'MERCH_ADDRESS' => $data['MERCH_ADDRESS'],
          'RRN' => $transaction->rrn,
          'INT_REF' => $transaction->int_ref,
          'ACCNT_SEL' => $payment_method = $order->get_payment_method() === 'vb_puncte_star' ? '08' : ''
      );

      $options = array(
          'body' => $body,
      );

      $response = wp_remote_post($gateway_url, $options);

      $action = getActionName($response['ACTION']);
    
      if (is_wp_error($response)) {
          $error_message = $response->get_error_message();
            activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Error: ', 'victoriabank-payment-gateway') . $error_message, $gateway_options['debugmode']);
      } else {
          if($response['RC'] && $response['RC'] !== '00') {
              activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Response code value : ', 'victoriabank-payment-gateway') . ResponseCodes::getValueByName(strpos($_POST['RC'], '-') !== false ? str_replace("-", "Dif", $_POST['RC']) : "Dif" . ltrim($_POST['RC'], '0')), $gateway_options['debugmode']);
          }
      }
      activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Action status : ', 'victoriabank-payment-gateway') . Actions::getValueByName($action), $gateway_options['debugmode']);
  }
}

add_action( 'woocommerce_order_status_changed', 'on_payment_complete', 10, 4 );

function get_transaction_data($order_id) {
  global $wpdb;

  $table = $wpdb->prefix . 'vb_transactions';

  $sql = $wpdb->prepare("SELECT * FROM $table WHERE order_id = %d LIMIT 1", $order_id);

  $transaction_data = $wpdb->get_row($sql);

  return $transaction_data ?? null;
}

register_deactivation_hook(__FILE__, 'deactivate_plugin');

function deactivate_plugin() {
    global $wpdb;

    $page_slugs = ['payment', 'unsuccessful_payment'];

    foreach ($page_slugs as $page_slug) {
        $page_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' AND post_status = 'publish'",
            $page_slug
        ));

        if ($page_id) {
            wp_delete_post($page_id, true);
        }
    }
    
    $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();

    foreach ($payment_gateways as $gateway) {
        $vb_gateways = array('vb_visa_mastercard', 'vb_star_card_rate', 'vb_puncte_star');
        if(in_array($gateway->id, $vb_gateways)) {
          $gateway->enabled = 'no';
          $gateway->init_form_fields();
          $gateway->process_admin_options();
        }
    }
}

register_uninstall_hook(__FILE__, 'delete_plugin');

function delete_plugin() {
    global $wpdb;

    $page_slugs = ['payment', 'unsuccessful_payment'];

    foreach ($page_slugs as $page_slug) {
        $page_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' AND post_status = 'publish'",
            $page_slug
        ));

        if ($page_id) {
            wp_delete_post($page_id, true);
        }
    }

    $settings_table = $wpdb->prefix . 'vb_payments_settings';
    $transaction_table = $wpdb->prefix . 'vb_transactions';

    $wpdb->query("DROP TABLE IF EXISTS $settings_table");
    $wpdb->query("DROP TABLE IF EXISTS $transaction_table");
}

add_action('wp', 'auto_content_cleanup');

function auto_content_cleanup()
{
  $log_file_visa_mastercard = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/vb_visa_mastercard.log';
  $log_file_star_card = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/vb_star_card_rate.log';
  $log_file_puncte_star = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/vb_puncte_star.log';

  cleanupLogFile($log_file_visa_mastercard);
  cleanupLogFile($log_file_star_card);
  cleanupLogFile($log_file_puncte_star);
}

function cleanupLogFile($log_file_path)
{
  $log_file_data = file_get_contents($log_file_path);
  $activities = explode("\n", $log_file_data);

  $current_date = date('Y-m-d');
  $thirty_days_ago = strtotime('-30 days', strtotime($current_date));

  foreach ($activities as $key => $activity) {
    $date = substr($activity, 1, 10);

    $date_obj = new DateTime($date);
    $formatted_date = $date_obj->format('Y-m-d');

    $date_from_string = strtotime($formatted_date);

    if ($date_from_string < $thirty_days_ago) {
      unset($activities[$key]);
    }
  }

  $result = implode("\n", $activities);
  $file_handle = fopen($log_file_path, 'w');
  fwrite($file_handle, $result);
  fclose($file_handle);
}

add_action('init', 'register_callback_endpoint');
function register_callback_endpoint() {
    add_rewrite_endpoint('finalize-order', EP_ROOT | EP_PAGES);
}

add_action('template_redirect', 'handle_callback_request');
function handle_callback_request() {
  global $wp_query;
  global $wpdb;
  global $woocommerce;

  if (isset($wp_query->query_vars['finalize-order'])) {
    $callbackData = $_POST;

    if($callbackData && $callbackData['CARD'] && $callbackData['APPROVAL']) {
      $order = wc_get_order(ltrim($callbackData['ORDER'], '0'));
      $payment_method = $order->get_payment_method();
      $gateway_options = get_option('woocommerce_' . $payment_method . '_settings');

      if ($order) {
          $mailer = WC()->mailer();

          $transaction = array(
            'RRN' => $callbackData['RRN'],
            'APPROVAL' => $callbackData['APPROVAL'],
            'CARD' => $callbackData['CARD']
          );

          $country = WC()->countries->get_base_country();
          $full_country_name = WC()->countries->countries[$country];
          $return_policy = get_merchant_data('Return/refund policy URL');
          $customer_service = get_merchant_data('Customer service contact');
          $merchant_name =  get_merchant_data('Merchant name');
          $merchant_url = get_merchant_data('Merchant URL');
          $encryption_type = get_merchant_data('Encryption method');

          $template = 'emails/customer-processing-order.php';
          $subject = '[' . $country . '] ' . $merchant_name . __(': New order #', 'victoriabank-payment-gateway') . ltrim($callbackData['ORDER'], '0');
          $content = wc_get_template_html($template, array(
              'order' => $order,
              'email_heading' => $subject,
              'vb_payment' => true,
              'date_label' => __('Date of transaction', 'victoriabank-payment-gateway'),
              'rrn_label' => __('Retrieval Reference Number (RRN)','victoriabank-payment-gateway'),
              'approval_label' => __('Authorization code','victoriabank-payment-gateway'),
              'card_label' => __('Card number','victoriabank-payment-gateway'),
              'merchant_url_label' => __('Merchant URL','victoriabank-payment-gateway'),
              'transaction' => $transaction,
              'email' => $order->get_billing_email(),
              'sent_to_admin' => true,
              'plain_text' => '',
              'additional_content' => 
                __('Congratulations on the sale. You are welcome to', 'victoriabank-payment-gateway') . ' <a href="' . $merchant_url . '">' . $merchant_name . '</a> (' . $full_country_name . 
                __(')<br><br> If you aren\'t satisfied in good quality you can return it by this ', 'victoriabank-payment-gateway') . '<a href="'. $return_policy . '">' . __('return/refund policy', 'victoriabank-payment-gateway') . '</a>' .
                __('.<br><br> Also if you need another help contact us - <b>', 'victoriabank-payment-gateway') . $customer_service . 
                __('<b>.<br><br> Process your orders on the go. <a href="https://woocommerce.com/mobile">Get the app</a>', 'victoriabank-payment-gateway'),
          ));

          $mailer->send($order->get_billing_email(), $subject, $content);

          activity_log_callback($payment_method, '#' . ltrim($callbackData['ORDER'], '0') . __(' Email was sent to customer', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
      
          $check = P_SIGN_DECRYPT($callbackData['P_SIGN'], $callbackData['ACTION'], $callbackData['RC'], $callbackData['RRN'], $callbackData['ORDER'], $callbackData['AMOUNT'], 'MD5');

          $existent_order = get_transaction_data($_POST['ORDER']);

          if(!$existent_order) {
            $transaction_data = array(
              'order_id' => $callbackData['ORDER'],
              'action' => $callbackData['ACTION'],
              'rc' => $callbackData['RC'],
              'approval' => $callbackData['APPROVAL'],
              'rrn' => $callbackData['RRN'],
              'int_ref' => $callbackData['INT_REF'],
              'timestamp_bank' => $callbackData['TIMESTAMP'],
              'nonce' => $callbackData['NONCE'],
              'status' => 'processing',
              'eci' => isset($callbackData['ECI']) ? $callbackData['ECI'] : null
            );
        
            $wpdb->insert(
                $wpdb->prefix . 'vb_transactions',
                $transaction_data
            );
          }

          $existent_order = get_transaction_data($_POST['ORDER']);
          if($gateway_options['transactiontype'] === '1' && $existent_order->status !== 'completed') {
            $test_mode_enabled = $gateway_options['testmode'];
            $gateway_url = $test_mode_enabled === 'yes' ? 'https://ecomt.victoriabank.md/cgi-bin/cgi_link' : 'https://vb059.vb.md/cgi-bin/cgi_link';
            
            $timestamp = date("YmdHis");
            $nonce = bin2hex(random_bytes(14));

            $body = array(
              'AMOUNT' => $callbackData['AMOUNT'],
              'CURRENCY' => $callbackData['CURRENCY'],
              'ORDER' => $callbackData['ORDER'],
              'DESC' => getProductNames($order),
              'MERCH_NAME' => $callbackData['MERCH_NAME'],
              'MERCH_URL' => $callbackData['MERCH_URL'],
              'MERCHANT' => $callbackData['MERCHANT'],
              'TERMINAL' => $callbackData['TERMINAL'],
              'EMAIL' => $callbackData['EMAIL'],
              'TRTYPE' => 21,
              'COUNTRY' => $callbackData['COUNTRY'],
              'NONCE' => $nonce,
              'BACKREF' => wc_get_checkout_url() . 'order-received/' . $order->id . '/?key=' . $order->get_order_key(),
              'MERCH_GMT' => $callbackData['MERCH_GMT'],
              'TIMESTAMP' => $timestamp,
              'P_SIGN' => P_SIGN_ENCRYPT($callbackData["ORDER"], $timestamp, 21, $callbackData['AMOUNT'], $nonce, 'MD5'),
              'LANG' => $callbackData['LANG'],
              'MERCH_ADDRESS' => $callbackData['MERCH_ADDRESS'],
              'RRN' => $callbackData['RRN'],
              'INT_REF' => $callbackData['INT_REF'],
              'ACCNT_SEL' => $order->get_payment_method() === 'vb_puncte_star' ? '08' : ''
            );

            $options = array(
                'body' => $body,
            );

            $response = wp_remote_post($gateway_url, $options);

            $error_message = '';
            $timedout_error = 'cURL error 28: Operation timed out after';
            if (is_wp_error($response)) {
              $error_message = $response->get_error_message();
              activity_log_callback($payment_method, '#' . $callbackData['ORDER'] . ' Error: ' . $error_message, $gateway_options['debugmode']);
            }

            if(!is_wp_error($response) || str_contains($error_message, $timedout_error)) {
              $wpdb->update(
                $wpdb->prefix . 'vb_transactions',
                array('status' => 'completed'),
                array('order_id' => $callbackData['ORDER']),
                array('%s'),
                array('%d')
              );

              $order->update_status('completed');
            }
          }

          if($check === 'OK') {                
            $woocommerce->cart->empty_cart();

            $order->payment_complete();
          }

          $action = getActionName($callbackData['ACTION']);
          $trtype = getTrTypeName($callbackData['TRTYPE']);

          if ($gateway_options['debugmode'] === 'yes') {
            if ($check === 'OK' && $callbackData['ACTION'] === 0) {
                activity_log_callback($payment_method, '#' . ltrim($callbackData['ORDER'], '0') . __(' Transaction "', 'victoriabank-payment-gateway') . $trtype . __('" was made successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
            } else {
                activity_log_callback($payment_method, '#' . ltrim($callbackData['ORDER'], '0') . __(' Transaction "', 'victoriabank-payment-gateway') . $trtype . __('" wasn\'t finalized successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
                activity_log_callback($payment_method, '#' . ltrim($callbackData['ORDER'], '0') . __(' Response code value : ', 'victoriabank-payment-gateway') . ResponseCodes::getValueByName(strpos($callbackData['RC'], '-') !== false ? str_replace("-", "Dif", $callbackData['RC']) : "Dif" . ltrim($callbackData['RC'], '0')), $gateway_options['debugmode']);
            }
            activity_log_callback($payment_method, '#' . ltrim($callbackData['ORDER'], '0') . __(' Action status : ', 'victoriabank-payment-gateway') . Actions::getValueByName($action), $gateway_options['debugmode']);
          }
      }
    }
  }
}

function finalize_order_flush_rewrite_rules() {
  flush_rewrite_rules();
}
add_action( 'init', 'finalize_order_flush_rewrite_rules' );

function save_notification_settings_in_db_t($table, $data, $db_instance) {
  $db_data = [];
  $format = [];

  foreach($data as $key => $value) {
      if($value){
          array_push($db_data, array(
              'name' => $key,
              'value' => $value,
          ));
          array_push($format, array(
              '%s',
              '%s',
          ));
      }
  }

  foreach($db_data as $key => $setting) {
      $db_instance->delete( $table, array('name' => $setting['name']));
      
      $db_instance->insert( $table, $setting, $format[$key] );
  }

  return 'Success';
}

add_filter('woocommerce_email_recipient_customer_processing_order', 'disable_default_email', 10, 2);

function disable_default_email($recipient, $order) {
    $payment_method = $order->get_payment_method();

    if ($payment_method === 'vb_visa_mastercard' || $payment_method === 'vb_star_card_rate' || $payment_method === 'vb_puncte_star') {
        $recipient = '';
    }

    return $recipient;
}

function update_page_content_by_title() {
  $page_title = 'Checkout';

  $page = get_page_by_title($page_title);

  if ($page) {
      $page_id = $page->ID;

      $new_content = '[woocommerce_checkout]';

      wp_update_post(array(
          'ID' => $page_id,
          'post_content' => $new_content,
      ));
  }
}

add_action('wp', 'update_page_content_by_title');

?>
