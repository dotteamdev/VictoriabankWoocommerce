<form method="post" enctype="multipart/form-data">
    <div class="setting-field">
        <label for="public_key"><?php echo __('Public key', 'victoriabank-payment-gateway')?>:</label>
        <input type="file" accept=".pem,.pub" id="public_key" name="public_key_field" required>
    </div>
    <div class="setting-field">
        <label for="bank_public_key"><?php echo __('Bank public key', 'victoriabank-payment-gateway')?>:</label>
        <input type ="file" accept=".pem,.pub" id="bank_public_key" name="bank_public_key_field" required>
    </div>
    <div class="setting-field">
        <label for="private_key"><?php echo __('Private key', 'victoriabank-payment-gateway')?>:</label>
        <input type ="file" accept=".pem,.pub" id="private_key" name="private_key_field" required>
    </div>
    <div class="setting-field">
        <label for="private_key_password"><?php echo __('Private Key Password', 'victoriabank-payment-gateway')?>:</label>
        <input type ="password" id="private_key_password" name="private_key_password_field" value="<?php echo $_POST["private_key_password_field"] ?? get_connection_data('Private Key Password') ?>">
    </div>

    <input type="submit" value="<?php echo __('Submit', 'victoriabank-payment-gateway')?>" name="submit_connection">
</form>

<script>
    const publicKeyFileName = '<?php echo getFileName('public_key_field', 'Public key') ?>'
    const bankPublicKeyFileName = '<?php echo getFileName('bank_public_key_field', 'Bank public key') ?>'
    const privateKeyFileName = '<?php echo getFileName('private_key_field', 'Private key') ?>'

    setFileToField('public_key', 'Public key', publicKeyFileName);
    setFileToField('bank_public_key', 'Bank public key', bankPublicKeyFileName);
    setFileToField('private_key', 'Private key', privateKeyFileName);
</script>

<?php
if(isset($_POST["submit_connection"])) {
    save_connection_settings();
    upload_keys();
}

function getFileName($input_name, $field_name) {
    return $_POST[$input_name] ?? get_connection_data($field_name);
}

function set_connection_settings() {
    $connection_settings['Public key'] = set_key_path($_FILES["public_key_field"]['name']);
    $connection_settings['Bank public key'] = set_key_path($_FILES["bank_public_key_field"]['name']);
    $connection_settings['Private key'] = set_key_path($_FILES["private_key_field"]['name']);
    $connection_settings['Private Key Password'] = $_POST["private_key_password_field"] ?? null;

    return $connection_settings;
}

function set_key_path($key) {
    if(!$key) {
        return;
    }

    return '/assets/uploads/secure/' . $key;
}

function get_connection_data($field_name) {
    global $wpdb;
    $table = $wpdb->prefix . 'vb_payments_settings';

    $sql = "SELECT * FROM ". $table;

    $connection_settings = $wpdb->get_results($sql);

    foreach($connection_settings as $setting) {
        if($setting->name === $field_name) {
            return $setting->value;
        }   
    }
}

function save_connection_data_in_db($table, $data, $db_instance) {
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

function save_connection_settings() {
    global $wpdb;

    $merchant_data = set_connection_settings();

    $table = $wpdb->prefix . 'vb_payments_settings';

    save_connection_data_in_db($table, $merchant_data, $wpdb);
}

function upload_keys() {
    $files = $_FILES;

    foreach($files as $key => $file) {
        if($file['name'] && $file['size'] > 16) {
            $file_name = sanitize_file_name($file['name']);
            $file_tmp = $file['tmp_name'];
            
            $upload_dir = PLUGIN_DIR . '/assets/uploads/secure';
            $destination = $upload_dir . '/' . $file_name;
            
            if(is_uploaded_file($file_tmp) && move_uploaded_file($file_tmp, $destination)) {
                echo key_type($key)." uploaded successfully!<br>";
            } else {
                echo "Error uploading ". key_type($key) ."!<br>";
            }
        }
    }
}

function key_type($key) {
    return ucfirst(str_replace(["_", "field"],[" ", ""], $key));
}

?>
