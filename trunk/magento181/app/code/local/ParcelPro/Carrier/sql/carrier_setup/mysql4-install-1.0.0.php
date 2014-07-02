<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('parcelpro_carrier_config')};
CREATE TABLE {$this->getTable('parcelpro_carrier_config')} (
  `index_id` int(11) unsigned NOT NULL auto_increment,
  `carrier` varchar(255) NOT NULL default '',
  `config_type` varchar(255) NOT NULL default '',
  `config_key` varchar(255) NOT NULL default '',
  `config_value` varchar(255) NOT NULL default '',
  `apply_to` varchar(255) NOT NULL default '',
  PRIMARY KEY (`index_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('parcelpro_carrier_config')}` (`index_id`, `carrier`, `config_type`, `config_key`, `config_value`, `apply_to`) VALUES
(1, '', 'carrier', 'UPS', 'UPS', '1'),
(2, '', 'carrier', 'FEDEX', 'FEDEX', '2'),
(3, 'UPS', 'method', '01', 'Next Day Air', ''),
(4, 'UPS', 'method', '02', '2nd Day Air', ''),
(5, 'FEDEX', 'method', '01-INT', 'International Priority', ''),
(6, 'FEDEX', 'method', '01-DOM', 'Priority Overnight', ''),
(7, 'FEDEX', 'method', '03-DOM', '2 Day', ''),
(8, 'FEDEX', 'method', '05', 'Standard Overnight', ''),
(9, 'UPS', 'packaging', '25', '10KG BOX', '01'),
(10, 'UPS', 'packaging', '24', '25KG BOX', '01'),
(11, 'UPS', 'packaging', '21', 'EXPRESS BOX', '01'),
(12, 'UPS', 'packaging', '01', 'LETTER', '01'),
(13, 'UPS', 'packaging', '04', 'PAK', '01'),
(14, 'UPS', 'packaging', '03', 'TUBE', '01'),
(15, 'UPS', 'packaging', '02', 'Your Packaging...', '01'),
(16, 'UPS', 'packaging', '25', '10KG BOX', '02'),
(17, 'UPS', 'packaging', '24', '25KG BOX', '02'),
(18, 'UPS', 'packaging', '21', 'EXPRESS BOX', '02'),
(19, 'UPS', 'packaging', '01', 'LETTER', '02'),
(20, 'UPS', 'packaging', '04', 'PAK', '02'),
(21, 'UPS', 'packaging', '03', 'TUBE', '02'),
(22, 'UPS', 'packaging', '02', 'Your Packaging...', '02'),
(23, 'FEDEX', 'packaging', 'LARGE BOX', 'LARGE FEDEX BOX', '01-INT'),
(24, 'FEDEX', 'packaging', 'MEDIUM BOX', 'MEDIUM FEDEX BOX', '01-INT'),
(25, 'FEDEX', 'packaging', 'SMALL BOX', 'SMALL FEDEX BOX', '01-INT'),
(26, 'FEDEX', 'packaging', '02', 'Your Packaging...', '01-INT'),
(27, 'FEDEX', 'packaging', 'LARGE BOX', 'LARGE FEDEX BOX', '01-DOM'),
(28, 'FEDEX', 'packaging', 'MEDIUM BOX', 'MEDIUM FEDEX BOX', '01-DOM'),
(29, 'FEDEX', 'packaging', 'SMALL BOX', 'SMALL FEDEX BOX', '01-DOM'),
(30, 'FEDEX', 'packaging', '02', 'Your Packaging...', '01-DOM'),
(31, 'FEDEX', 'packaging', 'LARGE BOX', 'LARGE FEDEX BOX', '03-DOM'),
(32, 'FEDEX', 'packaging', 'MEDIUM BOX', 'MEDIUM FEDEX BOX', '03-DOM'),
(33, 'FEDEX', 'packaging', 'SMALL BOX', 'SMALL FEDEX BOX', '03-DOM'),
(34, 'FEDEX', 'packaging', '02', 'Your Packaging...', '03-DOM'),
(35, 'FEDEX', 'packaging', 'LARGE BOX', 'LARGE FEDEX BOX', '05'),
(36, 'FEDEX', 'packaging', 'MEDIUM BOX', 'MEDIUM FEDEX BOX', '05'),
(37, 'FEDEX', 'packaging', 'SMALL BOX', 'SMALL FEDEX BOX', '05'),
(38, 'FEDEX', 'packaging', '02', 'Your Packaging...', '05'),
(39, 'UPS', 'special_services', 'NO_SIGNATURE_REQUIRED', 'Not Required', 'UPS'),
(40, 'UPS', 'special_services', 'ADULT', 'Adult Signature', 'UPS'),
(41, 'UPS', 'special_services', 'DIRECT', 'Direct Signature', 'UPS'),
(42, 'UPS', 'special_services', 'COD', 'COD', 'UPS'),
(43, 'UPS', 'special_services', 'saturday_pickup', 'Saturday pickup', 'UPS'),
(44, 'UPS', 'special_services', 'saturday_delivery', 'Saturday delivery', 'UPS'),
(45, 'UPS', 'special_services', 'hold_at_fedex_location', 'Hold At Fedex Location', 'UPS'),
(46, 'UPS', 'special_services', 'thermal_label', 'Thermal label', 'UPS'),
(47, 'FEDEX', 'special_services', 'NO_SIGNATURE_REQUIRED', 'Not Required', 'FEDEX'),
(48, 'FEDEX', 'special_services', 'ADULT', 'Adult Signature', 'FEDEX'),
(49, 'FEDEX', 'special_services', 'DIRECT', 'Direct Signature', 'FEDEX'),
(50, 'FEDEX', 'special_services', 'COD', 'COD', 'FEDEX'),
(51, 'FEDEX', 'special_services', 'saturday_pickup', 'Saturday pickup', 'FEDEX'),
(52, 'FEDEX', 'special_services', 'saturday_delivery', 'Saturday delivery', 'FEDEX'),
(53, 'FEDEX', 'special_services', 'hold_at_fedex_location', 'Hold At Fedex Location', 'FEDEX'),
(54, 'FEDEX', 'special_services', 'thermal_label', 'Thermal label', 'FEDEX');

    ");

$installer->addAttribute('catalog_product', 'insured', array(
		'group'             => 'General',
		'type'              => Varien_Db_Ddl_Table::TYPE_VARCHAR,
		'backend'           => '',
		'frontend'          => '',
		'label'             => 'Insured',
		'input'             => 'text',
		'class'             => '',
		'source'            => '',
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'visible'           => true,
		'required'          => true,
		'user_defined'      => true,
		'default'           => '',
		'searchable'        => true,
		'filterable'        => true,
		'comparable'        => true,
		'visible_on_front'  => true,
		'used_in_product_listing' => true,
		'unique'            => false,
		'apply_to'          => 'simple,configurable,virtual',
		'is_configurable'   => false
));

$installer->endSetup(); 