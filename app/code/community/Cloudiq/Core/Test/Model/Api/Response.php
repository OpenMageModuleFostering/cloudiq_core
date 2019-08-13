<?php

class Cloudiq_Core_Test_Model_Api_Response extends Cloudiq_Core_Test_Model_Api_TestCase {

    /** @var Cloudiq_Core_Model_Api_Response */
    protected $_model;

    protected function setUp() {
        parent::setUp();

        $this->_model = Mage::getModel('cloudiq_core/api_response');
    }

    protected function tearDown() {
        parent::tearDown();

        $this->_model = null;
    }

    /**
     * @test
     */
    public function testClassConstruction() {
        $this->assertInstanceOf('Cloudiq_Core_Model_Api_Response', $this->_model);
    }

    /**
     * @test
     */
    public function testWasSuccessfulNoRequest() {
        $this->assertFalse($this->_model->wasSuccessful());
    }

    /**
     * @test
     */
    public function testWasSuccessfulStatusCodeAndResponseAttribute() {
        $successful_response = Zend_Http_Response::fromString($this->_loadFileContents('Response/testWasSuccessfulStatusCodeAndResponseAttribute.txt'));

        $this->_model->populate($successful_response);

        $this->assertTrue($this->_model->wasSuccessful());
    }

    /**
     * @test
     */
    public function testWasSuccessfulBadResponseAttribute() {
        $bad_response = Zend_Http_Response::fromString($this->_loadFileContents('Response/testWasSuccessfulBadResponseAttribute.txt'));

        $this->_model->populate($bad_response);

        $this->assertFalse($this->_model->wasSuccessful());
    }
}
