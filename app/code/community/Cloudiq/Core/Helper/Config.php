<?php
class Cloudiq_Core_Helper_Config extends Mage_Core_Helper_Abstract {

    const CLOUDIQ_BASE_URL = "https://platform.cloud-iq.com/";

    /**
     * cloud.IQ use a profile_id GET parameter to tell their web app to support loading in an iframe.
     */
    const CLOUDIQ_IFRAME_PROFILE_ID = 2;

    public function hasBeenSetUp() {
        return $this->getToken() != "";
    }

    public function getAccountId() {
        return Mage::getStoreConfig('cloudiq_core/global/account_id');
    }

    public function getToken() {
        return Mage::getStoreConfig('cloudiq_core/global/token');
    }

    public function isEnabled() {
        return Mage::getStoreConfig('cloudiq_core/global/enabled');
    }

    /**
     * Return a URL for the cloud.IQ service.
     *
     * @param $module
     *
     * @return string
     */
    public function getCloudiqUrl($module, $profile_id = false) {
        $url = self::CLOUDIQ_BASE_URL;

        $parameters = array();

        if ($module != "") {
            $parameters[] = "module=" . $module;
        }

        if ($profile_id) {
            $parameters[] = "profileId=" . $profile_id;
        }

        $parameters = implode("&", $parameters);
        if ($parameters != "") {
            $url .= "?" . $parameters;
        }

        return $url;
    }

    /**
     * Return the URL for cloud.IQ API calls.
     *
     * @return string
     */
    public function getCloudiqApiUrl() {
        return self::CLOUDIQ_BASE_URL . "gateway";
    }
}
