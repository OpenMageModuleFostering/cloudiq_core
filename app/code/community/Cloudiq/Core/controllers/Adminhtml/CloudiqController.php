<?php
class Cloudiq_Core_Adminhtml_CloudiqController extends Mage_Adminhtml_Controller_Action {
    public function editAction() {
        $this->loadLayout();
        $this->_setActiveMenu('cloudiq');
        $this->renderLayout();
    }

    public function saveAction() {
        Mage::dispatchEvent("cloudiq_core_config_save", array('request'=>$this->getRequest()));

        $request = $this->getRequest();
        $form_data = new Varien_Object($request->getParam("global"));

        if (count($form_data->getData()) > 0) {
            // Save global Cloud.IQ config data to system config

            $account_id = trim($form_data->getData("account_id"));
            $token = trim($form_data->getData("token"));
            $enabled = ($form_data->getData("enabled")) ? 1 : 0;

            // Validate the form fields
            $validation_errors = 0;

            if ($account_id == "") {
                $validation_errors++;
                Mage::getSingleton("adminhtml/session")->addError($this->__("cloud.IQ: Account ID can not be empty."));
            }

            if ($token == "") {
                $validation_errors++;
                Mage::getSingleton("adminhtml/session")->addError($this->__("cloud.IQ: Token can not be empty."));
            }

            if ($validation_errors == 0 && $enabled) {
                // Check if the account is valid using the API
                $result = Mage::getModel("cloudiq_core/api_core")->lookupAccount($account_id, $token);
                if ($result["status"] !== true) {
                    $validation_errors++;
                    Mage::getSingleton("adminhtml/session")->addError(sprintf("%s: %s", $this->__("cloud.IQ: The account credentials provided could not be verified"), $this->__($result["content"])));
                }
            }

            if ($validation_errors == 0) {
                // Save configuration to system config
                $_system_config = Mage::getConfig();

                $_system_config->saveConfig("cloudiq_core/global/account_id", $account_id);
                $_system_config->saveConfig("cloudiq_core/global/token", $token);

                $_system_config->saveConfig("cloudiq_core/global/enabled", $enabled);

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("cloudiq_core")->__("Global configuration saved."));

                // Reload system config, so changes are picked up
                $_system_config->reinit();
                Mage::app()->reinitStores();
            }
        }

        // Redirect back to the form, preserving any params (such as active tab)
        $this->_redirect("*/*/edit", array("_current" => true));
    }

    public function frameAction() {
        $_config_helper = Mage::helper("cloudiq_core/config");

        $module = $this->getRequest()->getParam("module");
        $profile_id = $this->getRequest()->getParam("profile_id");

        $destination = $_config_helper->getCloudiqUrl($module, ($profile_id != null) ? $profile_id : null);

        $additional_parameters = array();

        // Pre-populate the sign up form with current user details
        if ($module == "signUp") {
            /** @var $user Mage_Admin_Model_User */
            $user = Mage::getSingleton('admin/session')->getUser();
            $additional_parameters["username"] = $user->getEmail();
            $additional_parameters["fullName"] = $user->getName();
        }

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock("core/template", "")
                ->setTemplate("cloudiq/core/tab/frame/submitter.phtml")
                ->setDestination($destination)
                ->setAccountId($_config_helper->getAccountId())
                ->setToken($_config_helper->getToken())
                ->setAdditionalParameters($additional_parameters)
                ->toHtml()
        );
    }
}
