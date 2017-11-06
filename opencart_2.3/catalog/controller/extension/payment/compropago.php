<?php

require_once __DIR__."/../../../../vendor/autoload.php";

use CompropagoSdk\Client;
use CompropagoSdk\Service;
use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Tools\Validations;

class ControllerExtensionPaymentCompropago extends Controller 
{    
    public $client;
    public $modActive;
    public $publicKey;
    public $privateKey;
    public $execMode;
    public $execLocation;

    public function index() 
    {
        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['text_loading'] = $this->language->get('text_loading');

        $data['continue'] = $this->url->link('extension/payment/compropago/success');
               
        $this->load->model('checkout/order');
        $this->load->model('setting/setting');
        $this->load->model('localisation/currency');

        $orderInfo   = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $limit       = $orderInfo['total'];
        $defCurrency = $this->config->get('config_currency');
        
        $this->initService();

        $this->getProviders($limit, $defCurrency);

        $data['providers']  = $this->getProviders($limit, $defCurrency);
        $data['showLogo']   = $this->config->get('compropago_showlogo');
        
        return $this->load->view('extension/payment/compropago', $data);
    }

    private function initService()
    {
        $this->publicKey    = $this->config->get('compropago_public_key');
        $this->privateKey   = $this->config->get('compropago_private_key');
        $this->execMode     = $this->config->get('compropago_mode') == 'no' || $this->config->get('compropago_mode') == 'NO' ? false : true;
        $this->client       = new Client($this->publicKey, $this->privateKey, false);
    }

    public function getProviders($limit=0.0, $currency="MXN")
    {
        
        $isLimit = intval($limit);
        
        $providers  = $this->client->api->listProviders($isLimit, $currency);
        return $providers;

    }

    public function saveOrder()
    {
        if($this->session->data['payment_method']['code'] == 'compropago'){
            $this->load->language('extension/payment/compropago');
            $this->load->model('checkout/order');

            $products   = $this->cart->getProducts();
            $orderName = '';

            foreach ($products as $product) {
                $orderName .= $product['name'];
            }
            $this->initService();
            
            $orderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $defCurrency = $this->config->get('config_currency');
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1, "", true);
            $getProvider = (isset($this->request->post['compropagoProvider']) && !empty($this->request->post['compropagoProvider'])) ? $this->request->post['compropagoProvider'] : "SEVEN_ELEVEN";
            
            $params = [
                'order_id'           => $orderInfo['order_id'],
                'order_name'         => $orderName,
                'order_price'        => $orderInfo['total'] * $orderInfo['currency_value'],
                'customer_name'      => $orderInfo['payment_firstname'] . " " . $orderInfo['payment_lastname'],
                'customer_email'     => $orderInfo['email'],
                'payment_type'       => $getProvider,
                'currency'           => $orderInfo['currency_code'],
                'app_client_name'    => 'OpenCart',
                'app_client_version' => VERSION,
                'cp'                 => $orderInfo['payment_postcode']
            ];
            
            $order = Factory::getInstanceOf('PlaceOrderInfo', $params);
            try {
                $response = $this->client->api->placeOrder($order);
            } catch (Exception $e) {
                die('This payment method is not available.') . '<br>' . $e->getMessage();
            }

            $recordTime = time();
            $order_id = $orderInfo['order_id'];
            $ioIn = base64_encode(json_encode($response));
            $ioOut = base64_encode(json_encode($order));    

            // Creacion del query para compropago_orders
            $query = "INSERT INTO " . DB_PREFIX . "compropago_orders (`date`,`modified`,`compropagoId`,`compropagoStatus`,`storeCartId`,`storeOrderId`,`storeExtra`,`ioIn`,`ioOut`)".
                " values (:fecha:,:modified:,':cpid:',':cpstat:',':stcid:',':stoid:',':ste:',':ioin:',':ioout:')";

            $query = str_replace(":fecha:",$recordTime,$query);
            $query = str_replace(":modified:",$recordTime,$query);
            $query = str_replace(":cpid:",$response->id,$query);
            $query = str_replace(":cpstat:",$response->type,$query);
            $query = str_replace(":stcid:",$order_id,$query);
            $query = str_replace(":stoid:",$order_id,$query);
            $query = str_replace(":ste:",'COMPROPAGO_PENDING',$query);
            $query = str_replace(":ioin:",$ioIn,$query);
            $query = str_replace(":ioout:",$ioOut,$query);

            $this->db->query($query);

            $compropagoOrderId = $this->db->getLastId();

            $query2 = "INSERT INTO ".DB_PREFIX."compropago_transactions
            (orderId,date,compropagoId,compropagoStatus,compropagoStatusLast,ioIn,ioOut)
            values (:orderid:,:fecha:,':cpid:',':cpstat:',':cpstatl:',':ioin:',':ioout:')";

            $query2 = str_replace(":orderid:",$compropagoOrderId,$query2);
            $query2 = str_replace(":fecha:",$recordTime,$query2);
            $query2 = str_replace(":cpid:",$response->id,$query2);
            $query2 = str_replace(":cpstat:",$response->type,$query2);
            $query2 = str_replace(":cpstatl:",$response->type,$query2);
            $query2 = str_replace(":ioin:",$ioIn,$query2);
            $query2 = str_replace(":ioout:",$ioOut,$query2);

            $this->db->query($query2);

            /**
             * Update correct status in orders
             */

            $status_update = 1;
            $query_update = "UPDATE `" . DB_PREFIX . "order` SET order_status_id = $status_update WHERE order_id = $order_id";
            $this->db->query($query_update);

            $json['success'] = htmlspecialchars_decode($this->url->link('extension/payment/compropago/success', 'info_order='.base64_encode(json_encode($response)) , 'SSL'));
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));

        }
    }

    public function success()
    {
        $this->load->model('checkout/order');   
        $orderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->language->load('extension/payment/compropago');
        $this->cart->clear();
        
        $decript =json_decode(base64_decode($this->request->get['info_order']));
        $data['orderId'] = $decript->id;
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_basket'),
            'href' => $this->url->link('checkout/cart')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('checkout/checkout', '', 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_success'),
            'href' => $this->url->link('checkout/success')
        );
        $data['language'] = $this->language->get('code');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue'] = $this->url->link('common/home');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['email'] = $orderInfo['email'];
        $this->response->setOutput($this->load->view('extension/payment/compropago_success', $data));
    }

    public function webhook()
    {
        $this->load->model('setting/setting');
        $this->load->model('checkout/order');

        $request = @file_get_contents('php://input');
        header('Content-Type: application/json');        
        
        if(!$resp_webhook = Factory::getInstanceOf('CpOrderInfo', $request)){
            echo json_encode([
              "status" => "error",
              "message" => "invalid request",
              "short_id" => null,
              "reference" => null
            ]);
        }

        try
        {
            try
            {
                $publicKey    = $this->config->get('compropago_public_key');
                $privateKey   = $this->config->get('compropago_private_key');
                $execMode     = $this->config->get('compropago_mode') == "YES";
                $client       = new Client($publicKey, $privateKey, $execMode);  
                Validations::validateGateway($client);

            } catch (Exception $e){
                die($e->getMessage());
            }
            
            if($resp_webhook->short_id == "000000"){
                die(json_encode([
                    "status" => "success",
                    "message" => "test success",
                    "short_id" => $resp_webhook->short_id,
                    "reference" => null
                ]));
            }
            
            try
            {
                $response = $client->api->verifyOrder($resp_webhook->id);
                if($response->type == 'error'){
                    die('Error procesando el numero de orden');
                }
            } catch (Exception $e){
                die($e->getMessage());
            }
            
            $newOrder = $this->db->query("SELECT * FROM ". DB_PREFIX ."compropago_orders WHERE compropagoId = '".$response->id."'");

            if($newOrder->num_rows == 0){
                die('El número de orden no se encontro en la tienda');
            }

            $id = intval($newOrder->row['storeOrderId']);

            switch ($response->type){
                case 'charge.success':
                    $nameStatus = "COMPROPAGO_SUCCESS";
                    $idStoreStatus = 2;
                    echo "Éxito: " . $response->id . " : " . $nameStatus;
                    break;
                case 'charge.pending':
                    $nameStatus = "COMPROPAGO_PENDING";
                    $idStoreStatus = 1;
                    echo "Éxito: " . $response->id . " :  " . $nameStatus;
                    break;
                case 'charge.expired':
                    $nameStatus = "COMPROPAGO_EXPIRED";
                    $idStoreStatus = 14;
                    echo "Éxito: " . $response->id . " : " . $nameStatus;
                    break;
                default:
                    die( 'Invalid Response type');
            }

            $this->db->query("UPDATE `". DB_PREFIX . "order` SET order_status_id = " . $idStoreStatus . " WHERE order_id = " . $id);
        } catch ( Exception $e) {
            die($e->getMessage());
        }
    }
}
