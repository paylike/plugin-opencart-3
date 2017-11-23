<?php

class ControllerExtensionPaymentPaylike extends Controller {
	private $error = array();
	private $oc_token = '';

	public function index() {
		$this->oc_token = version_compare( VERSION, '3.0.0.0', '>=' ) ? 'user_token' : $this->oc_token = 'token';
		$this->load->model( 'setting/setting' );
		$this->load->language( 'extension/payment/paylike' );
		$this->load->model( 'setting/store' );
		$this->load->model( 'tool/image' );
		$this->load->model( 'extension/payment/paylike' );

		$this->model_extension_payment_paylike->install();
		$upgrade = false;
		$query   = $this->db->query( "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . "paylike_admin'" );
		if ( $query->num_rows > 0 ) {
			$this->model_extension_payment_paylike->upgrade();
			$upgrade = true;
		}
		if ( is_null( $this->config->get( 'payment_paylike_method_title' ) ) ) {
			$this->session->data['error_warning'] = $this->language->get( 'text_setting_review_required' );
		}

		if ( ( $this->request->server['REQUEST_METHOD'] == 'POST' ) && $this->validate() ) {

			$this->model_setting_setting->editSetting( 'payment_paylike', $this->request->post );
			if ( version_compare( VERSION, '3.0.0.0', '>=' ) ) {
				$redirect_url = $this->url->link( 'marketplace/extension', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&type=payment', true );
			} else {
				$this->request->post['paylike_status']     = $this->request->post['payment_paylike_status'];
				$this->request->post['paylike_sort_order'] = $this->request->post['payment_paylike_sort_order'];
				$this->model_setting_setting->editSetting( 'paylike', $this->request->post );
				$redirect_url = $this->url->link( 'extension/extension', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&type=payment', true );
			}

			$this->session->data['success'] = $this->language->get( 'text_success' );

			$this->response->redirect( $redirect_url );
		}

		$this->document->setTitle( $this->language->get( 'heading_title' ) );
		$data['heading_title'] = $this->language->get( 'heading_title' );

		$data['button_save']             = $this->language->get( 'button_save' );
		$data['button_cancel']           = $this->language->get( 'button_cancel' );
		$data['button_paylike_payments'] = $this->language->get( 'button_paylike_payments' );

		$data['text_disabled']            = $this->language->get( 'text_disabled' );
		$data['text_test']                = $this->language->get( 'text_test' );
		$data['text_enabled']             = $this->language->get( 'text_enabled' );
		$data['text_live']                = $this->language->get( 'text_live' );
		$data['text_all_zones']           = $this->language->get( 'text_all_zones' );
		$data['text_description']         = $this->language->get( 'text_description' );
		$data['text_edit_settings']       = $this->language->get( 'text_edit_settings' );
		$data['text_general_settings']    = $this->language->get( 'text_general_settings' );
		$data['text_advanced_settings']   = $this->language->get( 'text_advanced_settings' );
		$data['text_capture_instant']     = $this->language->get( 'text_capture_instant' );
		$data['text_capture_delayed']     = $this->language->get( 'text_capture_delayed' );
		$data['text_display_mode_popup']  = $this->language->get( 'text_display_mode_popup' );
		$data['text_display_mode_inline'] = $this->language->get( 'text_display_mode_inline' );

		$data['entry_payment_method_title']  = $this->language->get( 'entry_payment_method_title' );
		$data['entry_checkout_popup_title']  = $this->language->get( 'entry_checkout_popup_title' );
		$data['entry_checkout_cc_logo']      = $this->language->get( 'entry_checkout_cc_logo' );
		$data['entry_checkout_display_mode'] = $this->language->get( 'entry_checkout_display_mode' );
		$data['entry_public_key_test']       = $this->language->get( 'entry_public_key_test' );
		$data['entry_app_key_test']          = $this->language->get( 'entry_app_key_test' );
		$data['entry_public_key_live']       = $this->language->get( 'entry_public_key_live' );
		$data['entry_app_key_live']          = $this->language->get( 'entry_app_key_live' );
		$data['entry_api_mode']              = $this->language->get( 'entry_api_mode' );
		$data['entry_capture_mode']          = $this->language->get( 'entry_capture_mode' );
		$data['entry_authorize_status_id']   = $this->language->get( 'entry_authorize_status_id' );
		$data['entry_capture_status_id']     = $this->language->get( 'entry_capture_status_id' );
		$data['entry_refund_status_id']      = $this->language->get( 'entry_refund_status_id' );
		$data['entry_void_status_id']        = $this->language->get( 'entry_void_status_id' );
		$data['entry_payment_enabled']       = $this->language->get( 'entry_payment_enabled' );
		$data['entry_logging']               = $this->language->get( 'entry_logging' );
		$data['entry_minimum_total']         = $this->language->get( 'entry_minimum_total' );
		$data['entry_geo_zone']              = $this->language->get( 'entry_geo_zone' );
		$data['entry_sort_order']            = $this->language->get( 'entry_sort_order' );
		$data['entry_store']                 = $this->language->get( 'entry_store' );

		$data['help_payment_method_title']       = $this->language->get( 'help_payment_method_title' );
		$data['help_checkout_popup_title']       = $this->language->get( 'help_checkout_popup_title' );
		$data['help_checkout_popup_description'] = $this->language->get( 'help_checkout_popup_description' );
		$data['help_checkout_cc_logo']           = $this->language->get( 'help_checkout_cc_logo' );
		$data['help_checkout_display_mode']      = $this->language->get( 'help_checkout_display_mode' );
		$data['help_public_key_test']            = $this->language->get( 'help_public_key_test' );
		$data['help_app_key_test']               = $this->language->get( 'help_app_key_test' );
		$data['help_public_key_live']            = $this->language->get( 'help_public_key_live' );
		$data['help_app_key_live']               = $this->language->get( 'help_app_key_live' );
		$data['help_api_mode']                   = $this->language->get( 'help_api_mode' );
		$data['help_capture_mode']               = $this->language->get( 'help_capture_mode' );
		$data['help_authorize_status_id']        = $this->language->get( 'help_authorize_status_id' );
		$data['help_capture_status_id']          = $this->language->get( 'help_capture_status_id' );
		$data['help_refund_status_id']           = $this->language->get( 'help_refund_status_id' );
		$data['help_void_status_id']             = $this->language->get( 'help_void_status_id' );
		$data['help_payment_enabled']            = $this->language->get( 'help_payment_enabled' );
		$data['help_logging']                    = $this->language->get( 'help_logging' );
		$data['help_minimum_total']              = $this->language->get( 'help_minimum_total' );
		$data['help_geo_zone']                   = $this->language->get( 'help_geo_zone' );
		$data['help_sort_order']                 = $this->language->get( 'help_sort_order' );
		$data['help_store']                      = $this->language->get( 'help_store' );


		if ( isset( $this->error['error_payment_method_title'] ) ) {
			$data['error_payment_method_title'] = $this->error['error_payment_method_title'];
		} else {
			$data['error_payment_method_title'] = '';
		}

		if ( isset( $this->error['error_public_key_test'] ) ) {
			$data['error_public_key_test'] = $this->error['error_public_key_test'];
		} else {
			$data['error_public_key_test'] = '';
		}

		if ( isset( $this->error['error_app_key_test'] ) ) {
			$data['error_app_key_test'] = $this->error['error_app_key_test'];
		} else {
			$data['error_app_key_test'] = '';
		}

		if ( isset( $this->error['error_public_key_live'] ) ) {
			$data['error_public_key_live'] = $this->error['error_public_key_live'];
		} else {
			$data['error_public_key_live'] = '';
		}

		if ( isset( $this->error['error_app_key_live'] ) ) {
			$data['error_app_key_live'] = $this->error['error_app_key_live'];
		} else {
			$data['error_app_key_live'] = '';
		}

		if ( isset( $this->error['warning'] ) ) {
			$data['error_warning'] = $this->error['warning'];
		} elseif ( isset( $this->session->data['error_warning'] ) && $this->session->data['error_warning'] != '' ) {
			$data['error_warning']                = $this->session->data['error_warning'];
			$this->session->data['error_warning'] = '';
		} else {
			$data['error_warning'] = '';
		}

		if ( isset( $this->session->data['success'] ) ) {
			$data['success'] = $this->session->data['success'];
			unset( $this->session->data['success'] );
		} else {
			$data['success'] = '';
		}

		if ( $upgrade ) {
			$data['success'] = $this->language->get( 'text_upgrade' );
		}


		if ( isset( $this->request->post['payment_paylike_method_title'] ) ) {
			$data['payment_paylike_method_title'] = $this->request->post['payment_paylike_method_title'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_method_title' ) ) ) {
			$data['payment_paylike_method_title'] = $this->config->get( 'payment_paylike_method_title' );
		} else {
			$data['payment_paylike_method_title'] = $this->language->get( 'default_payment_method_title' );
		}

		if ( isset( $this->request->post['payment_paylike_checkout_title'] ) ) {
			$data['payment_paylike_checkout_title'] = $this->request->post['payment_paylike_checkout_title'];
		} else {
			$data['payment_paylike_checkout_title'] = $this->config->get( 'payment_paylike_checkout_title' );
		}

		if ( isset( $this->request->post['payment_paylike_checkout_description'] ) ) {
			$data['payment_paylike_checkout_description'] = $this->request->post['payment_paylike_checkout_description'];
		} else {
			$data['payment_paylike_checkout_description'] = $this->config->get( 'payment_paylike_checkout_description' );
		}

		$data['ccLogos'] = $this->model_extension_payment_paylike->getCcLogos();
		if ( isset( $this->request->post['payment_paylike_checkout_cc_logo'] ) ) {
			$data['payment_paylike_checkout_cc_logo'] = $this->request->post['payment_paylike_checkout_cc_logo'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_checkout_cc_logo' ) ) ) {
			$data['payment_paylike_checkout_cc_logo'] = $this->config->get( 'payment_paylike_checkout_cc_logo' );

		} else {
			$data['payment_paylike_checkout_cc_logo'] = $this->language->get( 'default_payment_paylike_checkout_cc_logo' );
		}

		if ( isset( $this->request->post['payment_paylike_checkout_display_mode'] ) ) {
			$data['payment_paylike_checkout_display_mode'] = $this->request->post['payment_paylike_checkout_display_mode'];
		} else {
			$data['payment_paylike_checkout_display_mode'] = $this->config->get( 'payment_paylike_checkout_display_mode' );
		}

		if ( isset( $this->request->post['payment_paylike_public_key_test'] ) ) {
			$data['payment_paylike_public_key_test'] = $this->request->post['payment_paylike_public_key_test'];
		} else {
			$data['payment_paylike_public_key_test'] = $this->config->get( 'payment_paylike_public_key_test' );
		}

		if ( isset( $this->request->post['payment_paylike_app_key_test'] ) ) {
			$data['payment_paylike_app_key_test'] = $this->request->post['payment_paylike_app_key_test'];
		} else {
			$data['payment_paylike_app_key_test'] = $this->config->get( 'payment_paylike_app_key_test' );
		}

		if ( isset( $this->request->post['payment_paylike_public_key_live'] ) ) {
			$data['payment_paylike_public_key_live'] = $this->request->post['payment_paylike_public_key_live'];
		} else {
			$data['payment_paylike_public_key_live'] = $this->config->get( 'payment_paylike_public_key_live' );
		}

		if ( isset( $this->request->post['payment_paylike_app_key_live'] ) ) {
			$data['payment_paylike_app_key_live'] = $this->request->post['payment_paylike_app_key_live'];
		} else {
			$data['payment_paylike_app_key_live'] = $this->config->get( 'payment_paylike_app_key_live' );
		}

		if ( isset( $this->request->post['payment_paylike_api_mode'] ) ) {
			$data['payment_paylike_api_mode'] = $this->request->post['payment_paylike_api_mode'];
		} else {
			$data['payment_paylike_api_mode'] = $this->config->get( 'payment_paylike_api_mode' );
		}

		if ( isset( $this->request->post['payment_paylike_capture_mode'] ) ) {
			$data['payment_paylike_capture_mode'] = $this->request->post['payment_paylike_capture_mode'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_capture_mode' ) ) ) {
			$data['payment_paylike_capture_mode'] = $this->config->get( 'payment_paylike_capture_mode' );
		} else {
			$data['payment_paylike_capture_mode'] = 'instant';
		}

		if ( isset( $this->request->post['payment_paylike_authorize_status_id'] ) ) {
			$data['payment_paylike_authorize_status_id'] = $this->request->post['payment_paylike_authorize_status_id'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_authorize_status_id' ) ) ) {
			$data['payment_paylike_authorize_status_id'] = $this->config->get( 'payment_paylike_authorize_status_id' );
		} else {
			$data['payment_paylike_authorize_status_id'] = 1;
		}

		if ( isset( $this->request->post['payment_paylike_capture_status_id'] ) ) {
			$data['payment_paylike_capture_status_id'] = $this->request->post['payment_paylike_capture_status_id'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_capture_status_id' ) ) ) {
			$data['payment_paylike_capture_status_id'] = $this->config->get( 'payment_paylike_capture_status_id' );
		} else {
			$data['payment_paylike_capture_status_id'] = 5;
		}

		if ( isset( $this->request->post['payment_paylike_refund_status_id'] ) ) {
			$data['payment_paylike_refund_status_id'] = $this->request->post['payment_paylike_refund_status_id'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_refund_status_id' ) ) ) {
			$data['payment_paylike_refund_status_id'] = $this->config->get( 'payment_paylike_refund_status_id' );
		} else {
			$data['payment_paylike_refund_status_id'] = 11;
		}

		if ( isset( $this->request->post['payment_paylike_void_status_id'] ) ) {
			$data['payment_paylike_void_status_id'] = $this->request->post['payment_paylike_void_status_id'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_void_status_id' ) ) ) {
			$data['payment_paylike_void_status_id'] = $this->config->get( 'payment_paylike_void_status_id' );
		} else {
			$data['payment_paylike_void_status_id'] = 16;
		}

		if ( isset( $this->request->post['payment_paylike_status'] ) ) {
			$data['payment_paylike_status'] = $this->request->post['payment_paylike_status'];
		} else {
			$data['payment_paylike_status'] = $this->config->get( 'payment_paylike_status' );
		}

		if ( isset( $this->request->post['payment_paylike_logging'] ) ) {
			$data['payment_paylike_logging'] = $this->request->post['payment_paylike_logging'];
		} else {
			$data['payment_paylike_logging'] = $this->config->get( 'payment_paylike_logging' );
		}

		if ( isset( $this->request->post['payment_paylike_minimum_total'] ) ) {
			$data['payment_paylike_minimum_total'] = $this->request->post['payment_paylike_minimum_total'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_minimum_total' ) ) ) {
			$data['payment_paylike_minimum_total'] = $this->config->get( 'payment_paylike_minimum_total' );
		} else {
			$data['payment_paylike_minimum_total'] = 0;
		}

		if ( isset( $this->request->post['payment_paylike_geo_zone'] ) ) {
			$data['payment_paylike_geo_zone'] = $this->request->post['payment_paylike_geo_zone'];
		} else {
			$data['payment_paylike_geo_zone'] = $this->config->get( 'payment_paylike_geo_zone' );
		}

		if ( isset( $this->request->post['payment_paylike_sort_order'] ) ) {
			$data['payment_paylike_sort_order'] = $this->request->post['payment_paylike_sort_order'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_sort_order' ) ) ) {
			$data['payment_paylike_sort_order'] = $this->config->get( 'payment_paylike_sort_order' );
		} else {
			$data['payment_paylike_sort_order'] = 0;
		}

		$data['stores']   = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get( 'text_default' )
		);
		$stores           = $this->model_setting_store->getStores();
		foreach ( $stores as $store ) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if ( isset( $this->request->post['payment_paylike_store'] ) ) {
			$data['payment_paylike_store'] = $this->request->post['payment_paylike_store'];
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_store' ) ) ) {
			$data['payment_paylike_store'] = $this->config->get( 'payment_paylike_store' );
		} else {
			$data['payment_paylike_store'] = array( 0 );
		}


		$data['breadcrumbs']   = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_home' ),
			'href' => $this->url->link( 'common/dashboard', $this->oc_token . '=' . $this->session->data[ $this->oc_token ], true )
		);
		if ( version_compare( VERSION, '3.0.0.0', '>=' ) ) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get( 'text_extension' ),
				'href' => $this->url->link( 'marketplace/extension', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&type=payment', true )
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get( 'text_extension' ),
				'href' => $this->url->link( 'extension/extension', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&type=payment', true )
			);
		}
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'heading_title' ),
			'href' => $this->url->link( 'extension/payment/paylike', $this->oc_token . '=' . $this->session->data[ $this->oc_token ], true )
		);

		$data['action']           = $this->url->link( 'extension/payment/paylike', $this->oc_token . '=' . $this->session->data[ $this->oc_token ], true );
		$data['cancel']           = $this->url->link( 'marketplace/extension', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&type=payment', true );
		$data['paylike_payments'] = $this->url->link( 'extension/payment/paylike/payments', $this->oc_token . '=' . $this->session->data[ $this->oc_token ], true );


		$this->load->model( 'localisation/order_status' );
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model( 'localisation/geo_zone' );
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['header']      = $this->load->controller( 'common/header' );
		$data['column_left'] = $this->load->controller( 'common/column_left' );
		$data['footer']      = $this->load->controller( 'common/footer' );

		$this->response->setOutput( $this->load->view( 'extension/payment/paylike', $data ) );
	}

	public function install() {
		$this->load->model( 'extension/payment/paylike' );
		$this->model_extension_payment_paylike->install();
	}

	public function uninstall() {
		$this->load->model( 'extension/payment/paylike' );
		$this->model_extension_payment_paylike->uninstall();
	}

	protected function validate() {
		if ( ! $this->user->hasPermission( 'modify', 'extension/payment/paylike' ) ) {
			$this->error['warning'] = $this->language->get( 'error_permission' );
		}
		if ( $this->request->post['payment_paylike_method_title'] == '' ) {
			$this->error['error_payment_method_title'] = $this->language->get( 'error_payment_method_title' );
		}
		if ( $this->request->post['payment_paylike_api_mode'] == 'live' ) {
			if ( $this->request->post['payment_paylike_public_key_live'] == '' ) {
				$this->error['error_public_key_live'] = $this->language->get( 'error_public_key_live' );
			}
			if ( $this->request->post['payment_paylike_app_key_live'] == '' ) {
				$this->error['error_app_key_live'] = $this->language->get( 'error_app_key_live' );
			}
		} else {
			if ( $this->request->post['payment_paylike_public_key_test'] == '' ) {
				$this->error['error_public_key_test'] = $this->language->get( 'error_public_key_test' );
			}
			if ( $this->request->post['payment_paylike_app_key_test'] == '' ) {
				$this->error['error_app_key_test'] = $this->language->get( 'error_app_key_test' );
			}
		}
		if ( ! is_numeric( $this->request->post['payment_paylike_minimum_total'] ) ) {
			$this->request->post['payment_paylike_minimum_total'] = 0;
		}
		if ( ! is_numeric( $this->request->post['payment_paylike_sort_order'] ) ) {
			$this->request->post['payment_paylike_sort_order'] = 0;
		}
		if ( $this->error && ! isset( $this->error['warning'] ) ) {
			$this->error['warning'] = $this->language->get( 'error_warning' );
		}

		return ! $this->error;
	}

	public function payments() {
		$this->oc_token = version_compare( VERSION, '3.0.0.0', '>=' ) ? 'user_token' : $this->oc_token = 'token';
		$this->load->language( 'extension/payment/paylike_payments' );
		$this->load->model( 'extension/payment/paylike' );
		$this->load->model( 'setting/setting' );

		$this->model_extension_payment_paylike->install();
		$upgrade = false;
		$query   = $this->db->query( "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . "paylike_admin'" );
		if ( $query->num_rows > 0 || is_null( $this->config->get( 'payment_paylike_method_title' ) ) ) {
			$redirect_url                         = $this->url->link( 'extension/payment/paylike', $this->oc_token . '=' . $this->session->data[ $this->oc_token ], true );
			$this->session->data['error_warning'] = $this->language->get( 'text_setting_review_required' );
			$this->response->redirect( $redirect_url );
		}

		$this->document->setTitle( $this->language->get( 'heading_title' ) );
		$data['heading_title']             = $this->language->get( 'heading_title' );
		$data['column_order_id']           = $this->language->get( 'column_order_id' );
		$data['column_transaction_id']     = $this->language->get( 'column_transaction_id' );
		$data['column_transaction_type']   = $this->language->get( 'column_transaction_type' );
		$data['column_transaction_amount'] = $this->language->get( 'column_transaction_amount' );
		$data['column_order_amount']       = $this->language->get( 'column_order_amount' );
		$data['column_total_amount']       = $this->language->get( 'column_total_amount' );
		$data['column_date_added']         = $this->language->get( 'column_date_added' );
		$data['column_action']             = $this->language->get( 'column_action' );

		$data['entry_transaction_type'] = $this->language->get( 'entry_transaction_type' );
		$data['entry_date_added']       = $this->language->get( 'entry_date_added' );
		$data['button_filter']          = $this->language->get( 'button_filter' );
		$data['button_history']         = $this->language->get( 'button_history' );
		$data['button_capture']         = $this->language->get( 'button_capture' );
		$data['button_refund']          = $this->language->get( 'button_refund' );
		$data['button_void']            = $this->language->get( 'button_void' );

		$data['text_filter']     = $this->language->get( 'text_filter' );
		$data['text_list']       = $this->language->get( 'text_list' );
		$data['text_no_results'] = $this->language->get( 'text_no_results' );

		$data['popup_title_transaction'] = $this->language->get( 'popup_title_transaction' );
		$data['popup_title_history']     = $this->language->get( 'popup_title_history' );
		$data['popup_description']       = $this->language->get( 'popup_description' );

		$data['popup_transaction_id']   = $this->language->get( 'popup_transaction_id' );
		$data['popup_transaction_type'] = $this->language->get( 'popup_transaction_type' );
		$data['popup_amount']           = $this->language->get( 'popup_amount' );
		$data['popup_close']            = $this->language->get( 'popup_close' );
		$data['popup_execute']          = $this->language->get( 'popup_execute' );

		$data['error_warning'] = '';
		if ( ! is_null( $this->config->get( 'payment_paylike_api_mode' ) ) && $this->config->get( 'payment_paylike_api_mode' ) == 'live' ) {
			if ( is_null( $this->config->get( 'payment_paylike_public_key_live' ) )
			     || $this->config->get( 'payment_paylike_public_key_live' ) == ''
			     || is_null( $this->config->get( 'payment_paylike_app_key_live' ) )
			     || $this->config->get( 'payment_paylike_app_key_live' ) == '' ) {
				$data['error_warning'] = $this->language->get( 'error_live_keys' );
			} else {
				$data['public_key'] = $this->language->get( 'payment_paylike_public_key_live' );
			}
		} elseif ( ! is_null( $this->config->get( 'payment_paylike_api_mode' ) ) && $this->config->get( 'payment_paylike_api_mode' ) == 'test' ) {
			if ( is_null( $this->config->get( 'payment_paylike_public_key_test' ) )
			     || $this->config->get( 'payment_paylike_public_key_test' ) == ''
			     || is_null( $this->config->get( 'payment_paylike_app_key_test' ) )
			     || $this->config->get( 'payment_paylike_app_key_test' ) == '' ) {
				$data['error_warning'] = $this->language->get( 'error_test_keys' );
			} else {
				$data['public_key'] = $this->language->get( 'payment_paylike_public_key_test' );
			}
		} else {
			$data['error_warning'] = $this->language->get( 'error_setup' );
		}

		if ( isset( $this->request->get['filter_order_id'] ) ) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if ( isset( $this->request->get['filter_transaction_id'] ) ) {
			$filter_transaction_id = $this->request->get['filter_transaction_id'];
		} else {
			$filter_transaction_id = '';
		}

		if ( isset( $this->request->get['filter_transaction_type'] ) ) {
			$filter_transaction_type = $this->request->get['filter_transaction_type'];
		} else {
			$filter_transaction_type = '';
		}

		if ( isset( $this->request->get['filter_date_added'] ) ) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if ( isset( $this->request->get['sort'] ) ) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'order_id';
		}

		if ( isset( $this->request->get['order'] ) ) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if ( isset( $this->request->get['page'] ) ) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if ( isset( $this->request->get['filter_order_id'] ) ) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if ( isset( $this->request->get['filter_transaction_id'] ) ) {
			$url .= '&filter_transaction_id=' . urlencode( html_entity_decode( $this->request->get['filter_transaction_id'], ENT_QUOTES, 'UTF-8' ) );
		}

		if ( isset( $this->request->get['filter_transaction_type'] ) ) {
			$url .= '&filter_transaction_type=' . $this->request->get['filter_transaction_type'];
		}

		if ( isset( $this->request->get['filter_date_added'] ) ) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ( isset( $this->request->get['sort'] ) ) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if ( isset( $this->request->get['order'] ) ) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if ( isset( $this->request->get['page'] ) ) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_home' ),
			'href' => $this->url->link( 'common/dashboard', $this->oc_token . '=' . $this->session->data[ $this->oc_token ], true )
		);

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'         => $filter_order_id,
			'filter_transaction_id'   => $filter_transaction_id,
			'filter_transaction_type' => $filter_transaction_type,
			'filter_date_added'       => $filter_date_added,
			'sort'                    => $sort,
			'order'                   => $order,
			'start'                   => ( $page - 1 ) * $this->config->get( 'config_limit_admin' ),
			'limit'                   => $this->config->get( 'config_limit_admin' )
		);

		$data['transactions'] = array();
		$transactions_total   = $this->model_extension_payment_paylike->getTotalTransactions( $filter_data );
		$results              = $this->model_extension_payment_paylike->getTransactions( $filter_data );

		foreach ( $results as $result ) {
			$order_amount       = $this->getAmountsFromPaylikeAmount( $result['order_amount'], $result['transaction_currency'] );
			$transaction_amount = $this->getAmountsFromPaylikeAmount( $result['transaction_amount'], $result['transaction_currency'] );
			$total_amount       = $this->getAmountsFromPaylikeAmount( $result['total_amount'], $result['transaction_currency'] );

			$data['transactions'][] = array(
				'paylike_transaction_id' => $result['paylike_transaction_id'],
				'order_id'               => $result['order_id'],
				'transaction_id'         => $result['transaction_id'],
				'transaction_type'       => $result['transaction_type'],
				'order_amount'           => $order_amount['paylike_formatted'],
				'transaction_amount'     => $transaction_amount['paylike_formatted'],
				'total_amount'           => $total_amount['paylike_formatted'],
				'date_added'             => date( $this->language->get( 'datetime_format' ), strtotime( $result['date_added'] ) ),
				'order_link'             => $this->url->link( 'sale/order/info', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&order_id=' . $result['order_id'], true ),
				'action_history'         => $this->url->link( 'sale/order/history', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&order_id=' . $result['order_id'], true ),
				'allowed_capture'        => $order_amount['paylike_converted'] - $total_amount['paylike_converted'],
				'allowed_refund'         => $total_amount['paylike_converted'],
				'currency'               => $result['transaction_currency']
			);
		}

		$data['url_token_param'] = $this->oc_token . '=' . $this->session->data[ $this->oc_token ];

		$url = '';

		if ( isset( $this->request->get['filter_order_id'] ) ) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if ( isset( $this->request->get['filter_transaction_id'] ) ) {
			$url .= '&filter_transaction_id=' . urlencode( html_entity_decode( $this->request->get['filter_transaction_id'], ENT_QUOTES, 'UTF-8' ) );
		}

		if ( isset( $this->request->get['filter_transaction_type'] ) ) {
			$url .= '&filter_transaction_type=' . $this->request->get['filter_transaction_type'];
		}

		if ( isset( $this->request->get['filter_date_added'] ) ) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ( $order == 'ASC' ) {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if ( isset( $this->request->get['page'] ) ) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_id']         = $this->url->link( 'extension/payment/paylike/payments', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&sort=order_id' . $url, true );
		$data['sort_transaction_id']   = $this->url->link( 'extension/payment/paylike/payments', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&sort=transaction_id' . $url, true );
		$data['sort_transaction_type'] = $this->url->link( 'extension/payment/paylike/payments', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&sort=transaction_type' . $url, true );
		$data['sort_date_added']       = $this->url->link( 'extension/payment/paylike/payments', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . '&sort=date_added' . $url, true );

		$url = '';

		if ( isset( $this->request->get['filter_order_id'] ) ) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if ( isset( $this->request->get['filter_transaction_id'] ) ) {
			$url .= '&filter_transaction_id=' . urlencode( html_entity_decode( $this->request->get['filter_transaction_id'], ENT_QUOTES, 'UTF-8' ) );
		}

		if ( isset( $this->request->get['filter_transaction_type'] ) ) {
			$url .= '&filter_transaction_type=' . $this->request->get['filter_transaction_type'];
		}

		if ( isset( $this->request->get['filter_date_added'] ) ) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ( isset( $this->request->get['sort'] ) ) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if ( isset( $this->request->get['order'] ) ) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination        = new Pagination();
		$pagination->total = $transactions_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get( 'config_limit_admin' );
		$pagination->url   = $this->url->link( 'extension/payment/paylike/payments', $this->oc_token . '=' . $this->session->data[ $this->oc_token ] . $url . '&page={page}', true );

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf( $this->language->get( 'text_pagination' ), ( $transactions_total ) ? ( ( $page - 1 ) * $this->config->get( 'config_limit_admin' ) ) + 1 : 0, ( ( ( $page - 1 ) * $this->config->get( 'config_limit_admin' ) ) > ( $transactions_total - $this->config->get( 'config_limit_admin' ) ) ) ? $transactions_total : ( ( ( $page - 1 ) * $this->config->get( 'config_limit_admin' ) ) + $this->config->get( 'config_limit_admin' ) ), $transactions_total, ceil( $transactions_total / $this->config->get( 'config_limit_admin' ) ) );

		$data['filter_order_id']         = $filter_order_id;
		$data['filter_transaction_id']   = $filter_transaction_id;
		$data['filter_transaction_type'] = $filter_transaction_type;
		$data['filter_date_added']       = $filter_date_added;

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['transaction_types'] = $this->model_extension_payment_paylike->getTransactionTypes();

		$data['header']      = $this->load->controller( 'common/header' );
		$data['column_left'] = $this->load->controller( 'common/column_left' );
		$data['footer']      = $this->load->controller( 'common/footer' );

		$this->response->setOutput( $this->load->view( 'extension/payment/paylike_payments', $data ) );
	}

	public function transaction() {
		$json = $this->transaction_validate();
		if ( ! $json ) {
			$json = $this->execute();
		}
		$this->response->addHeader( 'Content-Type: application/json' );
		$this->response->setOutput( json_encode( $json ) );
	}

	public function execute() {

		$this->load->language( 'extension/payment/paylike_payments' );
		$this->load->model( 'extension/payment/paylike' );
		$this->logger = new Log( 'paylike.log' );
		$log          = $this->config->get( 'payment_paylike_logging' ) ? true : false;
		require_once( DIR_SYSTEM . 'library/Paylike/Client.php' );

		$ref          = $this->request->post['ref'];
		$type         = $this->request->post['type'];
		$input_amount = $this->request->post['amount'];

		$history = $this->model_extension_payment_paylike->getLastTransaction( $ref );
		if ( is_null( $history ) ) {
			$json['error'] = $this->language->get( 'error_message' );

			return $json;
		}

		$amount = $this->getAmountsFromPaylikeAmount( $input_amount, $history['transaction_currency'] );

		if ( $log ) {
			$this->logger->write( '************' );
			$this->logger->write( 'Admin transaction ' . $type . ' for order: ' . $history['order_id'] . ' (' . $amount['paylike_formatted'] . ' ' . $history['transaction_currency'] . ')' );
			$this->logger->write( 'Transaction Refference: ' . $ref );
		}

		$app_key = $this->config->get( 'payment_paylike_api_mode' ) == 'live' ? $this->config->get( 'payment_paylike_app_key_live' ) : $this->config->get( 'payment_paylike_app_key_test' );

		Paylike\Client::setKey( $app_key );
		$trans_data = Paylike\Transaction::fetch( $ref );
		if ( is_null( $trans_data ) ) {
			$this->logger->write( 'Invalid transaction data. Unable to Fetch transaction.' );
			$json['error'] = $this->language->get( 'error_message' );

			return $json;
		}

		if ( $trans_data['transaction']['currency'] != $history['transaction_currency'] ) {
			if ( $log ) {
				$this->logger->write( 'Error: Capture currency (' . $history['transaction_currency'] . ') not equal to Transaction currency (' . $trans_data['transaction']['currency'] . '). Transactiion aborted!' );
			}
			$json['error'] = $this->language->get( 'error_transaction_currency' );;

			return $json;
		}

		$response = array();

		switch ( $type ) {
			case "Capture":
				if ( $trans_data['transaction']['pendingAmount'] == 0 ) {
					if ( $log ) {
						$this->logger->write( 'Error: There is no Pending amount. Order is already captured. Transaction aborted.' );
					}
					$json['error'] = $this->language->get( 'error_order_captured' );;

					return $json;
				}
				if ( $trans_data['transaction']['pendingAmount'] < $amount['paylike'] ) {
					if ( $log ) {
						$this->logger->write( 'Warning: Capture amount is large than Transaction pending amount. Pending amount will be captured.' );
					}
					$amount = $this->getAmountsFromPaylikeAmount( $trans_data['transaction']['pendingAmount'], $history['transaction_currency'], true );
				}
				$data     = array(
					'amount'     => $amount['paylike'],
					'descriptor' => '',
					'currency'   => $history['transaction_currency']
				);
				$response = Paylike\Transaction::capture( $ref, $data );

				break;
			case "Refund":
				if ( $trans_data['transaction']['capturedAmount'] == 0 ) {
					if ( $log ) {
						$this->logger->write( 'Error: There is no Captured amount. Order is not captured and cannot be refunded. Transaction aborted.' );
					}
					$json['error'] = $this->language->get( 'error_refund_before_capture' );;

					return $json;
				}
				if ( $trans_data['transaction']['capturedAmount'] < $amount['paylike'] ) {
					if ( $log ) {
						$this->logger->write( 'Warning: Refund amount is larger than Transaction captured amount. Captured amount will be refunded.' );
					}
					$amount = $this->getAmountsFromPaylikeAmount( $trans_data['transaction']['capturedAmount'], $history['transaction_currency'], true );
				}
				$data     = array(
					'amount'     => $amount['paylike'],
					'descriptor' => ''
				);
				$response = Paylike\Transaction::refund( $ref, $data );

				break;
			case "Void":
				if ( $trans_data['transaction']['capturedAmount'] > 0 ) {
					if ( $log ) {
						$this->logger->write( 'Error: Order already Captured and cannot be Void. Transaction aborted.' );
					}
					$json['error'] = $this->language->get( 'error_void_after_capture' );;

					return $json;
				}
				if ( $trans_data['transaction']['pendingAmount'] < $amount['paylike'] ) {
					if ( $log ) {
						$this->logger->write( 'Warning: Void amount is larger than Transaction captured amount. Captured amount will be voided.' );
					}
					$amount = $this->getAmountsFromPaylikeAmount( $trans_data['transaction']['pendingAmount'], $history['transaction_currency'], true );
				}
				$data     = array(
					'amount' => $amount['paylike'],
				);
				$response = Paylike\Transaction::void( $ref, $data );
				break;
		}

		if ( isset( $response['transaction'] ) ) {
			$new_total_amount = $this->getAmountsFromPaylikeAmount( $response['transaction']['capturedAmount'] - $response['transaction']['refundedAmount'] - $response['transaction']['voidedAmount'], $history['transaction_currency'], true );
			$data             = array(
				'order_id'             => $history['order_id'],
				'transaction_id'       => $ref,
				'transaction_type'     => $type,
				'transaction_currency' => $history['transaction_currency'],
				'order_amount'         => $history['order_amount'],
				'transaction_amount'   => $amount['paylike_converted'],
				'total_amount'         => $new_total_amount['paylike_converted'],
				'history'              => '0',
				'date_added'           => 'NOW()'
			);
			$this->model_extension_payment_paylike->addTransaction( $data );

			$new_order_status_id = $this->config->get( 'payment_paylike_' . strtolower( $type ) . '_status_id' );
			$this->model_extension_payment_paylike->updateOrder( $data, $new_order_status_id );

			$json['success'] = sprintf( $this->language->get( 'success_transaction_' . strtolower( $type ) ), $amount['paylike_formatted'] ) . ' ' . $history['transaction_currency'];

			return $json;
		} else {
			$error = array();
			foreach ( $response as $field_error ) {
				$error[] = ucwords( $field_error['field'] ) . ': ' . $field_error['message'];
			}
			$error_message = implode( " ", $error );
			$json['error'] = $this->language->get( 'error_message' ) . ' ' . $error_message;

			return $json;
		}


	}

	protected function transaction_validate() {
		$json = array();
		$this->load->language( 'extension/payment/paylike_payments' );

		if ( ! $this->user->hasPermission( 'modify', 'extension/payment/paylike' ) ) {
			$json['error'] = $this->language->get( 'error_permission' );

			return $json;
		}

		if ( is_null( $this->request->post['ref'] ) || is_null( $this->request->post['type'] ) || is_null( $this->request->post['amount'] ) ) {
			$json['error'] = $this->language->get( 'error_transaction' );

			return $json;
		}

		if ( ! is_numeric( $this->request->post['amount'] ) ) {
			$json['error'] = $this->language->get( 'error_amount_format' );

			return $json;
		}

	}

	private function getAmountsFromPaylikeAmount( $paylike_amount, $currency_code, $isMinor = false ) {
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
		if ( in_array( $currency_code, $exponent_zero ) ) {
			$exponent = 0;
		} elseif ( in_array( $currency_code, $exponent_three ) ) {
			$exponent = 3;
		}

		$multiplier = pow( 10, $exponent );
		$amount     = array();

		$symbol_left  = $this->currency->getSymbolLeft( $currency_code );
		$symbol_right = $this->currency->getSymbolRight( $currency_code );

		if ( $isMinor ) {
			$amount['paylike'] = (string) ( $paylike_amount );
		} else {
			$amount['paylike'] = (string) ( $paylike_amount * $multiplier );
		}
		$amount['paylike']           = (int) $amount['paylike'];
		$amount['paylike_converted'] = $amount['paylike'] / $multiplier;
		$amount['paylike_formatted'] = $symbol_left . number_format( $amount['paylike_converted'], $exponent, $this->language->get( 'decimal_point' ), $this->language->get( 'thousand_point' ) ) . $symbol_right;

		return $amount;
	}

}