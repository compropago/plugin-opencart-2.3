<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/../../../../vendor/autoload.php";

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Tools\Validations;

class ControllerExtensionPaymentCompropago extends Controller {

	private $error = array();
	private $config_view = 'extension/payment/compropago.tpl';
	private $private_key;
	private $public_key;
    private $mode;
    
	public function index() {
		$this->load->language('extension/payment/compropago');
		$this->load->model('setting/setting');
		$data = array();
		$this->document->setTitle('ComproPago');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->model_setting_setting->editSetting('compropago', $this->request->post);
			$this->public_key   = $this->request->post['compropago_public_key'];
			$this->private_key  = $this->request->post['compropago_private_key'];
			$this->mode = $this->request->post['compropago_mode'] == '1' ? true : false;
            $retro = $this->hookRetro($this->public_key, $this->private_key, $this->mode);
            
			try {
				$uri = explode("admin/index.php",$_SERVER["REQUEST_URI"]);
				$uri = $uri[0];
				$webhook_url = $_SERVER['SERVER_NAME'] . $uri . "index.php?route=extension/payment/compropago/webhook";
				$client = new Client($this->public_key, $this->private_key, $this->mode);
				$client->api->createWebhook($webhook_url);
			} catch(Exception $e) {
				if ($e->getMessage() != 'Error: conflict.urls.create') {
					$retro[1] = $retro[1] == '' ? $e->getMessage() : ' - ' . $e->getMessage();
				} 
			}
			$message = $this->language->get('text_success') . ($retro[0] ? ' - ' . $retro[1] : '');
			$this->session->data['success'] = $message;
			$this->response->redirect($this->url->link('extension/payment/compropago', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$this->addData($data);
		$this->addWarnings($data);
		$this->addBreadcrumbs($data);
		$this->addSections($data);
		$this->response->setOutput($this->load->view($this->config_view, $data));
    }
    
	/**
	 * Add sections to render in view
	 * 
	 * @param $data array
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com> 
	 */
	private function addSections(&$data) {
		$this->load->language('extension/payment/compropago');
		$this->load->model('setting/setting');
		$data['action']      = $this->url->link('extension/payment/compropago', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel']      = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
    }
    
	/**
	 * Set data paramters
	 * 
	 * @param $data array
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
	 */
	private function addData(&$data) {
		$data['heading_title']  = $this->language->get('heading_title');
		$data['text_edit']      = $this->language->get('text_edit');
		$data['public_key']     = $this->config->get('compropago_public_key');
		$data['private_key']    = $this->config->get('compropago_private_key');
		$data['mode']           = $this->config->get('compropago_mode');
		$data['show_logos']     = $this->config->get('compropago_show_logos');
		$data['status']         = $this->config->get('compropago_status');
		$data['sort_order']     = empty($this->config->get('compropago_sort_order')) ? 1 : $this->config->get('compropago_sort_order');
		
		$providers = $this->config->get('compropago_providers');
		$client = new Client('', '', false);
		if (empty($providers)) {
			$active_providers = [];
			$deactive_providers = $client->api->listDefaultProviders();
		} else {
			$default_providers = $client->api->listDefaultProviders();
			$aux = explode(',', $providers);
			$deactive_providers = [];
			$active_providers = [];
			foreach ($default_providers as $provider) {
				if (!in_array($provider->internal_name, $aux)) {
					$deactive_providers[] = $provider;
				} else {
					$active_providers[] = $provider;
				}
			}
		}
		$data['active_providers'] = $active_providers;
		$data['deactive_providers'] = $deactive_providers;
    }
    
	/**
	 * Add warning labels
	 * 
	 * @param $dara array
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>  
	 */
	private function addWarnings(&$data) {
        $data['error_warning'] = '';
		$retro = $this->hookRetro(
			$this->config->get('compropago_public_key'),
			$this->config->get('compropago_private_key'),
			$this->config->get('compropago_mode') == '1' ? true : false
		);
		if ($retro[0]) {
			$this->error['warning'][] = $retro[1];
		}
		if (isset($this->error['warning'])) {
			$data['error_warning'] = '';
			foreach ($this->error['warning'] as $value) {
				if ($data['error_warning'] == '') {
					$data['error_warning'] = $value;
				} else {
					$data['error_warning'] .= ' - ' . $value;
				}
			}
		}
	}
	/**
	 * Add Breadcrumbs section
	 * 
	 * @param $data
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
	 */
	private function addBreadcrumbs(&$data) {
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/cod', 'token=' . $this->session->data['token'], 'SSL')
		);
    }
    
	/**
	 * Compropago query tables
	 * 
	 * @param string $prefix
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
	 */
	private function sqlCreateTables($prefix=null) {
		return array(
			'CREATE TABLE `' . $prefix . 'compropago_orders` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`date` int(11) NOT NULL,
			`modified` int(11) NOT NULL,
			`compropagoId` varchar(50) NOT NULL,
			`compropagoStatus`varchar(50) NOT NULL,
			`storeCartId` varchar(255) NOT NULL,
			`storeOrderId` varchar(255) NOT NULL,
			`storeExtra` varchar(255) NOT NULL,
			`ioIn` mediumtext,
			`ioOut` mediumtext,
			PRIMARY KEY (`id`), UNIQUE KEY (`compropagoId`)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;',
			'CREATE TABLE `' . $prefix . 'compropago_transactions` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`orderId` int(11) NOT NULL,
			`date` int(11) NOT NULL,
			`compropagoId` varchar(50) NOT NULL,
			`compropagoStatus` varchar(50) NOT NULL,
			`compropagoStatusLast` varchar(50) NOT NULL,
			`ioIn` mediumtext,
			`ioOut` mediumtext,
			PRIMARY KEY (`id`)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;',
			'CREATE TABLE `' . $prefix . 'compropago_webhook_transactions` (
			`id` integer not null auto_increment,
			`webhookId` varchar(50) not null,
			`webhookUrl` varchar(300) not null,
			`updated` integer not null,
			`status` varchar(50) not null,
			primary key(id)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;'
		);
	}
	/**
	 * Compropago drop tables
	 * 
	 * @param string $prefix
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
	 */
	private function sqlDropTables($prefix=null) {
		return array(
			'DROP TABLE IF EXISTS `' . $prefix . 'compropago_orders`;',
			'DROP TABLE IF EXISTS `' . $prefix . 'compropago_transactions`;',
			'DROP TABLE IF EXISTS `' . $prefix . 'compropago_webhook_transactions`'
		);
    }
    
	/**
	 * Verify some configuration errors
	 * 
	 * @param string $public_key
	 * @param string $private_key
	 * @param bool $mode
	 * @return array
	 * 
	 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
	 */
	private function hookRetro($public_key, $private_key, $live) {
		$error = array(
			false,
			'',
			'yes'
		);
		if (!empty($public_key) && !empty($private_key)) {
			try {
				$client = new Client($public_key, $private_key, $live);
				$cp_response = Validations::evalAuth($client);
								
				if (!Validations::validateGateway($client)) {
					$error[1] = 'Invalid Keys, The Public Key and Private Key must be valid before using this module.';
					$error[0] = true;
				} else if ($cp_response->mode_key != $cp_response->livemode) {
					$error[1] = 'Your Keys and Your ComproPago account are set to different Modes.';
					$error[0] = true;
				} else if ($live != $cp_response->livemode) {
					$error[1] = 'Your Store and Your ComproPago account are set to different Modes.';
					$error[0] = true;
				} else if ($live != $cp_response->mode_key) {
					$error[1] = 'ComproPago ALERT:Your Keys are for a different Mode.';
					$error[0] = true;
				} else if (!$cp_response->mode_key && !$cp_response->livemode) {
					$error[1] = 'WARNING: ComproPago account is Running in TEST Mode, NO REAL OPERATIONS';
					$error[0] = true;
				}
			} catch (Exception $e) {
				$error[2] = 'no';
				$error[1] = $e->getMessage();
				$error[0] = true;
			}
		} else {
			$error[1] = 'The Public Key and Private Key must be set before using ComproPago';
			$error[2] = 'no';
			$error[0] = true;
		}
		return $error;
    }

    /**
     * Obtener querys con el prfijo de la aplicacion e insertar tablas de compropago
     */
    public function install()
    {   
        $this->load->model('extension/payment/compropago');
        $this->model_extension_payment_compropago->install();
    }

    /**
     * Obtener querys con prefijo y eleminar tablas de compropago
     */
    public function uninstall()
    {
        $this->load->model('extension/payment/compropago');
        $this->model_extension_payment_compropago->uninstall();

    }
}
    
