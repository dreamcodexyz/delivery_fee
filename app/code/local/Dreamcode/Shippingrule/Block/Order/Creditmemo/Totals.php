<?php
 
class Dreamcode_Shippingrule_Block_Order_Creditmemo_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        if ($creditmemo->getDcfeeAmount() > 0.0001) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code' => $this->getCode(),
                'label' => Mage::getStoreConfig('dcshippingrule/general/title'),
                'value' => +$invoice->getDcfeeAmount(),
                'base_value' => +$invoice->getBaseDcfeeAmount(),
                )), 'subtotal');
        }
    }

}
