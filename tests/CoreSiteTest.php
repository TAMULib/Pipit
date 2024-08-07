<?php
namespace Tests;
use TestFiles;
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Site\CoreSite;
use Pipit\Classes\Site\CoreSitePage;
use Pipit\Classes\ViewRenderers\HTMLViewRenderer;


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
        $this->config = CoreFunctions::getInstance()->getAppConfiguration();
    }

    protected function _after()
    {
    }

    public function testConfiguredUser()
    {
        $coreSite = $this->getCoreSiteInstance();
        $this->assertEquals('TestFiles\Classes\Data\TestUser',get_class($coreSite->getGlobalUser()));
    }

    public function testConfiguredCasUser()
    {
        $this->config['CAS_USER_REPO'] = 'TestDataRepository';
        $coreSite = $this->getCoreSiteInstance('TestUserCAS');
        $this->assertEquals('TestFiles\Classes\Data\TestUserCAS',get_class($coreSite->getGlobalUser()));
    }

    public function testConfiguredCasUserWithNoRepository()
    {
        $this->config['USECAS'] = true;
        $this->config['CAS_USER_REPO'] = null;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("UserCAS requires CAS_USER_REPO to be defined with a Pipit\Interfaces\DataRepository");

        $coreSite = $this->getCoreSiteInstance('TestUserCAS');
    }

    public function testDefaultCasUser()
    {
        $this->config['USE_CAS'] = true;
        //We will fail to connect to a non-existent database, but we're only testing that the right class is set for the globalUser
        $this->expectException(\PDOException::class);
        $coreSite = $this->getDefaultCoreSiteInstance();
        $this->assertEquals('Pipit\Classes\Data\UserCAS',get_class($coreSite->getGlobalUser()));
    }

    public function testConfiguredSamlUser()
    {
        $this->config['SAML_USER_REPO'] = 'TestDataRepository';
        $coreSite = $this->getCoreSiteInstance('TestUserSAML');
        $this->assertEquals('TestFiles\Classes\Data\TestUserSAML',get_class($coreSite->getGlobalUser()));
    }

    public function testConfiguredSamlUserWithNoRepository()
    {
        $this->config['USESAML'] = true;
        $this->config['SAML_USER_REPO'] = null;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("UserSAML requires SAML_USER_REPO to be defined with a Pipit\Interfaces\DataRepository");

        $coreSite = $this->getCoreSiteInstance('TestUserSAML');
    }

    public function testDefaultSamlUser()
    {
        $this->config['USE_SAML'] = true;
        //We will fail to connect to a non-existent database, but we're only testing that the right class is set for the globalUser
        $this->expectException(\PDOException::class);
        $coreSite = $this->getDefaultCoreSiteInstance();
        $this->assertEquals('Pipit\Classes\Data\UserSAML',get_class($coreSite->getGlobalUser()));
    }

    public function testDefaultUser()
    {
        //We will fail to connect to a non-existent database, but we're only testing that the right class is set for the globalUser
        $this->expectException(\PDOException::class);
        $coreSite = $this->getDefaultCoreSiteInstance();
        $this->assertEquals('Pipit\Classes\Data\UserDB',get_class($coreSite->getGlobalUser()));
    }

    public function testControllerFetching() {
        $coreSite = $this->getCoreSiteInstance();

        $coreSite->setViewRenderer(new HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$this->config,null));
        // Try to load a controller that doesn't exist
        $this->assertEquals('TestFiles\Classes\Controllers\DefaultController',$coreSite->getControllerClass("Nonexistentcontroller"));
        // Try to load a controller that exists
        $this->assertEquals('TestFiles\Classes\Controllers\TestController',$coreSite->getControllerClass("Test"));
        // Intentionally load the default controller
        $this->assertEquals('TestFiles\Classes\Controllers\DefaultController',$coreSite->getControllerClass("DefaultController"));

    }

    public function testAdminControllerFetching() {
        // As an admin level user....
        $coreSite = $this->getCoreSiteInstance('TestAdminUser');

        $coreSite->setViewRenderer(new HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$this->config,null));
        // Try to load a controller that exists
        $this->assertEquals('TestFiles\Classes\Controllers\SecureTestAdminController',$coreSite->getControllerClass("SecureTest"));
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
        $coreSite = $this->getCoreSiteInstance();
        $repositoryName = 'TestDataRepository';
        $this->assertEquals('TestFiles\Classes\Data\TestDataRepository',get_class($coreSite->getDataRepository($repositoryName)));
    }

    public function testGetHelper() {
        $coreSite = $this->getCoreSiteInstance();
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
        $coreSite->setViewRenderer(new HTMLViewRenderer($coreSite->getGlobalUser(),$coreSite->getPages(),$coreSite->getSanitizedInputData(),'DefaultController'));
        $activeViewRenderer = $coreSite->getViewRenderer();
        $this->assertTrue($activeViewRenderer instanceof HTMLViewRenderer);
    }

    public function testGetSiteConfig() {
        $coreSite = $this->getCoreSiteInstance();
        $coreSiteConfig = $coreSite->getSiteConfig();
        $this->assertTrue(count($this->config) == count($coreSiteConfig));
    }

    public function testPages() {
        $coreSite = $this->getCoreSiteInstance();
        $this->assertTrue(count($coreSite->getPages()) > 0);
    }

    public function testCurrentPage() {
        $coreSite = $this->getCoreSiteInstance();
        $testPage = current($coreSite->getPages());
        $this->assertTrue($coreSite->getCurrentPage()==null);
        $coreSite->setCurrentPage($testPage);
        $this->assertTrue($coreSite->getCurrentPage()==$testPage);
    }

    private function getCoreSiteInstance($userClass=null) {
        if ($userClass) {
            $this->config['USER_CLASS'] = $userClass;
        } else {
            $this->config['USER_CLASS'] = 'TestUser';
        }
        return new CoreSite($this->config);
    }

    private function getDefaultCoreSiteInstance() {
        return new CoreSite($this->config);
    }
}