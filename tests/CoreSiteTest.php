<?php
namespace Tests;
use TestFiles;
use Core as PipitCore;


class CoreSiteTest extends \Codeception\Test\Unit
{
    /**
     * @var \Tests\
     */
    protected $tester;

    protected $config;

    protected function _before()
    {
        $this->config = get_defined_constants(true)["user"];
    }

    protected function _after()
    {
    }

    // tests
    public function testConfiguredUser()
    {
        $this->config['USER_CLASS'] = 'TestUser';
        $coreSite = new PipitCore\Classes\CoreSite($this->config);
        $this->assertEquals('TestFiles\Classes\Data\TestUser',get_class($coreSite->getGlobalUser()));
    }

    public function testControllerFetching() {
        $this->config['USER_CLASS'] = 'TestUser';

        $this->config['sitePages'] = array("Test" => new PipitCore\Classes\CoreSitePage("Test","Test",SECURITY_PUBLIC));
        $coreSite = new PipitCore\Classes\CoreSite($this->config);
        $coreSite->setViewRenderer(new PipitCore\Classes\ViewRenderers\HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$this->config,null));

        // Try to load a controller that doesn't exist
        $this->assertEquals('TestFiles\Classes\Controllers\DefaultController',$coreSite->getControllerClass("Nonexistentcontroller"));
        // Try to load a controller that exists
        $this->assertEquals('TestFiles\Classes\Controllers\TestController',$coreSite->getControllerClass("Test"));        
    }
}