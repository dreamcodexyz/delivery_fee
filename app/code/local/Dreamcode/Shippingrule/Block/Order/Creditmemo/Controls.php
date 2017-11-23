<?php


class Magestore_Customercredit_Block_Order_Creditmemo_Controls extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function getGrandTotal()
    {
        $totalsBlock = Mage::getBlockSingleton('sales/order_creditmemo_totals');
        $creditmemo = $totalsBlock->getCreditmemo();
        return $creditmemo->getGrandTotal();
    }

    /**
     * @return mixed
     */
    public function enableTemplate()
    {
        $order_id = Mage::app()->getRequest()->getParam('order_id');
        return Mage::helper('customercredit')->isBuyCreditProduct($order_id);
    }

    /**
     * @return bool
     */
    public function isAssignCredit()
    {
        $data = explode(",", Mage::helper('customercredit')->getGeneralConfig('assign_credit'));
        $order_id = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        $customer = Mage::getSingleton('customer/customer')->load($order->getCustomerId());
        foreach ($data as $group) {
            if ($customer->getGroupId() == $group) {
                return true;
            }
        }
        return false;
    }

}
