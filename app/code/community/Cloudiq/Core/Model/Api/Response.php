<?php
/**
 * @method setRawResponse(Zend_Http_Response $raw_response)
 * @method Zend_Http_Response getRawResponse()
 * @method setHttpStatusCode(int $status_code)
 * @method int getHttpStatusCode()
 * @method setXmlParseErrors(array $errors)
 * @method array getXmlParseErrors()
 * @method setResponse(SimpleXMLElement $data)
 * @method SimpleXMLElement getResponse()
 * @method bool hasResponse()
 */
class Cloudiq_Core_Model_Api_Response extends Varien_Object {

    const STATUS_SUCCESS = 1;
    const STATUS_AUTHENTICATION_FAILURE = 101;
    const STATUS_REQUESTING_IP_NOT_VALID = 102;
    const STATUS_INVALID_FUNCTION_REQUEST = 103;
    const STATUS_REQUEST_MISSING_A_MANDATORY_PARAMETER = 104;
    const STATUS_FORMAT_ERROR_IN_REQUEST_PARAMETERS = 105;
    const STATUS_REQUEST_NOT_VALID_FOR_GIVEN_ACCOUNT = 106;
    const STATUS_INTERNAL_ERROR_WHILE_PROCESSING_YOUR_REQUEST = 107;
    const STATUS_VALIDATION_FAILED = 200;

    public static $status_code_descriptions = array(
        self::STATUS_SUCCESS => "Success",
        self::STATUS_AUTHENTICATION_FAILURE => "Authentication failure",
        self::STATUS_REQUESTING_IP_NOT_VALID => "Requesting ip not valid",
        self::STATUS_INVALID_FUNCTION_REQUEST => "Invalid function request",
        self::STATUS_REQUEST_MISSING_A_MANDATORY_PARAMETER => "Request missing a mandatory parameter",
        self::STATUS_FORMAT_ERROR_IN_REQUEST_PARAMETERS => "Format error in request parameters",
        self::STATUS_REQUEST_NOT_VALID_FOR_GIVEN_ACCOUNT => "Request not valid for given account",
        self::STATUS_INTERNAL_ERROR_WHILE_PROCESSING_YOUR_REQUEST => "Internal error while processing your request",
        self::STATUS_VALIDATION_FAILED => "Validation failed"
    );

    /**
     * Populate the response data from the given Zend_Http_Response object.
     *
     * @param Zend_Http_Response $raw_response
     *
     * @return $this
     */
    public function populate(Zend_Http_Response $raw_response) {
        $this->setRawResponse($raw_response);

        $this->setHttpStatusCode((int) $raw_response->getStatus());

        // Handle libxml errors internally
        $this->setXmlParseErrors(NULL);
        $xml_errors_setting = libxml_use_internal_errors(true);

        $decoded_data = simplexml_load_string(utf8_encode($raw_response->getBody()));

        if ($decoded_data) {
            $this->setResponse($decoded_data);
        } else {
            $xml_errors = array();
            foreach (libxml_get_errors() as $error) {
                $xml_errors[] = preg_replace("/\n/", " ", $error->message);
            }
            $this->setXmlParseErrors($xml_errors);
        }

        // Reset libxml error settings
        libxml_clear_errors();
        libxml_use_internal_errors($xml_errors_setting);

        return $this;
    }

    public function getResponseXPath($xpath) {
        if ($this->hasResponse()) {
            return $this->getResponse()->xpath($xpath);
        } else {
            return NULL;
        }
    }

    /**
     * Get the API response status code. Return false if there
     * is no status code.
     *
     * @return int
     */
    public function getApiStatusCode() {
        $status_attribute_xpath = $this->getResponseXPath('/response/@status[1]');
        if (count($status_attribute_xpath) == 1) {
            $status_attribute = (string) $status_attribute_xpath[0];
            if (is_numeric($status_attribute)) {
                return (int) $status_attribute;
            }
        }
        return false;
    }

    /**
     * Return the description text for the given API response code. Returns
     * "Unknown" for unknown codes.
     *
     * @param  int    $code API response code
     *
     * @return string
     */
    public function getApiStatusCodeDescription($code) {
        if (isset(self::$status_code_descriptions[$code])) {
            return self::$status_code_descriptions[$code];
        }

        return "Unknown";
    }

    /**
     * Perform a check on the HTTP Status Code returned by the request and evaluate the status attribute of
     * the wrapping <response> element for a valid status code.
     *
     * @return bool
     */
    public function wasSuccessful() {
        if ($this->getResponse()) {
            $http_status_string = (string) $this->getHttpStatusCode();

            if (substr($http_status_string, 0, 1) == "2") {
                $api_status = $this->getApiStatusCode();

                if ($api_status !== false) {
                    return $api_status == self::STATUS_SUCCESS;
                }
            }
        }

        return false;
    }

    /**
     * Choose and return the appropriate text error message for this request.
     * Returns false if the request was successful.
     *
     * @return bool|string
     */
    public function getErrorMessage() {
        $message = false;

        if (!$this->wasSuccessful()) {
            // Check for API status errors
            $api_status_code = $this->getApiStatusCode();
            if ($api_status_code !== false) {
                $message = $this->getApiStatusCodeDescription($api_status_code);
            } else {
                // Check for response parse errors
                $xml_parse_errors = $this->getXmlParseErrors();
                if (!empty($xml_parse_errors)) {
                    $message = "Failed to parse the API response: " . implode(", ", $xml_parse_errors);
                } else {
                    // Check for HTTP errors
                    $http_status_code = $this->getHttpStatusCode();
                    if (!is_null($http_status_code)) {
                        $message = "HTTP Error: " . Zend_Http_Response::responseCodeAsText($http_status_code);
                    } else {
                        $message = "Unknown error";
                    }
                }
            }
        }

        return $message;
    }
}
