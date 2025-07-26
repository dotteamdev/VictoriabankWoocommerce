<html>
    <h3><?php echo __('Manually process callback data', 'victoriabank-payment-gateway')?></h3>
    <form method="post">
        <div class="setting-field">
            <label for="terminal">TERMINAL:</label>
            <input type="text" class="regular-text" id="terminal" name="TERMINAL">
        </div>
        <div class="setting-field">
            <label for="trtype">TRTYPE:</label>
            <select class="regular-text" name="TRTYPE">
                <option value="21" selected>Complete transaction (21)</option>
                <option value="24">Refund (24)</option>
            </select>
        </div>
        <div class="setting-field">
            <label for="order">ORDER:</label>
            <input type="text" class="regular-text" id="order" name="ORDER">
        </div>
        <div class="setting-field">
            <label for="amount">AMOUNT:</label>
            <input type="text" class="regular-text" id="amount" name="AMOUNT">
        </div>
        <div class="setting-field">
            <label for="currency">CURRENCY:</label>
            <input type="text" class="regular-text" id="currency" name="CURRENCY">
        </div>
        <div class="setting-field" style="display: none;">
            <label for="account">ACCNT_SEL:</label>
            <input type="text" class="regular-text" id="account" name="ACCNT_SEL" value="08" disabled>
        </div>
        <div class="setting-field">
            <label for="rrn">RRN:</label>
            <input type="text" class="regular-text" id="rrn" name="RRN">
        </div>
        <div class="setting-field">
            <label for="int_ref">INT_REF:</label>
            <input type="text" class="regular-text" id="int_ref" name="INT_REF">
        </div>
        <input type="submit" value="<?php echo __('Process', 'victoriabank-payment-gateway')?>" name="process_manual"/>
    </form>
</html>

<?php
if(isset($_POST["process_manual"])) {
    set_manual_process_data();
}

function set_manual_process_data() {
    global $wpdb;

    $order_id = $_POST["ORDER"];
    $order = wc_get_order($order_id);
    
    $payment_method = $order->get_payment_method();
    $gateway_options = get_option( 'woocommerce_' . $payment_method . '_settings' );
    
    $gateway_url = $gateway_options['testmode'] === 'yes' ? 'https://ecomt.victoriabank.md/cgi-bin/cgi_link' : 'https://vb059.vb.md/cgi-bin/cgi_link';

    $timestamp = date("YmdHis");
    $nonce = bin2hex(random_bytes(14));

    $data = set_body_data($order, $gateway_options['transactiontype']);

    if ($_POST['TRTYPE'] === '21') {
        $new_value = 'completed';
        activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Transaction "Sales completion" was made successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
    } else if ($_POST['TRTYPE'] === '24') {
        $new_value = 'refunded';
        activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Transaction "Refund" was made successfully', 'victoriabank-payment-gateway'), $gateway_options['debugmode']);
    }

    $wpdb->update(
        $wpdb->prefix . 'vb_transactions',
        array('status' => $new_value),
        array('order_id' => $order_id),
        array('%s'),
        array('%d')
    );

    $body = array(
        'AMOUNT' => $_POST['AMOUNT'],
        'CURRENCY' => $_POST['CURRENCY'],
        'ACCNT_SEL' => $_POST['ACCNT_SEL'] ?? '',
        'ORDER' => $_POST["ORDER"],
        'DESC' => getProductNames($order),
        'MERCH_NAME' => $data['MERCH_NAME'],
        'MERCH_URL' => $data['MERCH_URL'],
        'MERCHANT' => $data['MERCHANT'],
        'TERMINAL' => $_POST['TERMINAL'],
        'EMAIL' => $data['EMAIL'],
        'TRTYPE' => $_POST['TRTYPE'],
        'COUNTRY' => $data['COUNTRY'],
        'NONCE' => $nonce,
        'BACKREF' => home_url() . '/wp-admin/options-general.php?page=vb-payments-plugin-settings&tab=notification',
        'MERCH_GMT' => $data['MERCH_GMT'],
        'TIMESTAMP' => $timestamp,
        'P_SIGN' => P_SIGN_ENCRYPT($_POST["ORDER"], $timestamp, $_POST['TRTYPE'], $_POST['AMOUNT'], $nonce, 'MD5'),
        'LANG' => $data['LANG'],
        'MERCH_ADDRESS' => $data['MERCH_ADDRESS'],
        'RRN' => $_POST['RRN'],
        'INT_REF' => $_POST['INT_REF'],
    );
    
    $options = array(
        'body' => $body,
    );
    
    $response = wp_remote_post($gateway_url, $options);

    $action = getActionName($response['ACTION']);
    
    if (is_wp_error($response)) {
        $notification = showNotification(true);
        echo $notification;

        $error_message = $response->get_error_message();
        activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Error: ', 'victoriabank-payment-gateway') . $error_message, $gateway_options['debugmode']);
    } else {
        if($response['ACTION'] && $response['ACTION'] !== '0') {
            $notification = showNotification(true);
            echo $notification;
        } else {
            $notification = showNotification();
            echo $notification;
        }

        if($response['RC'] && $response['RC'] !== '00') {
            activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Response code value : ', 'victoriabank-payment-gateway') . ResponseCodes::getValueByName(str_replace("-", "Dif", $response['RC'])), $gateway_options['debugmode']);
        }
    }
    activity_log_callback($payment_method, '#' . ltrim($order_id, '0') . __(' Action status : ', 'victoriabank-payment-gateway') . Actions::getValueByName($action), $gateway_options['debugmode']);
}

add_action('admin_notices', 'set_manual_process_data');

function showNotification($error = false) {
    if($error) {
        return '<div class="notice notice-error is-dismissible">
            <p>' . __('Transaction failed', 'victoriabank-payment-gateway') . '</p>
        </div>';
    } else {
        return '<div class="notice notice-success is-dismissible">
            <p>' . __('Transaction was made successfully, you can check result on the email', 'victoriabank-payment-gateway') . '</p>
        </div>';
    }
}

?>
