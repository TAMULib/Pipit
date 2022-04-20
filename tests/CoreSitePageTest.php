<?php
namespace Tests;
use TestFiles;
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Site\CoreSitePage;


class CoreSitePageTest extends \Codeception\Test\Unit
{
    /**
     * @var \Tests\
     */
    protected $tester;

    protected $config;

    protected $coreSitePages;

    protected function _before()
    {
        $this->config = CoreFunctions::getInstance()->getAppConfiguration();
        $this->coreSitePages = [
                "widgets" => new CoreSitePage("widgets","widgets",SECURITY_USER),
                "DynamicRepo" => new CoreSitePage("dynamic repo","dynamic-repo",SECURITY_PUBLIC),
                "files" => new CoreSitePage("File Manager","files",SECURITY_USER),
                "users" => new CoreSitePage("users","users",SECURITY_ADMIN)];
    }

    public function testGetAccessLevel() {
        $testPage = $this->coreSitePages['widgets'];
        $this->assertEquals($testPage->getAccessLevel(), SECURITY_USER);
        $testPage = $this->coreSitePages['DynamicRepo'];
        $this->assertEquals($testPage->getAccessLevel(), SECURITY_PUBLIC);
        $testPage = $this->coreSitePages['users'];
        $this->assertEquals($testPage->getAccessLevel(), SECURITY_ADMIN);
    }

    public function testSetAccessLevel() {
        $testPage = $this->coreSitePages['widgets'];
        $this->assertEquals($testPage->getAccessLevel(), SECURITY_USER);
        $testPage->setAccessLevel(SECURITY_PUBLIC);
        $this->assertEquals($testPage->getAccessLevel(), SECURITY_PUBLIC);
    }

    public function testGetName() {
        $testPage = $this->coreSitePages['widgets'];
        $this->assertEquals($testPage->getName(),'widgets');
    }

    public function testSetName() {
        $testPage = $this->coreSitePages['widgets'];
        $newName = "New Name";
        $this->assertEquals($testPage->getName(),'widgets');
        $testPage->setName($newName);
        $this->assertEquals($testPage->getName(),$newName);
    }

    public function testGetPath() {
        $testPage = $this->coreSitePages['widgets'];
        $this->assertEquals($testPage->getPath(),'widgets');
    }

    public function testSetPath() {
        $testPage = $this->coreSitePages['widgets'];
        $newPath = "new-path";
        $this->assertEquals($testPage->getName(),'widgets');
        $testPage->setPath($newPath);
        $this->assertEquals($testPage->getPath(),$newPath);
    }

    public function testTitle() {
        $testPage = $this->coreSitePages['widgets'];
        $testTitle = 'Test Title';
        $testPage->setTitle($testTitle);
        $this->assertEquals($testPage->getTitle(),$testTitle);
    }

    public function testSubTitle() {
        $testPage = $this->coreSitePages['widgets'];
        $testSubTitle = 'Test Subtitle';
        $testPage->setSubTitle($testSubTitle);
        $this->assertEquals($testPage->getSubTitle(),$testSubTitle);
    }

    public function testGetOptions() {
        $testPage = $this->coreSitePages['widgets'];
        $testPage->setOptions([
            ["name"=>"list"],
            ["name"=>"add","action"=>"add","modal"=>true]
        ]);
        $options = $testPage->getOptions();
        $this->assertTrue(count($options) == 2);
        $this->assertEquals($options[0]['name'], "list");
        $this->assertEquals($options[1]['name'], "add");
    }

    public function testIsSearchable() {
        $testPage = $this->coreSitePages['widgets'];
        $this->assertTrue(!$testPage->isSearchable());
        $testPage->setIsSearchable(true);
        $this->assertTrue($testPage->isSearchable());
    }

    public function testSearchableFields() {
        $testPage = $this->coreSitePages['widgets'];
        $this->assertTrue(is_array($testPage->getSearchableFields()));
        $this->assertEquals(count($testPage->getSearchableFields()),0);
        $testPage->setSearchableFields([
                                        ["name"=>"name_last","type"=>"text"],
                                        ["name"=>"name_first","type"=>"text"]
                                        ]);
        $this->assertEquals(count($testPage->getSearchableFields()),2);
        $this->assertEquals($testPage->getSearchableFields()[1]['name'],"name_first");
    }

    public function testIsAdminPage() {
        $testPage = $this->coreSitePages['users'];
        $this->assertTrue($testPage->isAdminPage());
        $testPage = $this->coreSitePages['widgets'];
        $this->assertFalse($testPage->isAdminPage());
    }

    public function testIsPublicPage() {
        $testPage = $this->coreSitePages['DynamicRepo'];
        $this->assertTrue($testPage->isPublicPage());
        $testPage = $this->coreSitePages['widgets'];
        $this->assertFalse($testPage->isPublicPage());
    }
}
