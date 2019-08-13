<?php
class Cloudiq_Core_Block_Adminhtml_Config_Edit_Tab_Frame extends Mage_Adminhtml_Block_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /** @var  $_helper Cloudiq_Core_Helper_Config */
    protected $_helper;

    public function _construct() {
        $this->_helper = Mage::helper("cloudiq_core/config");

        $this->setTemplate("cloudiq/core/tab/frame/iframe.phtml");
    }

    /**
     * Return the URL to query for the iframe's content.
     *
     * @return string
     */
    public function getSubmitterUrl() {
        return $this->getUrl("adminhtml/cloudiq/frame", array(
            "module" => $this->getModule()
        ));
    }

    public function getTabLabel() {
         return $this->__($this->getLabel());
    }

    public function getTabTitle() {
        return $this->__($this->getLabel());
    }

    public function canShowTab() {
        return $this->_helper->hasBeenSetUp();
    }

    public function isHidden() {
        return !$this->_helper->hasBeenSetUp();
    }

    protected function getFrameId() {
        return "cloudiq_frame_" . $this->getModule();
    }
}
