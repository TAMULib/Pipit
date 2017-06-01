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
        $coreSite = $this->getCoreSiteInstance('TestUser',array("Test" => new PipitCore\Classes\CoreSitePage("Test","Test",SECURITY_PUBLIC)));

        $coreSite->setViewRenderer(new PipitCore\Classes\ViewRenderers\HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$this->config,null));
        // Try to load a controller that doesn't exist
        $this->assertEquals('TestFiles\Classes\Controllers\DefaultController',$coreSite->getControllerClass("Nonexistentcontroller"));
        // Try to load a controller that exists
        $this->assertEquals('TestFiles\Classes\Controllers\TestController',$coreSite->getControllerClass("Test"));
        // Intentionally load the default controller
        $this->assertEquals('TestFiles\Classes\Controllers\DefaultController',$coreSite->getControllerClass("DefaultController"));

    }

    public function testAdminControllerFetching() {
        // As an admin level user....
        $coreSite = $this->getCoreSiteInstance('TestAdminUser',array("Test" => new PipitCore\Classes\CoreSitePage("Test","Test",SECURITY_ADMIN)));

        $coreSite->setViewRenderer(new PipitCore\Classes\ViewRenderers\HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$this->config,null));

        // Try to load a controller that exists
        $this->assertEquals('TestFiles\Classes\Controllers\TestAdminController',$coreSite->getControllerClass("Test"));
    }

    public function testAddingSystemMessage() {
        $coreSite = $this->getCoreSiteInstance('TestUser');
        $theMessage = 'A Message';
        $coreSite->addSystemMessage($theMessage);

        $this->assertEquals($theMessage,$coreSite->getSystemMessages()[0]->getMessage());

    }

    public function testAddingSystemError() {
        $coreSite = $this->getCoreSiteInstance('TestUser');
        $theMessage = 'An Error Message';
        $coreSite->addSystemMessage($theMessage,'error');

        $this->assertEquals('error',$coreSite->getSystemMessages()[0]->getType());

    }

    private function getCoreSiteInstance($userClass='TestUser',$sitePages=array()) {
        $this->config['USER_CLASS'] = $userClass;

        $this->config['sitePages'] = $sitePages;
        return new PipitCore\Classes\CoreSite($this->config);
    }

}