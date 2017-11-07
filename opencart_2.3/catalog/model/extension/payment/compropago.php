<?php

class ModelExtensionPaymentCompropago extends Model
{
    const CP_VERSION = "2.1.0";

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/compropago');

        $method_data = array(
            'code'       => 'compropago',
            'title'      => '<img src="https://cdn.compropago.com/cp-assets/ui-compropago/logo.svg" style="height:50px;"  alt="ComproPago - Efectivo"/>',
            'terms'      => false,
            'sort_order' => $this->config->get('compropago_sort_order')
        );

        return $method_data;
    }
}