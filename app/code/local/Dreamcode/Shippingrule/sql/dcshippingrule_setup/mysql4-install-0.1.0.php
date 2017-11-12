<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$this->getConnection()->dropColumn($this->getTable('sales/order'), 'dcfee_amount');
$this->getConnection()->dropColumn($this->getTable('sales/order'), 'base_dcfee_amount');

$installer->addAttribute('order', 'dcfee_amount', array('type' => 'decimal'));
$installer->addAttribute('order', 'base_dcfee_amount', array('type' => 'decimal'));

$this->getConnection()->dropColumn($this->getTable('sales/quote_address'), 'dcfee_amount');
$this->getConnection()->dropColumn($this->getTable('sales/quote_address'), 'base_dcfee_amount');

$installer->addAttribute('quote_address', 'dcfee_amount', array('type' => 'decimal'));
$installer->addAttribute('quote_address', 'base_dcfee_amount', array('type' => 'decimal'));

$installer->endSetup();