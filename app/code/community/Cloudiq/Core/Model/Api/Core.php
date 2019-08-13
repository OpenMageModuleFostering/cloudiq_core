<?php

class Cloudiq_Core_Model_Api_Core extends Cloudiq_Core_Model_Api_Abstract {

    /**
     * Request account information from the cloud.IQ API for either the
     * account provided or the account stored in the configuration (if none
     * is provided). Returns an array with two elements:
     *   status  - boolean flag, true if the request was successful, false otherwise
     *   content - the account information XML element, if the request was successful
     *             or the error message if it failed
     *
     * @param $account_id Account ID to lookup instead of the configured one.
     * @param $token      Account token to use instead of the configured one.
     *
     * @return array
     */
    public function lookupAccount($account_id = NULL, $token = NULL) {
        $parameters = array(
            "mode"   => "lookup",
            "action" => "account"
        );

        if ($account_id != NULL && $token != NULL) {
            // Lookup the account provided instead of the one saved
            $parameters["id"]    = $account_id;
            $parameters["token"] = $token;
        }

        /** @var Cloudiq_Core_Model_Api_Request $request */
        $request = $this->_getRequestObject()->setParameters($parameters);
        $response = $request->send(Zend_Http_Client::GET);

        $result = array(
            "status" => false,
            "content" => ""
        );

        if ($response) {
            if ($response->wasSuccessful()) {
                $account_response = $response->getResponse()->account;

                if (!is_null($account_response)) {
                    if ($account_response["status"] == Cloudiq_Core_Model_Api_Response::STATUS_SUCCESS) {
                        $result["status"] = true;
                        $result["content"] = $account_response;
                    } else {
                        // Unsuccessful account response
                        $result["content"] = $response->getApiStatusCodeDescription($account_id["status"]);
                        if ($account_response->description) {
                            $result["content"] .= sprintf(": %s", $account_response->description);
                        }
                    }
                } else {
                    $result["content"] = "Unknown API response status";
                }
            } else {
                // Unsuccessful response
                $result["content"] = $response->getErrorMessage();
            }
        } else {
            $result["content"] = "Failed to connect to cloud.IQ API";
        }

        return $result;
    }
}
