<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * EventsControllerTest tests Events controller actions are working fine
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_EventsControllerTest extends ControllerTestCase
{
    protected $testData;
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
        $this->testData = array(
            'title' => 'testEvents',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'img_preview' => '',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        Admin_Fixture_User::destroy();
    }

    public function testListAllEvents()
    {
        $this->dispatch('/admin/events');
        $this->assertController('events');
        $this->assertAction('index');
    }

    public function testEventsCannotBeDeletedIfBoxcheckedIsNull()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0)
                ));
        $this->dispatch('/admin/events');
        $this->assertQueryContentContains('li',
                'Events deletion failed with the following error: Checksum is not correct');
    }

    public function testEventsCanBeDeleted()
    {
        $events = $this->_create();
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array($events->getId()),
                    'boxchecked' => 1
                ));
        $this->dispatch('/admin/events');
        $this->assertRedirectTo('/admin/events');
    }

    public function testEventsThrowExceptionOnNotExistingNews()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0),
                    'boxchecked' => 1
                ));
        $this->dispatch('/admin/events');
        $this->assertQueryContentContains('li',
                'Events deletion failed with the following error: '
                . 'Events by id 0 not found');
    }

    public function testUserCanSeeEditEventsForm()
    {
        $events = $this->_create();
        $this->dispatch('/admin/events/edit/id/' . $events->getId());
        $this->assertController('events');
        $this->assertAction('edit');
        // truncate table
        $this->_flushTable();

    }

    public function testUserCanSeeCreateEventsForm()
    {
        $this->dispatch('/admin/events/create');
        $this->assertController('events');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'Events Manager: Add Events');
    }

    public function testUserCanCreateEvents()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost($this->testData);
        $this->dispatch('/admin/events/create');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
    }

    public function testUserCanUpdateEvents()
    {
        $events = $this->_create();
        $data = $events->toArray();
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'id' => $data['id'],
                    'title' => $data['title'],
                    'short_description' => $data['short_description'],
                    'detail_description' => $data['detail_description'],
                    'published' => $data['published']
                ));
        $this->dispatch('/admin/events/edit');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
        $this->_flushTable();
    }

    private function _create()
    {
        $eventsMapper = new Admin_Model_EventsMapper();
        $events = new Admin_Model_Events($this->testData);
        $eventsMapper->save($events);
        return $events;
    }

    private function _flushTable()
    {
        $eventsMapper = new Admin_Model_EventsMapper();
        $eventsMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE events');
    }
}
