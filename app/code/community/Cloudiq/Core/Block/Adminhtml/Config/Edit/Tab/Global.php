<?php
class Cloudiq_Core_Block_Adminhtml_Config_Edit_Tab_Global extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    /** @var $_helper Cloudiq_Core_Helper_Data */
    protected $_helper;
    /** @var $_config_helper Cloudiq_Core_Helper_Config */
    protected $_config_helper;

    public function __construct() {
        parent::__construct();

        $this->_helper = Mage::helper('cloudiq_core');
        $this->_config_helper = Mage::helper('cloudiq_core/config');

        $this->setTemplate('cloudiq/core/tab/global.phtml');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>$this->_helper->__('Global Settings')));

        $fieldset->addField('global[enabled]', 'select', array(
            'label' => $this->_helper->__('Enable Account?'),
            'title' => $this->_helper->__('Enable Account?'),
            'name'  => 'global[enabled]',
            'value' => $this->_config_helper->isEnabled(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField('global[account_id]', 'text', array(
            'label' => $this->_helper->__('cloud.IQ Account ID'),
            'title' => $this->_helper->__('cloud.IQ Account ID'),
            'name'  => 'global[account_id]',
            'note'  => "Cut and paste this from the cloud.IQ confirmation message.",
            'value' => $this->_config_helper->getAccountId(),
            'required' => true
        ));

        $fieldset->addField('global[token]', 'text', array(
            'label' => $this->_helper->__('cloud.IQ Token'),
            'title' => $this->_helper->__('cloud.IQ Token'),
            'name'  => 'global[token]',
            'note'  => "Cut and paste this from the cloud.IQ confirmation message.",
            'value' => $this->_config_helper->getToken(),
            'required' => true
        ));

        $this->setForm($form);
    }

    public function getTabLabel() {
        $label = $this->hasBeenSetUp() ? "Global Settings" : "cloud.IQ Sign up";
        return $this->__($label);
    }

    public function getTabTitle() {
        $label = $this->hasBeenSetUp() ? "Global Settings" : "cloud.IQ Sign up";
        return $this->__($label);
    }

    public function canShowTab() {
        // Always show the Global settings tab
        return true;
    }

    public function isHidden() {
        // Always show the Global settings tab
        return false;
    }

    /**
     * Check if the module has been setup with API access credentials.
     *
     * @return bool
     */
    protected function hasBeenSetUp() {
        return $this->_config_helper->hasBeenSetUp();
    }

    /**
     * Return the URL used for the Setup iFrame.
     *
     * @return string
     */
    protected function getSetupUrl() {
        return $this->getUrl("adminhtml/cloudiq/frame", array("module" => "signUp", "profile_id" => Cloudiq_Core_Helper_Config::CLOUDIQ_IFRAME_PROFILE_ID));
    }
}
