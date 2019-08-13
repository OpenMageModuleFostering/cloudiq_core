<?php
class Cloudiq_Core_Block_Adminhtml_Config_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct();

        $this->_blockGroup = 'cloudiq_core';
        $this->_controller = 'adminhtml_config';
        $this->_mode = 'edit';

    }

    public function getHeaderText() {
        return $this->__('cloud.IQ Settings');
    }
}
