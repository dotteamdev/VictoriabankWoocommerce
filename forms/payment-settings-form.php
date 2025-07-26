<form method="post" enctype="multipart/form-data">
    <div class="setting-field" style="width: 520px;">
        <label for="logo_vb_visa_mastercard" style="width: 210px;"><?php echo __('Logo for Visa/Mastercard', 'victoriabank-payment-gateway')?>:</label>
        <div style="display: flex;">
            <input type="checkbox" id="display_logo_vb_visa_mastercard" name="display_logo_vb_visa_mastercard_field" style="margin-top: 6px; margin-right: 7px;" <?php echo checkDisplayedLogos('Enable logo for Visa/Mastercard') ?>>
            <input type="file" accept=".jpeg,.jpg,.png" id="logo_vb_visa_mastercard" name="logo_vb_visa_mastercard_field" style="width: 300px">
        </div>
    </div>
    <div class="setting-field" style="width: 520px;">
        <label for="logo_star_card"><?php echo __('Logo for Star Card Rate', 'victoriabank-payment-gateway')?>:</label>
        <div>
            <input type="checkbox" id="display_logo_star_card" name="display_logo_star_card_field" <?php echo checkDisplayedLogos('Enable logo for Star Card Rate') ?>>
            <input type ="file" accept=".jpeg,.jpg,.png" id="logo_star_card" name="logo_star_card_field" style="width: 300px">
        </div>        
    </div>
    <div class="setting-field" style="width: 520px;">
        <label for="logo_puncte_star"><?php echo __('Logo for Puncte Star', 'victoriabank-payment-gateway')?>:</label>
        <div>
            <input type="checkbox" id="display_logo_puncte_star" name="display_logo_puncte_star_field" <?php echo checkDisplayedLogos('Enable logo for Puncte Star') ?>>
            <input type ="file" accept=".jpeg,.jpg,.png" id="logo_puncte_star" name="logo_puncte_star_field" style="width: 300px">
        </div>
    </div>

    <input type="submit" value="<?php echo __('Submit', 'victoriabank-payment-gateway')?>" name="submit_payment">
</form>

<script>
    const logoVisaMaster = '<?php echo getLogoFileName('logo_vb_visa_mastercard_field', 'Logo for Visa/Mastercard') ?>'
    const logoStarCard = '<?php echo getLogoFileName('logo_star_card_field', 'Logo for Star Card Rate') ?>'
    const logoPuncteStar = '<?php echo getLogoFileName('logo_puncte_star_field', 'Logo for Puncte Star') ?>'

    setFileToField('logo_vb_visa_mastercard', 'Logo for Visa/Mastercard', logoVisaMaster);
    setFileToField('logo_star_card', 'Logo for Star Card Rate', logoStarCard);
    setFileToField('logo_puncte_star', 'Logo for Puncte Star', logoPuncteStar);
</script>

<?php
$default = $_COOKIE["set_defaults"];

if(isset($_POST["submit_payment"])) {
    save_payment_settings();
    upload_logos();
}

if($default === "true"){
    setcookie("set_defaults", "false");
    setDefault();
    ?> 
        <script>
            let checkboxes = document.querySelectorAll('input[type="checkbox"]');

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
        </script>
    <?php
}

function setDefault() {
    global $wpdb;

    $payment_settings['Enable logo for Visa/Mastercard'] = "on";
    $payment_settings['Enable logo for Star Card Rate'] = "on";
    $payment_settings['Enable logo for Puncte Star'] = "on";

    save_payment_data_in_db($wpdb->prefix . 'vb_payments_settings', $payment_settings, $wpdb);
}

function checkDisplayedLogos($setting_name) {
    $payment_settings = set_payment_settings();
    
    if($payment_settings[$setting_name] === 'on' || get_payment_data($setting_name) === 'on') {
        return 'checked';
    } else {
        return '';
    }
}

function getLogoFileName($input_name, $field_name) {
    return $_POST[$input_name] ?? get_payment_data($field_name);
}

function set_payment_settings() {
    $payment_settings['Logo for Visa/Mastercard'] = set_logo_path('logo_vb_visa_mastercard.png');
    $payment_settings['Logo for Star Card Rate'] = set_logo_path('logo_star_card.png');
    $payment_settings['Logo for Puncte Star'] = set_logo_path('logo_puncte_star.png');
    $payment_settings['Enable logo for Visa/Mastercard'] = $_POST["display_logo_vb_visa_mastercard_field"] ?? "off";
    $payment_settings['Enable logo for Star Card Rate'] = $_POST["display_logo_star_card_field"] ?? "off";
    $payment_settings['Enable logo for Puncte Star'] = $_POST["display_logo_puncte_star_field"] ?? "off";

    return $payment_settings;
}

function set_logo_path($key) {
    if(!$key) {
        return;
    }

    return '/assets/images/' . $key;
}

function get_payment_data($field_name) {
    global $wpdb;
    $table = $wpdb->prefix . 'vb_payments_settings';

    $sql = "SELECT * FROM ". $table;

    $payment_settings = $wpdb->get_results($sql);

    foreach($payment_settings as $setting) {
        if($setting->name === $field_name) {
            return $setting->value;
        }   
    }
}

function save_payment_data_in_db($table, $data, $db_instance) {
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

function save_payment_settings() {
    global $wpdb;

    $payment_data = set_payment_settings();

    $table = $wpdb->prefix . 'vb_payments_settings';

    save_payment_data_in_db($table, $payment_data, $wpdb);
}

function upload_logos() {
    $files = $_FILES;

    foreach($files as $key => $file) {
        if($file['name'] && $file['size'] > 128) {
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $logo_name = str_replace("_field","", $key);
            $file_tmp = $file['tmp_name'];
            
            $upload_dir = PLUGIN_DIR . '/assets/images';
            $destination = $upload_dir . '/' . $logo_name . '.' . $file_extension;

            if(is_uploaded_file($file_tmp) && move_uploaded_file($file_tmp, $destination)) {
                echo logo_name($key)." uploaded successfully!<br>";
            } else {
                echo "Error uploading ". logo_name($key) ."!<br>";
            }
        }
    }
}

function logo_name($key) {
    return ucfirst(str_replace(["_", "field"],[" ", ""], $key));
}

?>

<h3><?php echo __('Logo Settings', 'victoriabank-payment-gateway'); ?></h3>

<form method="post" enctype="multipart/form-data">
    <div class="setting-field">
        <label for="logo_visa_mastercard"><?php echo __('Logo for Visa/Mastercard', 'victoriabank-payment-gateway')?>:</label>
        <input type="file" accept="image/*" id="logo_visa_mastercard" name="logo_visa_mastercard_field">
        <label>
            <input type="checkbox" name="enable_logo_visa_mastercard_field" value="on" <?php echo get_payment_data('Enable logo for Visa/Mastercard') === 'on' ? 'checked' : ''; ?>>
            <?php echo __('Enable logo', 'victoriabank-payment-gateway'); ?>
        </label>
    </div>
    
    <div class="setting-field">
        <label for="logo_star_card_rate"><?php echo __('Logo for Star Card Rate', 'victoriabank-payment-gateway')?>:</label>
        <input type="file" accept="image/*" id="logo_star_card_rate" name="logo_star_card_rate_field">
        <label>
            <input type="checkbox" name="enable_logo_star_card_rate_field" value="on" <?php echo get_payment_data('Enable logo for Star Card Rate') === 'on' ? 'checked' : ''; ?>>
            <?php echo __('Enable logo', 'victoriabank-payment-gateway'); ?>
        </label>
    </div>
    
    <div class="setting-field">
        <label for="logo_puncte_star"><?php echo __('Logo for Puncte Star', 'victoriabank-payment-gateway')?>:</label>
        <input type="file" accept="image/*" id="logo_puncte_star" name="logo_puncte_star_field">
        <label>
            <input type="checkbox" name="enable_logo_puncte_star_field" value="on" <?php echo get_payment_data('Enable logo for Puncte Star') === 'on' ? 'checked' : ''; ?>>
            <?php echo __('Enable logo', 'victoriabank-payment-gateway'); ?>
        </label>
    </div>

    <h3><?php echo __('Email Customization', 'victoriabank-payment-gateway'); ?></h3>
    
    <div class="setting-field">
        <label for="email_confirmation_subject"><?php echo __('Order Confirmation Email Subject', 'victoriabank-payment-gateway')?>:</label>
        <input type="text" class="regular-text" id="email_confirmation_subject" name="email_confirmation_subject_field" 
               value="<?php echo esc_attr($_POST["email_confirmation_subject_field"] ?? get_payment_data('Email confirmation subject') ?? ''); ?>" 
               placeholder="<?php echo __('Leave empty for default: [Country] MerchantName: New order #OrderNumber', 'victoriabank-payment-gateway'); ?>">
        <p class="description"><?php echo __('Custom subject for order confirmation emails. Use placeholders: {merchant_name}, {order_number}, {country}', 'victoriabank-payment-gateway'); ?></p>
    </div>

    <div class="setting-field">
        <label for="email_confirmation_content"><?php echo __('Order Confirmation Email Additional Content', 'victoriabank-payment-gateway')?>:</label>
        <textarea class="large-text" rows="6" id="email_confirmation_content" name="email_confirmation_content_field" 
                  placeholder="<?php echo __('Custom content to add at the end of confirmation emails', 'victoriabank-payment-gateway'); ?>"><?php echo esc_textarea($_POST["email_confirmation_content_field"] ?? get_payment_data('Email confirmation content') ?? ''); ?></textarea>
        <p class="description"><?php echo __('HTML is allowed. Leave empty to use default content. Use placeholders: {merchant_name}, {merchant_url}, {return_policy}, {customer_service}', 'victoriabank-payment-gateway'); ?></p>
    </div>

    <div class="setting-field">
        <label for="email_refund_subject"><?php echo __('Refund Email Subject', 'victoriabank-payment-gateway')?>:</label>
        <input type="text" class="regular-text" id="email_refund_subject" name="email_refund_subject_field" 
               value="<?php echo esc_attr($_POST["email_refund_subject_field"] ?? get_payment_data('Email refund subject') ?? ''); ?>" 
               placeholder="<?php echo __('Leave empty for default subject', 'victoriabank-payment-gateway'); ?>">
        <p class="description"><?php echo __('Custom subject for refund emails. Use placeholders: {merchant_name}, {order_number}', 'victoriabank-payment-gateway'); ?></p>
    </div>

    <div class="setting-field">
        <label for="email_refund_content"><?php echo __('Refund Email Additional Content', 'victoriabank-payment-gateway')?>:</label>
        <textarea class="large-text" rows="4" id="email_refund_content" name="email_refund_content_field" 
                  placeholder="<?php echo __('Custom content to add at the end of refund emails', 'victoriabank-payment-gateway'); ?>"><?php echo esc_textarea($_POST["email_refund_content_field"] ?? get_payment_data('Email refund content') ?? ''); ?></textarea>
        <p class="description"><?php echo __('HTML is allowed. Use placeholders: {merchant_name}, {return_policy}, {customer_service}', 'victoriabank-payment-gateway'); ?></p>
    </div>

    <div class="setting-field">
        <label>
            <input type="checkbox" name="hide_woocommerce_promotion_field" value="on" <?php echo get_payment_data('Hide WooCommerce promotion') === 'on' ? 'checked' : ''; ?>>
            <?php echo __('Hide WooCommerce promotional text from emails', 'victoriabank-payment-gateway'); ?>
        </label>
        <p class="description"><?php echo __('Removes "Process your orders on the go. Get the app" text from emails', 'victoriabank-payment-gateway'); ?></p>
    </div>

    <input type="submit" value="<?php echo __('Submit', 'victoriabank-payment-gateway')?>" name="submit_payment_settings">
</form>
