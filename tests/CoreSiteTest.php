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

    const SYSTEM_MESSAGES = ['A Message','Another Message','A third Message'];

    protected function _before()
    {
        $this->config = $GLOBALS['config'];
    }

    protected function _after()
    {
    }

    // tests
    public function testConfiguredUser()
    {
        $coreSite = $this->getCoreSiteInstance('TestUser',array("Test" => new PipitCore\Classes\CoreSitePage("Test","Test",SECURITY_PUBLIC)));
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
        $coreSite = $this->getCoreSiteInstance();
        $theMessage = self::SYSTEM_MESSAGES[0];
        $coreSite->addSystemMessage($theMessage);

        $this->assertEquals($theMessage,$coreSite->getSystemMessages()[0]->getMessage());

    }

    public function testAddingSystemError() {
        $coreSite = $this->getCoreSiteInstance();
        $coreSite->addSystemMessage(self::SYSTEM_MESSAGES[0],'error');

        $this->assertEquals('error',$coreSite->getSystemMessages()[0]->getType());

    }

    public function testGetSystemMessages() {
        $coreSite = $this->getCoreSiteInstance();
        $theMessages = self::SYSTEM_MESSAGES;
        foreach ($theMessages as $theMessage) {
            $coreSite->addSystemMessage($theMessage);
        }

        $this->assertEquals(count(self::SYSTEM_MESSAGES),count($coreSite->getSystemMessages()));

    }

    public function testGetDataRepository() {
        $coreSite = $this->getCoreSiteInstance('TestUser');
        $repositoryName = 'TestDataRepository';
        $this->assertEquals('TestFiles\Classes\Data\TestDataRepository',get_class($coreSite->getDataRepository($repositoryName)));
    }

    public function testGetHelper() {
        $coreSite = $this->getCoreSiteInstance('TestUser');
        $helperName = 'TestHelper';
        $this->assertTrue($coreSite->getHelper($helperName) instanceof \TestFiles\Classes\Helpers\TestHelper);
    }

    public function testRedirectUrl() {
        $redirectUrl = "redirect.here";
        $coreSite = $this->getCoreSiteInstance();
        $this->assertEquals($coreSite->hasRedirectUrl(),false);
        $coreSite->setRedirectUrl($redirectUrl);
        $this->assertEquals($coreSite->hasRedirectUrl(),true);
    }

    public function testViewRenderer() {
        $coreSite = $this->getCoreSiteInstance();
        $coreSite->setViewRenderer(new \Core\Classes\ViewRenderers\HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$coreSite->getSanitizedInputData(),'DefaultController'));
        $activeViewRenderer = $coreSite->getViewRenderer();
        $this->assertTrue($activeViewRenderer instanceof \Core\Classes\ViewRenderers\HTMLViewRenderer);
    }

    public function testGetSiteConfig() {
        $coreSite = $this->getCoreSiteInstance();
        $coreSiteConfig = $coreSite->getSiteConfig();
        $this->assertTrue(count($this->config) == count($coreSiteConfig));
    }

    /*
    public function testPages() {}

    public function testCurrentPage() {}
*/


    private function getCoreSiteInstance($userClass=null,$sitePages=array()) {
        if ($userClass) {
            $this->config['USER_CLASS'] = $userClass;
        } else {
            $this->config['USER_CLASS'] = 'TestUser';
        }
        $this->config['sitePages'] = $sitePages;
        return new PipitCore\Classes\CoreSite($this->config);
    }

}