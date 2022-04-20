<?php
namespace Tests;
use TestFiles;
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Site\CoreSite;
use Pipit\Classes\Site\CoreSitePage;
use Pipit\Classes\ViewRenderers\HTMLViewRenderer;

class TraitFileConfigurationTest extends \Codeception\Test\Unit {
    public function testGetConfigurationFromFileName() {
        $mockInstance = $this->getObjectForTrait('Pipit\Traits\FileConfiguration');
        $config = $this->invokeMethod($mockInstance, 'getConfigurationFromFileName', ['fileName'=>'test.templated']);

        $this->assertEquals('value1',$config['testKey1']);
        $this->assertEquals('value2',$config['testKey2']);
        $this->assertEquals('contains value1',$config['testTemplateKey1']);
        $this->assertEquals('contains value2',$config['testTemplateKey2'],);
        $this->assertEquals('contains value1 and value2',$config['testTemplateKeyAll']);
        $this->assertEquals('section value contains value1',$config['section']['testSectionKey1']);
        $this->assertEquals('regular value',$config['section2']['test']['deepsection1']);
        $this->assertEquals('deep templated value2',$config['section2']['test']['deepsection2']);
    }

    public function testMissingConfigurationFileThrowsConfigurationException() {
        $this->expectException('Pipit\Classes\Exceptions\ConfigurationException');
        $mockInstance = $this->getObjectForTrait('Pipit\Traits\FileConfiguration');
        $this->invokeMethod($mockInstance, 'getConfigurationFromFileName', ['fileName'=>'nonexistent.config']);
    }

    protected function invokeMethod(&$object, $methodName, $parameters=[]) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
