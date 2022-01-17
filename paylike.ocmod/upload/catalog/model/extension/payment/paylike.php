<?php

class ModelExtensionPaymentPaylike extends Model
{
    public function getMethod($address, $total)
    {
        /** Extract database table data paylike_admin. */
        $query = $this->db->query("SELECT table_name
                                   FROM information_schema.tables
                                   WHERE table_schema = '" . DB_DATABASE . "'
                                   AND table_name = '" . DB_PREFIX . "paylike_admin'"
                               );
        /** Check if table was extracted. */
        if ($query->num_rows > 0) {
            return array();
        }

        $status         = false;
        $status_enabled = $this->config->get('payment_paylike_status');
        /** Check if Paylike module status is enabled, then set status = true. */
        if ($status_enabled) {
            $status = true;
        }

        if ($status) {
            $query = $this->db->query("SELECT *
                                       FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('payment_paylike_geo_zone') . "'
                                       AND country_id = '" . (int) $address['country_id'] . "'
                                       AND (zone_id = '" . (int) $address['zone_id'] . "'
                                       OR zone_id = '0')"
                                    );

            if ($this->config->get('payment_paylike_minimum_total') > 0 && $this->config->get('payment_paylike_minimum_total') > $total) {
                $status = false;
            } elseif (! $this->config->get('payment_paylike_geo_zone')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        }

        $method_data = array();

        if ($status) {
            $logos        = $this->config->get('payment_paylike_checkout_cc_logo');
            $logos_string = '';
            if (is_array($logos)) {
                foreach ($logos as $logo) {
                    $logos_string .= '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/paylike/' . $logo . '" style="display-inline;height:25px;margin-left:5px;" />';
                }
            }
            $method_data = array(
                'code'       => 'paylike',
                'title'      => $this->config->get('payment_paylike_method_title'),
                'terms'      => $logos_string,
                'sort_order' => $this->config->get('payment_paylike_sort_order')
            );
        }

        return $method_data;
    }
}
