<form method="post">
    <div class="setting-field">
        <label for="merchant_name"><?php echo __('Merchant name', 'victoriabank-payment-gateway')?>:</label>
        <input type = 'text' class="regular-text" id="merchant_name" name="merchant_name_field" value="<?php echo $_POST["merchant_name_field"] ?? get_merchant_data('Merchant name') ?>" required>
    </div>
    <div class="setting-field">
        <label for="merchant_url"><?php echo __('Merchant URL', 'victoriabank-payment-gateway')?>:</label>
        <input type = 'text' class="regular-text" id="merchant_url" name="merchant_url_field" value="<?php echo $_POST["merchant_url_field"] ?? get_merchant_data('Merchant URL') ?>" required>
    </div>
    <div class="setting-field">
        <label for="merchant_address"><?php echo __('Merchant address', 'victoriabank-payment-gateway')?>:</label>
        <input type = 'text' class="regular-text" id="merchant_address" name="merchant_address_field" value="<?php echo $_POST["merchant_address_field"] ?? get_merchant_data('Merchant address') ?>" >
    </div>
    <div class="setting-field">
        <label for="return_policy"><?php echo __('Return/refund policy URL', 'victoriabank-payment-gateway')?>:</label>
        <input type = 'text' class="regular-text" id="return_policy" name="return_policy_field" value="<?php echo $_POST["return_policy_field"] ?? get_merchant_data('Return/refund policy URL') ?>" required>
    </div>
    <div class="setting-field">
        <label for="customer_service"><?php echo __('Customer service contact', 'victoriabank-payment-gateway')?>:</label>
        <input type = 'text' class="regular-text" id="customer_service" name="customer_service_field" value="<?php echo $_POST["customer_service_field"] ?? get_merchant_data('Customer service contact') ?>" required>
    </div>

    <input type="submit" value="<?php echo __('Submit', 'victoriabank-payment-gateway')?>" name="submit_merchant">
</form>

<?php
if(isset($_POST["submit_merchant"])) {
    save_merchant_data();
}

function set_merchant_data() {
    $merchant_data['Merchant name'] = $_POST["merchant_name_field"] ?? null;
    $merchant_data['Merchant URL'] = $_POST["merchant_url_field"] ?? null;
    $merchant_data['Merchant address'] = $_POST["merchant_address_field"]  ?? null;
    $merchant_data['Return/refund policy URL'] = $_POST["return_policy_field"]  ?? null;
    $merchant_data['Customer service contact'] = $_POST["customer_service_field"]  ?? null;

    return $merchant_data;
}

function save_merchant_data_in_db($table, $data, $db_instance) {
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

function save_merchant_data() {
    global $wpdb;

    $merchant_data = set_merchant_data();

    $table = $wpdb->prefix . 'vb_payments_settings';

    save_merchant_data_in_db($table, $merchant_data, $wpdb);
}

?>
