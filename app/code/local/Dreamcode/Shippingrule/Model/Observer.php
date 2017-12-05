<?php
class Dreamcode_Shippingrule_Model_Observer
{
    public function saveOscBefore(Varien_Event_Observer $observer)
    {
        $data = Mage::getModel('core/date')->date('m/d/Y');
        $post = $observer->getEvent()->getPost();
        if(array_key_exists('onestepcheckout_date', $post)){
            $date = str_replace('/', '-', $post['onestepcheckout_date']);
            $data = date('m/d/Y', strtotime($date)); 
        }
        Mage::getSingleton('core/session')->setDcDeliveryDate($data); 
        return $this;
    }

    /**
     * @param $observer
     */
    public function paypal_prepare_line_items($observer)
    {
        $session = Mage::getSingleton('checkout/session');
        if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
            $paypalCart = $observer->getEvent()->getPaypalCart();
            if ($paypalCart) {
                $salesEntity = $paypalCart->getSalesEntity();
                $fee = $salesEntity->getDcfeeAmount();
                if (!$fee)
                    $fee = $session->getBaseDcfeeAmount();
                if ($fee)
                    $paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SHIPPING, abs((float)$fee), Mage::getStoreConfig('dcshippingrule/general/title'));
            }
        } else {
            $salesEntity = $observer->getSalesEntity();
            $additional = $observer->getAdditional();
            if ($salesEntity && $additional) {
                $items = $additional->getItems();
                $items[] = new Varien_Object(array(
                    'name' => Mage::getStoreConfig('dcshippingrule/general/title'),
                    'qty' => 1,
                    'amount' => +(abs((float)$salesEntity->getDcfeeAmount())),
                ));
                $additional->setItems($items);
            }
        }
    }
}