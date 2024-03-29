<?php
class Cloudiq_Core_Block_Adminhtml_Config_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $url = Mage::helper('adminhtml')->getUrl('*/*/save');
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $url, 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
