<?php
class Dreamcode_Shippingrule_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
    protected $_code = 'dcfee';

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $session = Mage::getSingleton('checkout/session');

        if(Mage::helper('core')->isModuleEnabled('Dreamcode_Shippingrule') && Mage::helper('core')->isModuleOutputEnabled('Dreamcode_Shippingrule'))
        {
            if(Mage::getStoreConfig('dcshippingrule/general/allow') != 1) {
                $session->setBaseDcfeeAmount(0);
                return $this;
            }
        }else{
            $session->setBaseDcfeeAmount(0);
            return $this;
        }


        $scopeId = Mage::app()->getStore()->getStoreId();

        // $this->_setAmount(0);
        // $this->_setBaseAmount(0);
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            $session->setBaseDcfeeAmount(0);
            return $this; //this makes only address type shipping to come through
        }
        $quote = $address->getQuote();
        $exist_amount = $quote->getDcfeeAmount();
        $exist_base_amount = $quote->getBaseDcfeeAmount();
        $base_fee = 0; 
        $fee = 0; 
        $deliveryDateSelected = Mage::getSingleton('core/session')->getDcDeliveryDate(); 
        if(!$deliveryDateSelected){
            $deliveryDateSelected = '01/01/1970';
            // $deliveryDateSelected = Mage::getModel('core/date')->date('m/d/Y');
        }
        $today = Mage::getModel('core/date')->date('m/d/Y');
        $nextDate = Date('m/d/Y', strtotime('+1 day'));


        if(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_same_day') == 1 && $today == $deliveryDateSelected){
            
            $slot_time = Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_same_day_enable_by_slot_time');
            
            $start_time = strtotime( $today );
            $end_time = strtotime( $nextDate );

            if($slot_time != 0) {
                $slot_time = explode("-", $slot_time);
                $start_time = strtotime( $today . ' ' . $slot_time[0] );
                $end_time = strtotime( $today . ' ' . $slot_time[1] );
            }
            
            $slot_time_now = Mage::getModel('core/date')->timestamp(time()); //strtotime(Mage::getSingleton('core/date')->gmtDate());
            //echo ($slot_time_now .' '. $start_time .' '. $end_time) .'<br>'; 

            if($slot_time_now >= $start_time && $slot_time_now <= $end_time){
                $base_fee = max($base_fee, Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_same_day'));
            }

        } 

        if(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_next_day') == 1 && $nextDate == $deliveryDateSelected){
            $base_fee = max($base_fee, Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_next_day'));
        } 

        $configValue = unserialize(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/special_days'));
        if(!empty($configValue)){
            foreach($configValue as $config){
                if($deliveryDateSelected == $config['date']){
                    $base_fee = max($base_fee, $config['fee']);
                }
            }
        }

        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $fee = Mage::helper('directory')->currencyConvert($base_fee, $baseCurrencyCode, $currentCurrencyCode);

        $balance = $fee - $exist_amount;
        $base_balance = $base_fee - $exist_base_amount;

        
        $session->setBaseDcfeeAmount($base_balance);


        $address->setDcfeeAmount($balance);
        $address->setBaseDcfeeAmount($base_balance);

        $quote->setDcfeeAmount($balance);
        $quote->setBaseDcfeeAmount($base_balance);

        $address->setGrandTotal($address->getGrandTotal() + $address->getDcfeeAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseDcfeeAmount());
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amt = $address->getDcfeeAmount();
        if(isset($amt) && $amt > 0)
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::getStoreConfig('dcshippingrule/general/title'),
                'value' => $amt
            ));
        return $this;
    }
}