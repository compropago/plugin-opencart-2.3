<?php

class ModelExtensionPaymentCompropago extends Model {

	public function install() {
		$this->db->query(
			'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'compropago_orders` (
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
            )ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;');

		$this->db->query(
			'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'compropago_transactions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `orderId` int(11) NOT NULL,
            `date` int(11) NOT NULL,
            `compropagoId` varchar(50) NOT NULL,
            `compropagoStatus` varchar(50) NOT NULL,
            `compropagoStatusLast` varchar(50) NOT NULL,
            `ioIn` mediumtext,
            `ioOut` mediumtext,
            PRIMARY KEY (`id`)
            )ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;');
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "compropago_orders`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "compropago_transactions`;");
	}
}