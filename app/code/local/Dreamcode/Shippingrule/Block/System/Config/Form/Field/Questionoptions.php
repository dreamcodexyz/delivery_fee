<?php
class Dreamcode_Shippingrule_Block_System_Config_Form_Field_Questionoptions extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('date', array(
            'label' => Mage::helper('adminhtml')->__('Date'),
            'style' => 'width:80px',
        ));
        $this->addColumn('fee', array(
            'label' => Mage::helper('adminhtml')->__('Fee'),
            'style' => 'width:110px',
        ));
        $this->addColumn('note', array(
            'label' => Mage::helper('adminhtml')->__('Note'),
            'style' => 'width:200px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Option');
        parent::__construct();
    }
}