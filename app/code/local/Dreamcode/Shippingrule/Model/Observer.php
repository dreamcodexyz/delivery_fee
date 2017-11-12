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
}