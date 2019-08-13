<?php
class Cloudiq_Core_Block_Adminhtml_Config_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    protected $_config_helper;

    public function __construct() {
        parent::__construct();
        $this->setId('cloudiq_config_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('cloud.IQ Configuration'));
        $this->_config_helper = Mage::helper('cloudiq_core/config');
        $this->setTemplate('cloudiq/core/tabs.phtml');
    }

    protected function _beforeToHtml() {
        // Open the Global Settings form tab by default
        $this->setActiveTab("global_section");
        return parent::_beforeToHtml();
    }
}
