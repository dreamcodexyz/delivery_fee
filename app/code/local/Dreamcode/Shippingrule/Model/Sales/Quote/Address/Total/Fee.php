<?php
class Dreamcode_Shippingrule_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
    protected $_code = 'dcfee';

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $scopeId = Mage::app()->getStore()->getStoreId();

        // $this->_setAmount(0);
        // $this->_setBaseAmount(0);
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this; //this makes only address type shipping to come through
        }
        $quote = $address->getQuote();
        $exist_amount = $quote->getDcfeeAmount();
        $fee = 0; //Dreamcode_Shippingrule_Model_Fee::getFee();
        $deliveryDateSelected = Mage::getSingleton('core/session')->getDcDeliveryDate(); 
        if(!$deliveryDateSelected){
            $deliveryDateSelected = Mage::getModel('core/date')->date('m/d/Y');
        }
        $today = Mage::getModel('core/date')->date('m/d/Y');

        $nextDate = Date('m/d/Y', strtotime('+1 day'));
        if(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_same_day') == 1 && $today == $deliveryDateSelected){
            $fee += Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_same_day');
        } else if(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_next_day') == 1 && $nextDate == $deliveryDateSelected){
            $fee += Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_next_day');
        } else {
            $configValue = unserialize(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/special_days'));
            if(!empty($configValue)){
                foreach($configValue as $config){
                    if($deliveryDateSelected == $config['date']){
                        $fee += $config['fee'];
                    }
                }
            }
        }
        
        $balance = $fee - $exist_amount;
        $address->setDcfeeAmount($balance);
        $address->setBaseDcfeeAmount($balance);
        $quote->setDcfeeAmount($balance);
        $address->setGrandTotal($address->getGrandTotal() + $address->getDcfeeAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseDcfeeAmount());
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amt = $address->getDcfeeAmount();
        if($amt)
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::getStoreConfig('dcshippingrule/general/title'),
                'value' => $amt
            ));
        return $this;
    }
}