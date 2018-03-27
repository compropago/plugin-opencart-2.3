<?php

class ModelExtensionPaymentCompropago extends Model
{
    const CP_VERSION = "2.1.0";

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/compropago');

        $method_data = array(
            'code'       => 'compropago',
            'title'      => '<img src="https://compropago.com/plugins/logo.png" style="height:25px;"  alt="ComproPago - Efectivo"/> - Pago en efectivo',
            'terms'      => false,
            'sort_order' => $this->config->get('compropago_sort_order')
        );

        return $method_data;
    }
}