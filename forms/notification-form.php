<html>
    <form method="post">
        <div class="setting-field">
            <label for="callback_url"><?php echo __('Callback URL', 'victoriabank-payment-gateway')?>:</label>
            <input type = 'text' class="regular-text" id="callback_url" name="callback_url_field" value="<?php echo get_notification_settings('Callback URL') ?>" disabled>
        </div>
    </form>
    <?php require_once('manually-process-form.php') ?>
</html>

<?php

function get_notification_settings($field_name) {
    global $wpdb;
    $table = $wpdb->prefix . 'vb_payments_settings';

    $sql = "SELECT * FROM ". $table;

    $notification_settings = $wpdb->get_results($sql);

    foreach($notification_settings as $setting) {
        if($setting->name === $field_name) {
            return $setting->value;
        }   
    }
}

?>