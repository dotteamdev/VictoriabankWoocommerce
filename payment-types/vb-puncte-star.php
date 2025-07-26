<?php

add_filter('woocommerce_payment_gateways', 'vb_puncte_star_add_gateway_class');
function vb_puncte_star_add_gateway_class($gateways) {
	$gateways[] = 'WC_Victoriabank_Puncte_Star_payment';
	return $gateways;
}

add_action('plugins_loaded', 'vb_puncte_star_init_gateway_class');
function vb_puncte_star_init_gateway_class() {

	class WC_Victoriabank_Puncte_Star_payment extends WC_Payment_Gateway {

 		public function __construct() {
            $this->id = 'vb_puncte_star';
            $this->icon = apply_filters('woocommerce_gateway_icon', plugin_dir_url('').'vb-payment-plugin\assets\images\sc_logo_&_card_02.png');
            $this->method_title = 'Victoriabank Puncte Star';
            $this->method_description = __('WooCommerce Payment Gateway for Victoriabank Puncte Star', 'victoriabank-payment-gateway');

	        $this->supports = array(
		        'products'
	        );

	        $this->init_form_fields();

	        $this->init_settings();
            $this->needs_setup();

	        $this->title = 'Puncte Star';
	        $this->description = $this->get_option('description');
	        $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');

            $options = get_option($this->plugin_id . $this->id . '_settings');;
            $this->has_fields = $options['description'] ? true : false;

	        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

	        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
 		}

 		public function init_form_fields(){
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => __('Activate/Deactivate', 'victoriabank-payment-gateway'),
                    'type'        => 'checkbox',
                    'default'     => 'no',
                    'description' => __('Check box activate this payment method', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'title' => array(
                    'title'       => __('Title', 'victoriabank-payment-gateway'),
                    'type'        => 'text',
                    'description' => __('The title of the payment method shown to the customer during checkout', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => __('Description', 'victoriabank-payment-gateway'),
                    'type'        => 'textarea',
                    'description' => __('Description of the payment method displayed to the customer during checkout', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'terminal_id' => array(
                    'title'       => __('Terminal id', 'victoriabank-payment-gateway'),
                    'type'        => 'text',
                    'description' => __('This is id of payment terminal', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'merchant_id' => array(
                    'title'       => __('Merchant id', 'victoriabank-payment-gateway'),
                    'type'        => 'text',
                    'description' => __('This is id of merchant', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'testmode' => array(
                    'title'       => __('Test mode', 'victoriabank-payment-gateway'),
                    'label'       => __('Enable Test Mode', 'victoriabank-payment-gateway'),
                    'type'        => 'checkbox',
                    'default'     => 'no',
                    'description' => __('Check box activate test mode', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'debugmode' => array(
                    'title'       => __('Debug mode', 'victoriabank-payment-gateway'),
                    'label'       => __('Enable Debug Mode', 'victoriabank-payment-gateway'),
                    'type'        => 'checkbox',
                    'default'     => 'no',
                    'description' => __('Check box activate debug mode', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
                'transactiontype' => array(
                    'title'       => __('Transaction type', 'victoriabank-payment-gateway'),
                    'type'        => 'select',
                    'options' => array(
                        '1' => __('Charge', 'victoriabank-payment-gateway'),
                        '0' => __('Authorization', 'victoriabank-payment-gateway'), 
                    ),
                    'description' => __('Selects how payments will be processed. Charge immediately sends all transactions to the bank for settlement, Authorization only authorizes the order amount to the bank for later settlement.', 'victoriabank-payment-gateway'),
                    'desc_tip'    => true,
                ),
            );
	 	}

		public function payment_fields() {
			$options = get_option($this->plugin_id . $this->id . '_settings');

            echo '<div class="custom-payment-description">' . $options['description'] . '</div>';
		}

	 	public function payment_scripts() {
	
	 	}

		public function validate_fields() {

		}

        public function get_title() {
            $options = get_option($this->plugin_id . $this->id . '_settings');
            $title = $options['title'];

            return strlen($title) > 0 ? $title : $this->title;
        }

        public function get_icon() {
            $logo_file = plugin_dir_url(''). 'vb-payment-plugin/' . get_logo_puncte('Logo for Puncte Star');

            $existed_logo = file_exists($logo_file) ? '<img src="' . $logo_file . '" alt="Payment Icon">' : '<img src="' . $this->icon . '" alt="Payment Icon">';
            $enabled = get_logo_puncte('Enable logo for Puncte Star') === 'on';

            return $enabled ? $existed_logo : '';
        }

		public function process_payment($order_id) {

            return array(
                'result' => 'success',
                'redirect' => home_url() . '/payment?order_id=' . $order_id
            );
	 	}

		public function webhook() {
					
	 	}
 	}
}

function get_logo_puncte($field_name) {
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