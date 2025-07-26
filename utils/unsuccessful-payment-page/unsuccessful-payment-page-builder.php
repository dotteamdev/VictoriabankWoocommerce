<?php

function create_unsuccessful_payment_block() {

    $customer_service = get_merchant_data('Customer service contact');

    ?>
    <div class="container">
        <p><?php echo __('Your payment failed. Please try again or contact our support team if the problem continues to occur.', 'victoriabank-payment-gateway') ?></p>
        <p><?php echo __('Customer service contact', 'victoriabank-payment-gateway') . ' - ' . $customer_service ?></p>
        <p><?php echo __('You can return to ', 'victoriabank-payment-gateway') ?><a href="<?php echo home_url(); ?>"><?php echo __('Homepage', 'victoriabank-payment-gateway') ?></a><?php echo __(' and continue shopping.', 'victoriabank-payment-gateway') ?></p>
    </div>
    <?php
}
    
add_shortcode('unsuccessful_payment_page', 'create_unsuccessful_payment_block');

function create_unsuccessful_payment_page() {
    $page = array(
      'post_title' => __('Unsuccessful payment', 'victoriabank-payment-gateway'),
      'post_content' => '[unsuccessful_payment_page]',
      'post_status' => 'publish',
      'post_type' => 'page',
      'post_name' => 'unsuccessful-payment'
    );
  
    wp_insert_post(wp_slash($page));
}

add_action('wp_head', 'remove_unsuccessful_payment_page_tab');

function remove_unsuccessful_payment_page_tab() {
    global $wpdb;
    
    $page_slug = 'unsuccessful-payment';
    $page_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' AND post_status = 'publish'",
        $page_slug
    ));
    
    if ($page_id) {
        ?>
        <style type="text/css">
            .page-item-<?php echo $page_id ?> {
                display: none !important;
            }
        </style>
        <?php
    }
}