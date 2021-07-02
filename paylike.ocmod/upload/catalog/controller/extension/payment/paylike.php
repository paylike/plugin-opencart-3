<?php

class ControllerExtensionPaymentPaylike extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/paylike');
        $this->load->model('extension/payment/paylike');
        $this->load->model('checkout/order');
        $data['plugin_version'] = '1.1.0';
        $data['VERSION'] = VERSION;
        $data['active_mode']=$this->config->get('payment_paylike_api_mode');

        if ($this->config->get('payment_paylike_api_mode') == 'live') {
            $data['paylike_public_key'] = $this->config->get('payment_paylike_public_key_live');
        } else {
            $data['paylike_public_key'] = $this->config->get('payment_paylike_public_key_test');
        }

        if ($this->config->get('payment_paylike_checkout_title') != '') {
            $data['popup_title'] = $this->config->get('payment_paylike_checkout_title');
        } else {
            $data['popup_title'] = $this->config->get('config_name');
        }

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['lc']             = $this->session->data['language'];
        $data['mode']           = $this->config->get('payment_paylike_checkout_display_mode');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $order_info['currency_code'] = strtoupper($order_info['currency_code'])

        $data['order_id']  = $this->session->data['order_id'];
        $data['name']      = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $data['email']     = $order_info['email'];
        $data['telephone'] = $order_info['telephone'];

        $data['address'] = $order_info['payment_address_1'] . ', ';
        $data['address'] .= $order_info['payment_address_2'] != '' ? $order_info['payment_address_2'] . ', ' : '';
        $data['address'] .= $order_info['payment_city'] . ', ' . $order_info['payment_zone'] . ', ';
        $data['address'] .= $order_info['payment_country'] . ' - ' . $order_info['payment_postcode'];

        $data['ip']            = $order_info['ip'];
        $amount                = $this->getAmountsFromOrderAmount($order_info['total'], $order_info['currency_code']);
        $data['amount']        = $amount['paylike'];
        $data['currency_code'] = $order_info['currency_code'];

        $products       = $this->cart->getProducts();
        $products_array = array();
        $products_label = array();
        $p              = 0;
        foreach ($products as $key => $product) {
            $products_array[ $p ] = array(
                'ID'       => $product['product_id'],
                'name'     => $product['name'],
                'quantity' => $product['quantity']
            );
            $products_label[ $p ] = $product['quantity'] . 'x ' . $product['name'];
            $p ++;
        }
        $data['products'] = json_encode($products_array);
        if ($this->config->get('payment_paylike_checkout_description') != '') {
            $data['popup_description'] = $this->config->get('payment_paylike_checkout_description');
        } else {
            $data['popup_description'] = implode(", & ", $products_label);
        }

        return $this->load->view('extension/payment/paylike', $data);
    }

    public function process_payment()
    {
        $json = array();
        $this->load->language('extension/payment/paylike');

        if (is_null($this->request->post['trans_ref']) || $this->request->post['trans_ref'] == '') {
            $json['error'] = $this->language->get('error_no_transaction_found');
        }

        if (! $json) {
            $json = $this->validate_payment();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function validate_payment()
    {
        $this->load->language('extension/payment/paylike');
        $this->logger = new Log('paylike.log');
        $log          = $this->config->get('payment_paylike_logging') ? true : false;

        $json = array();
        $ref  = $this->request->post['trans_ref'];

        if ($log) {
            $this->logger->write('************');
        }
        if ($log) {
            $this->logger->write('Transaction validation. Transaction refference: ' . $ref);
        }

        $app_key = $this->config->get('payment_paylike_api_mode') == 'live' ? $this->config->get('payment_paylike_app_key_live') : $this->config->get('payment_paylike_app_key_test');
        require_once(DIR_SYSTEM . 'library/Paylike/Client.php');
        Paylike\Client::setKey($app_key);

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $order_info['currency_code'] = strtoupper($order_info['currency_code']);
        $amount = $this->getAmountsFromOrderAmount($order_info['total'], $order_info['currency_code']);

        $trans_data = Paylike\Transaction::fetch($ref);

        if (is_null($trans_data)) {
            if ($log) {
                $this->logger->write('Invalid transaction data. Unable to authorize transaction.');
            }
            $json['error'] = $this->language->get('error_invalid_transaction_data');

            return $json;
        }

        if (is_array($trans_data) && isset($trans_data['error']) && ! is_null($trans_data['error']) && $trans_data['error'] == 1) {
            if ($log) {
                $this->logger->write('Transaction error returned: ' . $trans_data['message']);
            }
            $json['error'] = $this->language->get('error_transaction_error_returned');

            return $json;
        } elseif (is_array($trans_data) && isset($trans_data[0]['message']) && ! is_null($trans_data[0]['message'])) {
            if ($log) {
                $this->logger->write('Transaction error returned: ' . $trans_data[0]['message']);
            }
            $json['error'] = $this->language->get('error_transaction_error_returned');

            return $json;
        }

        if (isset($trans_data['transaction'])) {
            if (isset($trans_data['transaction']['successful']) && (strtoUpper($trans_data['transaction']['currency']) == $order_info['currency_code']) && ($trans_data['transaction']['amount'] == $amount['paylike'])) {
                $order_captured = false;

                if ($this->config->get('payment_paylike_capture_mode') == 'instant') {
                    $data         = array(
                        'amount'   => $amount['paylike'],
                        'currency' => $order_info['currency_code']
                    );
                    $capture_data = Paylike\Transaction::capture($ref, $data);
                    if (! isset($capture_data['transaction'])) {
                        if ($log) {
                            $this->logger->write('Unable to capture amount of ' . $amount['paylike_formatted'] . ' (' . $order_info['currency_code'] . '). Order #' . $order_info['order_id'] . ' history updated.');
                        }
                    } else {
                        if ($log) {
                            $this->logger->write('Transaction finished. Captured amount: ' . $amount['paylike_formatted'] . ' (' . $order_info['currency_code'] . '). Order #' . $order_info['order_id'] . ' history updated.');
                        }
                        $order_captured = true;
                    }
                } else {
                    if ($log) {
                        $this->logger->write('Transaction authorized. Pending amount: ' . $amount['paylike_formatted'] . ' (' . $order_info['currency_code'] . '). Order #' . $order_info['order_id'] . ' history updated.');
                    }
                }

                if (! $order_captured) {
                    $type                = 'Authorize';
                    $transaction_amount  = 0;
                    $total_amount        = 0;
                    $comment             = 'Paylike transaction: ref:' . $ref . "\r\n" . 'Authorized amount: ' . $amount['paylike_formatted'] . ' (' . $order_info['currency_code'] . ')';
                    $new_order_status_id = $this->config->get('payment_paylike_authorize_status_id');
                    $json['success']     = $this->language->get('success_message_authorized');
                } else {
                    $type                = 'Capture';
                    $transaction_amount  = $amount['paylike_converted'];
                    $total_amount        = $amount['paylike_converted'];
                    $comment             = 'Paylike transaction: ref:' . $ref . "\r\n" . 'Captured amount: ' . $amount['paylike_formatted'] . ' (' . $order_info['currency_code'] . ')';
                    $new_order_status_id = $this->config->get('payment_paylike_capture_status_id');
                    $json['success']     = $this->language->get('success_message_captured');
                }

                $this->db->query("INSERT INTO `" . DB_PREFIX . "paylike_transaction` SET order_id = '" . $order_info['order_id'] . "', transaction_id = '" . $ref . "', transaction_type = '" . $type . "', transaction_currency = '" . $order_info['currency_code'] . "', order_amount = '" . $amount['paylike_converted'] . "', transaction_amount = '" . $transaction_amount . "', total_amount = '" . $total_amount . "', history = '0', date_added = NOW()");
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], $new_order_status_id, $comment);

                $json['redirect'] = $this->url->link('checkout/success', '', true);

                return $json;
            }
        }

        if ($log) {
            $this->logger->write('Transaction error. Empty transaction results.');
        }
        $json['error'] = $this->language->get('error_invalid_transaction_data');

        return $json;
    }

    private function getAmountsFromOrderAmount($order_amount, $currency_code)
    {
        $exponent_zero  = array(
            'BIF',
            'BYR',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'PYG',
            'RWF',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF'
        );
        $exponent_three = array( 'BHD', 'IQD', 'JOD', 'KWD', 'OMR', 'TND' );
        $exponent       = 2;
        if (in_array($currency_code, $exponent_zero)) {
            $exponent = 0;
        } elseif (in_array($currency_code, $exponent_three)) {
            $exponent = 3;
        }

        $multiplier = pow(10, $exponent);
        $amount     = array();

        $amount['order_amount']      = $order_amount;
        $amount['store_converted']   = $this->currency->format($amount['order_amount'], $currency_code, false, false);
        $amount['store_formatted']   = $this->currency->format($amount['order_amount'], $currency_code, false, true);
        $amount['paylike']           = ceil($amount['store_converted'] * $multiplier);
        $amount['paylike_converted'] = $this->currency->format($amount['paylike'] / $multiplier, $currency_code, 1, false);
        $amount['paylike_formatted'] = $this->currency->format($amount['paylike'] / $multiplier, $currency_code, 1, true);

        return $amount;
    }
}
