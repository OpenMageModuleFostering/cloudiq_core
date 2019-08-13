<?php
/**
 * @method setParameters(array $parameters)
 * @method getParameters()
 */
class Cloudiq_Core_Model_Api_Request extends Varien_Object {

    const LOG_LOCATION = "cloudiq_api.log";

    /**
     * @param $method HTTP verb to use when making the request, e.g. GET, POST, DELETE, PUT.
     *
     * @throws Zend_Http_Exception
     *
     * @return Cloudiq_Core_Model_Api_Response
     */
    public function send($method) {
        $_config_helper = Mage::helper("cloudiq_core/config");

        $parameters = $this->getParameters();
        $get_params = array();
        $post_params = array();

        if (!isset($parameters["token"])) {
            // Authenticate through GET parameters
            $get_params["id"] = $_config_helper->getAccountId();
            $get_params["token"] = $_config_helper->getToken();
        }

        if ($method == Zend_Http_Client::GET) {
            $get_params = array_merge($get_params, $parameters);
        } else {
            $post_params = array_merge($post_params, $parameters);
        }

        $client = new Zend_Http_Client();
        $client
            ->setUri($this->getEndpoint())
            ->setParameterGet($get_params)
            ->setParameterPost($post_params)
            ->setMethod($method);

        try {
            /** @var $request_result Zend_Http_Response */
            $request_result = $client->request();
        } catch (Zend_Http_Client_Exception $e) {
            $this->_log(Zend_Log::ERR, $e->getMessage(), $this->getEndpoint(), $method, $get_params, $post_params);
            return null;
        }

        /** @var $response Cloudiq_Core_Model_Api_Response */
        $response = Mage::getModel('cloudiq_core/api_response');
        $response->populate($request_result);

        $this->_log(Zend_Log::DEBUG, "Success", $this->getEndpoint(), $method, $get_params, $post_params, $response);

        return $response;
    }

    /**
     * Return the URI to make API calls against.
     *
     * @return string
     */
    public function getEndpoint() {
        return Mage::helper("cloudiq_core/config")->getCloudiqApiUrl();
    }

    /**
     * Print the API request details to a log file.
     *
     * @param                                 $level
     * @param                                 $status
     * @param                                 $url
     * @param                                 $method
     * @param array                           $get_params
     * @param array                           $post_params
     * @param Cloudiq_Core_Model_Api_Response $response
     */
    protected function _log($level, $status, $url, $method, $get_params = array(), $post_params = array(), $response = NULL) {
        $message = sprintf("%s\nMethod: %s\nEndpoint: %s\n", $status, $method, $url);

        if (!empty($get_params)) {
            array_walk($get_params, function (&$v, $k) { $v = sprintf("%s = %s", $k, $v); });
            $message .= sprintf("GET Parameters:\n\t%s\n", implode("\n\t", $get_params));
        }

        if (!empty($post_params)) {
            array_walk($post_params, function (&$v, $k) { $v = sprintf("%s = %s", $k, $v); });
            $message .= sprintf("POST Parameters:\n\t%s\n", implode("\n\t", $post_params));
        }

        if (!is_null($response)) {
            $message .= sprintf("Response status: %s\n", ($response->wasSuccessful() ? "Successful" : "Failed"));
            if (!$response->wasSuccessful()) {
                $message .= sprintf("Response error: %s\n", $response->getErrorMessage());
            }
            $raw_response = $response->getRawResponse();
            if ($raw_response) {
                $message .= sprintf("Raw response:\n\t%s\n\t%s\n",
                                        $raw_response->getHeadersAsString(true, "\n\t"),
                                        implode("\n\t", explode("\n", $raw_response->getBody()))
                );
            }
        }

        Mage::log($message, $level, self::LOG_LOCATION);
    }
}
