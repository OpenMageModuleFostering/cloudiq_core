<?php
class Cloudiq_Core_Model_Api_Abstract extends Varien_Object {
    /**
     * @return Cloudiq_Core_Model_Api_Request
     */
    protected function _getRequestObject() {
        return Mage::getModel('cloudiq_core/api_request');
    }
}

