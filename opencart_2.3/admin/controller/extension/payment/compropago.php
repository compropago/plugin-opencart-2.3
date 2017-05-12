<?php

require_once __DIR__."/../../../../vendor/autoload.php";

use CompropagoSdk\Client;
use CompropagoSdk\Service;
use CompropagoSdk\Tools\Validations;

class ControllerExtensionPaymentCompropago extends Controller
{   
    public $privateKey;
    public $publicKey;
    public $execMode; # Verifica si las llaves estan en modo vivo o modo de pruebas
    public $client;
    public $createWebhook;
    public $execLocation;
    public $modActive; # Verifica si el módulo completo esta activo
    /**
     * @var array
     * Errores generales del controlador (Obligatoria su declaracion)
     */
    private $error = array();

    /**
     * Carga de la vista principal de configuracion
     */
    public function index()
    { 
        # Carga de el arreglo de lenguaje en admin/lenguage/payment/compropago.php
        $this->language->load('extension/payment/compropago');
        
        $this->document->setTitle('Compropago Payment Method Configuration');
        
        $this->load->model('setting/setting');

        $this->load->model('extension/payment/compropago');

        # Validacion de envio de informacion de configuracion por metodo POST - existencia de llaves publica y privada
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if ($this->request->post['compropago_mode'] == "NO") {
                $mode = false;
            }else{
                $mode = true;
            }
            $this->client = new Client($this->request->post['compropago_public_key'], $this->request->post['compropago_private_key'], false);
            $this->client->api->createWebhook($this->request->post['compropago_webhook']);
            $this->model_setting_setting->editSetting('compropago', $this->request->post);
            $this->session->data['success'] = "<b>".$this->language->get('text_success'). "</b>";
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
        }

        /**
        * Create an array with all my data and save into the database
        *   @return array;
        */
        
        $data['compropago_private_key']  = isset($this->request->data['compropago_private_key']) ? $this->request->data['compropago_private_key'] : $this->config->get('compropago_private_key');
        $data['compropago_public_key']   = isset($this->request->data['compropago_public_key']) ? $this->request->data['compropago_public_key'] : $this->config->get('compropago_public_key');
        $data['compropago_mode']         = isset($this->request->data['compropago_mode']) ? $this->request->data['compropago_mode'] : $this->config->get('compropago_mode');
        $data['compropago_status']       = isset($this->request->data['compropago_status']) ? $this->request->data['compropago_status'] : $this->config->get('compropago_status'); 
        $data['compropago_showlogo']     = isset($this->request->data['compropago_showlogo']) ? $this->request->data['compropago_showlogo'] : $this->config->get('compropago_showlogo');
        $data['compropago_location']     = isset($this->request->data['compropago_location']) ? $this->request->data['compropago_location'] : $this->config->get('compropago_location');
        $data['compropago_webhook']      = isset($this->request->data['compropago_webhook']) ? $this->request->data['compropago_webhook'] : $this->config->get('compropago_webhook');
        var_dump($data['compropago_mode']);
        $data['compropago_order_status_new_id']     = isset($this->request->data['compropago_order_status_new_id']) ? $this->request->data['compropago_order_status_new_id'] : $this->config->get('compropago_order_status_new_id');
        $data['compropago_order_status_approve_id'] = isset($this->request->data['compropago_order_status_approve_id']) ? $this->request->data['compropago_order_status_approve_id'] : $this->config->get('compropago_order_status_approve_id');
        $data['compropago_sort_order']              = isset($this->request->data['compropago_sort_order']) ? $this->request->data['compropago_sort_order'] : $this->config->get('compropago_sort_order');
        

        # Inclucion de las variables de lenguaje cargadas con $this->lenguage->load('payment/compropago') dentro del arreglo $data
        # $data sera procesado para el render de compropago.tpl

        $data['heading_title']              = $this->language->get('heading_title');
        $data['text_edit']                  = $this->language->get('text_edit');
        $data['text_enabled']               = $this->language->get('text_enabled');
        $data['text_disabled']              = $this->language->get('text_disabled');
        $data['text_yes']                   = $this->language->get('text_yes');
        $data['text_no']                    = $this->language->get('text_no');
        $data['entry_private_key']          = $this->language->get('entry_private_key');
        $data['entry_public_key']           = $this->language->get('entry_public_key');
        $data['entry_mode']                 = $this->language->get('entry_mode');
        $data['entry_select_mode_true']     = $this->language->get('entry_select_mode_true');
        $data['entry_select_mode_false']    = $this->language->get('entry_select_mode_false');
        $data['entry_location']             = $this->language->get('entry_location');
        $data['entry_select_location_true'] = $this->language->get('entry_select_location_true');
        $data['entry_select_location_false']= $this->language->get('entry_select_location_false');
        $data['entry_order_status_new']     = $this->language->get('entry_order_status_new');
        $data['entry_order_status_approve'] = $this->language->get('entry_order_status_approve');
        $data['entry_status']               = $this->language->get('entry_status');
        $data['entry_sort_order']           = $this->language->get('entry_sort_order');
        $data['entry_db_prefix']            = $this->language->get('entry_db_prefix');
        $data['entry_showlogo']             = $this->language->get('entry_showlogo');
        $data['entry_description']          = $this->language->get('entry_description');
        $data['entry_instrucciones']        = $this->language->get('entry_instrucciones');
        $data['help_secret_key']            = $this->language->get('help_secret_key');
        $data['help_public_key']            = $this->language->get('help_public_key');
        $data['help_mode']                  = $this->language->get('help_mode');
        $data['help_location']              = $this->language->get('help_location');
        $data['button_save']                = $this->language->get('text_button_save');
        $data['button_cancel']              = $this->language->get('text_button_cancel');
        $data['tab_plugin_configurations']  = $this->language->get('tab_plugin_configurations');
        $data['tab_display_configurations'] = $this->language->get('tab_display_configurations');
        $data['tab_estatus_configurations'] = $this->language->get('tab_estatus_configurations');


        /*
        * Validate privatekey, publickey and the mode
        */
        if (isset($data['compropago_private_key']) && !empty($data['compropago_private_key'])) {
            $this->privateKey = $data['compropago_private_key'];
        }

        if (isset($data['compropago_public_key']) && !empty($data['compropago_public_key'])) {
            $this->publicKey = $data['compropago_public_key'];
        }

        if (!empty($data['compropago_mode']) && isset($data['compropago_mode'])) {
            if ($data['compropago_mode'] == "NO") {
                $this->execMode = false;
             } elseif ($data['compropago_mode'] == "SI"){
                $this->execMode = true;
             }
        }

        if (!empty($data['compropago_location']) && isset($data['compropago_location'])) {
            if ($data['compropago_location'] == "NO") {
                $this->execLocation = false;
             } elseif ($data['compropago_location'] == "SI"){
                $this->execLocation = true;
             }
        }
        
        if (!empty($data['compropago_status']) && isset($data['compropago_status'])) {
            if ($data['compropago_status'] == 0) {
                $this->modActive = false;
            }elseif($data['compropago_status'] == 1){
                $this->modActive = true;
            }
        }

        if (!empty($data['compropago_webhook']) && isset($data['compropago_webhook'])) {
            $this->createWebhook = $data['compropago_webhook'];
        }

        if (isset($this->modActive) && !empty($this->modActive)) {
            if($this->modActive == true){
                $hook_data = $this->hookRetro($this->modActive, $this->publicKey, $this->privateKey, $this->execMode);
                if ($hook_data[0]) {
                    if ($hook_data[2] == "no") {
                        $data['hook_error']         = $hook_data[0];
                        $data['hook_error_text']    = $hook_data[1];
                        $data['compropago_status']  = false;
                    } else{
                        $data['hook_error']         = $hook_data[0];
                        $data['hook_error_text']    = $hook_data[1];
                    }
                }
            }
        }else{
            $data['hook_error'] = true;
            $data['hook_error_text'] = "ComproPago esta deshabilitado.";
        }
        
        
        $data['error_warning'] = isset($this->error['warning']) ?  $this->error['warning'] : '';
        $data['error_private_key'] = isset($this->error['private_key']) ? $this->error['private_key'] : '';
        $data['error_public_key'] = isset($this->error['public_key']) ? $this->error['public_key'] : '';

        /**
         * Inclucion de los breadcrums en la cabecera de la vista de configuracion
         * El orden de inclucion de los breadcrums, sera el mismo al desplegarse
         * Ej: $data['breadcrums'][0] / $data['breadcrums'][1]
         */
        
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(

            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/compropago', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/compropago', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        # carga del modulo de estatus de peticion
        $this->load->model('localisation/order_status');
        # recuperacion de todos los estatus
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        # Inclucion de las partes genericas de la vista de panel de administracion
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        # render final de la vista de configuracion del modulo
        $this->response->setOutput($this->load->view('extension/payment/compropago.tpl', $data));
    }

    /**
     * Crea una instancia de tipo client.
     * @return string
     */
    public function initService()
    {
        $this->client = new Client($this->publicKey, $this->privateKey, $this->execMode);
    }

    /**
     * Crea el webhook si no existe
     * @return string
     */
    public function setWebhook($createWebhook)
    {
        $this->client->api->createWebhook($createWebhook);
    }

    /**
     * Obtiene el error de retroalimentacion.
     * @return string
     */
    private function getErrorText()
    {
        $final = "";
        foreach($this->error as $text){
            $final .= $text;
        }

        return $final;
    }   
    /**
     * Se encarga de generar las retroalimentaciones para el usuario
     * @return bool
     */

    public function hookRetro($enabled, $publicKey, $privateKey, $live)
    {   
        $error = array(false,'','yes');
        if($enabled){
            if(!empty($publicKey) && !empty($privateKey) ){
                try{
                    $client = new Client($publicKey, $privateKey, $live);
                    $compropagoResponse = Validations::evalAuth($client);
                    if(!Validations::validateGateway($client)){
                        $error[1] = 'Invalid Keys, The Public Key and Private Key must be valid before using this module.';
                        $error[0] = true;
                    }else{
                        if($compropagoResponse->mode_key != $compropagoResponse->livemode){
                            $error[1] = 'Your Keys and Your ComproPago account are set to different Modes.';
                            $error[0] = true;
                        }else{
                            if($live != $compropagoResponse->livemode){
                                $error[1] = 'Your Store and Your ComproPago account are set to different Modes.';
                                $error[0] = true;
                            }else{
                                if($live != $compropagoResponse->mode_key){
                                    $error[1] = 'ComproPago ALERT:Your Keys are for a different Mode.';
                                    $error[0] = true;
                                }else{
                                    if(!$compropagoResponse->mode_key && !$compropagoResponse->livemode){
                                        $error[1] = 'WARNING: ComproPago account is Running in TEST Mode, NO REAL OPERATIONS';
                                        $error[0] = true;
                                    }
                                }
                            }
                        }
                    }
                }catch (Exception $e) {
                    $error[2] = "no";
                    $error[1] = $e->getMessage();
                    $error[0] = true;
                }
            }else{
                $error[2] = true;
                $error[1] = 'The Public Key and Private Key must be set before using ComproPago';
                $error[0] = true;
            }
        }else{
            $error[2] = true;
            $error[1] = 'ComproPago is not Enabled';
            $error[0] = true;
        }
        return $error;
    }

    /**
     * @return bool
     * Validacion de error por llaves
     */
    private function validate()
    {
        if (!$this->request->post['compropago_private_key']) {
            $this->error['private_key'] = $this->language->get('error_private_key');
        }

        if (!$this->request->post['compropago_public_key']) {
            $this->error['public_key'] = $this->language->get('error_public_key');
        }

        return !$this->error;
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