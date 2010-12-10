<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * MenuControllerTest test user can view/create/update/delete menus
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_MenuControllerTest extends ControllerTestCase
{
    protected $testData;
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
        $this->testData = array(
            'title' => 'test_title',
            'menutype' => 'menutype' . rand(0, 1000000),
            'description' => 'test_description'
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        Admin_Fixture_User::destroy();
    }

    public function testMenusCanBeDisplayed()
    {
        $this->dispatch('/admin/menu');
        $this->assertController('menu');
        $this->assertAction('index');
    }

    public function testMenusCreationFormIsDisplayed()
    {
        $this->dispatch('/admin/menu/create');
        $this->assertController('menu');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'Menu Manager: Add New Menu');
    }

    public function testMenusCanBeCreated()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost($this->testData);
        $this->dispatch('/admin/menu/create');
        $this->assertRedirectTo('/admin/menu');
        $this->_flushTable();
    }

    public function testMenusEditFormIsDisplayed()
    {
        $menu = $this->_create();
        $this->dispatch('/admin/menu/edit/id/' . $menu->getId());
        $this->assertController('menu');
        $this->assertAction('edit');
        $this->assertQueryContentContains('h2', 'Menu Manager: Edit Menu');
        $this->_flushTable();
    }


    public function testMenusCanBeDeletedWithUrlParams()
    {
        $this->dispatch('/admin/menu/delete/id/0');
        $this->assertRedirectTo('/admin/menu');
    }

    public function testMenusCanBeEdited()
    {
        $menu = $this->_create();
        $testData = array(
            'title' => $menu->getTitle(),
            'menutype' => $menu->getMenutype(),
            'description' => $menu->getDescription(),
            'id' => $menu->getId()
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testData);
        $this->dispatch('/admin/menu/edit/');
        /**
         * test User is forwarded to index action
         */
        $this->assertAction('index');
        $this->_flushTable();
    }
    
    private function _create()
    {
        $objectMapper = new Admin_Model_MenuMapper();
        $object = new Admin_Model_Menu($this->testData);
        $objectMapper->save($object);
        return $object;
    }

    private function _flushTable()
    {
        $eventsMapper = new Admin_Model_MenuMapper();
        $eventsMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE menus');
    }
}
