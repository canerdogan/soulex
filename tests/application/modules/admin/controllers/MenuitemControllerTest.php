<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of MenuitemControllerTest
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_MenuitemControllerTest extends ControllerTestCase
{
    protected $testData;
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
        $this->testData = array(
            'label' => 'test_label',
            'uri' => '/testing.html',
            'position' => '100',
            'parent_id' => '0', // menu root
            'published' => '0',
            'menu_id' => '5' // location: menu top
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        Admin_Fixture_User::destroy();
    }

    public function testMenuitemsCanBeDisplayed()
    {
        $this->dispatch('/admin/menuitem');
        $this->assertController('menuitem');
        $this->assertAction('index');
    }

    public function testDeleteMenuItem()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0),
                    'boxchecked' => '1'
                ));
        $this->dispatch('/admin/menuitem');
        $this->assertRedirectTo('/admin/menuitem');
    }

    public function testCreateFormCanBeDisplayed()
    {
        $this->dispatch('/admin/menuitem/create');
        $this->assertController('menuitem');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'Menu Manager: Add New Menu Item');
    }

    public function testMenuitemCanBeCreated()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost($this->testData);
        $this->dispatch('/admin/menuitem/create');
        $this->assertRedirectTo('/admin/menuitem');
    }

    public function testEditFormCanBeDisplayed()
    {
        $item = $this->_create();
        $this->dispatch('/admin/menuitem/edit/id/' . $item->getId());
        $this->assertController('menuitem');
        $this->assertAction('edit');
        $this->assertQueryContentContains('h2', 'Menu Manager: Edit Menu Item');
        $this->_flushTable();
    }

    public function testShowErrorOnNotExistingMenuitem()
    {
        $this->dispatch('/admin/menuitem/edit/id/0');
        $this->assertQueryContentContains('li', 'MenuItem by id 0 not found');
        $this->assertAction('edit');
    }

    public function testMenuitemCanBeEdited()
    {
        $item = $this->_create();
        $data = $this->testData;
        $data['id'] = $item->getId();
        $this->getRequest()->setMethod('POST')
                ->setPost($data);
        $this->dispatch('/admin/menuitem/edit');
        $this->assertRedirectTo('/admin/menuitem');
        $this->_flushTable();
    }

    public function testMenuitemCanBeDeletedWithUrlParams()
    {
        $this->dispatch('/admin/menuitem/delete/id/0');
        $this->assertRedirectTo('/admin/menuitem');
    }

    private function _create()
    {
        $objectMapper = new Admin_Model_MenuItemMapper();
        $object = new Admin_Model_MenuItem($this->testData);
        $objectMapper->save($object);
        return $object;
    }

    private function _flushTable()
    {
        $eventsMapper = new Admin_Model_MenuItemMapper();
        $eventsMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE menu_items');
    }
}
