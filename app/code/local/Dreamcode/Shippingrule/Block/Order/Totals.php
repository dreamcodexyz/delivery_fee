<?php

class Dreamcode_Shippingrule_Block_Order_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $order = $this->getParentBlock()->getOrder();
        // Zend_debug::dump($order->getDcfeeAmount());die();
        if ($order->getDcfeeAmount() > 0) {
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code' => $this->getCode(),
                'value' => +$order->getDcfeeAmount(),
                'base_value' => +$order->getBaseDcfeeAmount(),
                'label' => Mage::getStoreConfig('dcshippingrule/general/title'),
                )), 'subtotal');
        }
    }

}
