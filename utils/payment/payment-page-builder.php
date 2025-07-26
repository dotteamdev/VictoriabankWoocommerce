<?php 
function create_payment_form() {
    global $woocommerce;

    if (isset($_COOKIE['order_number'])) {
        $order_id = $_GET['order_id'];
        $order = wc_get_order($order_id);
    
        $payment_method = $order->get_payment_method();
    
        $options = get_option( 'woocommerce_' . $payment_method . '_settings' );

        $trtype = $options['transactiontype'] === "1" ? 0 : $options['transactiontype'];
    
        $data = set_body_data($order, $trtype);
    
        $test_mode_enabled = $options['testmode'];
    
        $gateway_url = $test_mode_enabled === 'yes' ? 'https://ecomt.victoriabank.md/cgi-bin/cgi_link' : 'https://vb059.vb.md/cgi-bin/cgi_link';

        $product_names = '';

        $product_names = getProductNames($order);

        $woocommerce->cart->empty_cart();
    
        if ($order) {
          ?>
            <form id="paymentForm" action="<?php echo $gateway_url ?>" method="post" hidden>
              AMOUNT	<input value="<?php echo $data['AMOUNT'] ?>" name="AMOUNT" /><br />
              ORDER	<input value="<?php echo $data['ORDER'] ?>" name="ORDER" /><br />
              DESC	<input value="<?php echo $product_names ?>" name="DESC" /><br />
              MERCH_NAME	<input value="<?php echo $data['MERCH_NAME'] ?>" name="MERCH_NAME" /><br />
              MERCH_URL	<input value="<?php echo $data['MERCH_URL'] ?>" name="MERCH_URL" /><br />
              MERCHANT	<input value="<?php echo $data['MERCHANT'] ?>" name="MERCHANT" /><br />
              TERMINAL	<input value="<?php echo $data['TERMINAL'] ?>" name="TERMINAL" /><br />
              EMAIL	<input value="<?php echo $data['EMAIL'] ?>" name="EMAIL" /><br />
              TRTYPE	<input value="<?php echo $trtype ?>" name="TRTYPE" /><br />
              COUNTRY	<input value="<?php echo $data['COUNTRY'] ?>" name="COUNTRY" /><br />
              NONCE	<input value="<?php echo $data['NONCE'] ?>" name="NONCE" /><br />
              BACKREF	<input value="<?php echo $data['BACKREF'] ?>" name="BACKREF" /><br />
              MERCH_GMT <input value="<?php echo $data['MERCH_GMT'] ?>" name="MERCH_GMT" /><br />
              TIMESTAMP <input value="<?php echo $data['TIMESTAMP'] ?>" name="TIMESTAMP" /><br />
              P_SIGN <input value="<?php echo $data['P_SIGN'] ?>" name="P_SIGN" /><br />
              LANG <input value="<?php echo $data['LANG'] ?>"  name="LANG" /><br />
              MERCH_ADDRESS <input value="<?php echo $data['MERCH_ADDRESS'] ?>" name="MERCH_ADDRESS" /><br />
              <?php 
                if($payment_method === 'vb_puncte_star') {
                  ?>
                    CURRENCY <input value="06S" name="CURRENCY" /><br />
                    ACCOUNT <input value="08" name="ACCNT_SEL" /><br />
                  <?php
                } else {
                  ?>
                    CURRENCY	<input value="<?php echo $data['CURRENCY'] ?>" name="CURRENCY" /><br />
                  <?php
                }
              ?>
              <input type="submit" value="Submit" />
            </form>
            <script>
              const elements = document.querySelectorAll('*');
              elements.forEach(function(element) {
                element.style.display = 'none';
              });
      
              const paymentForm = document.getElementById('paymentForm');
              paymentForm.submit();
            </script>
          <?php
        }
      }
    }
    
add_shortcode('checkout_form_custom', 'create_payment_form');
    
function create_checkout_page() {
    $page = array(
        'post_title' => 'Payment page',
        'post_content' => '[checkout_form_custom]',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_name' => 'payment'
    );
    
    wp_insert_post(wp_slash($page));
}
    
add_action('wp_head', 'remove_page_tab');

function remove_page_tab() {
    global $wpdb;
    
    $page_slug = 'payment';
    $page_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' AND post_status = 'publish'",
        $page_slug
    ) );
    
    if ( $page_id ) {
        ?>
        <style type="text/css">
            .page-item-<?php echo $page_id ?> {
                display: none !important;
            }
        </style>
        <?php
    }
}

function getProductNames($order) {
  $product_names = '';

  foreach ($order->get_items() as $item_id => $item) {
      $product = $item->get_product();
      $product_name = $product->get_name();
      
      if (mb_strlen($product_names . $product_name, 'UTF-8') <= 50) {
          $product_names .= $product_name . ', ';
      } else {
          break;
      }
  }

  return rtrim($product_names, ', ');
}