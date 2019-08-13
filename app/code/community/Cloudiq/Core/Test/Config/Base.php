<?php
class Cloudiq_Core_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config {
    /**
     * @test
     */
    public function testBasicConfiguration() {
        $this->assertModuleCodePool('community');
        $this->assertModuleVersion('1.0.0');
    }

    /**
     * @test
     */
    public function testClassAliases() {
        $this->assertHelperAlias('cloudiq_core', 'Cloudiq_Core_Helper_Data');
        $this->assertModelAlias('cloudiq_core/test', 'Cloudiq_Core_Model_Test');
        $this->assertBlockAlias('cloudiq_core/test', 'Cloudiq_Core_Block_Test');
    }

    /**
     * @test
     */
    public function testDataHelperExists() {
        $this->assertInstanceOf('Cloudiq_Core_Helper_Data', Mage::helper('cloudiq_core'));
    }
}
