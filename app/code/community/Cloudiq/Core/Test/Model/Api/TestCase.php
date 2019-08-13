<?php
abstract class Cloudiq_Core_Test_Model_Api_TestCase extends EcomDev_PHPUnit_Test_Case {
    /**
     * @param string $http_method
     * @param int $status_code
     * @param string $response_filename
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getRequestMock($http_method, $status_code, $response_filename) {
        $file_contents = $this->_loadFileContents($response_filename);

        $this->assertNotNull($file_contents);
        $this->assertNotEmpty($file_contents);

        /** @var $response Cloudiq_Core_Model_Api_Response */
        $response = Mage::getModel('cloudiq_core/api_response');
        $response
            ->setStatusCode($status_code)
            ->setRawResponse($file_contents);

        $request_mock = $this->getModelMock('cloudiq_core/api_request', array('send'));

        $request_mock
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo($http_method))
            ->will($this->returnValue($response));

        return $request_mock;
    }

    protected function _loadFileContents($file) {
        $directory_tree = array(
            Mage::getModuleDir('', 'Cloudiq_Core'),
            'Test',
            'Model',
            'Api',
            'data',
            $file
        );

        $file_path = join(DS, $directory_tree);

        return file_get_contents($file_path);
    }
}
