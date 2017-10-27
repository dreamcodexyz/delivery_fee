<?php
class Dreamcode_Shippingrule_Model_Observer
{
    public function saveOscBefore(Varien_Event_Observer $observer)
    {
        $data = Mage::getModel('core/date')->date('m/d/Y');
        $post = $observer->getEvent()->getPost();
        if(array_key_exists('onestepcheckout_date', $post)){
            $data = $post['onestepcheckout_date'];
        }
        Mage::getSingleton('core/session')->setDcDeliveryDate($data); 
        return $this;
    }
}