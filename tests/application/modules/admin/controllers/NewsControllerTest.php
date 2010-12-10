<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * NewsControllerTest tests news controller actions are working fine
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_NewsControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    protected function tearDown()
    {
        parent::tearDown();
        Admin_Fixture_User::destroy();
    }

    public function testListAllNews()
    {
        $this->dispatch('/admin/news');
        $this->assertController('news');
        $this->assertAction('index');
    }

    public function testNewsCannotBeDeletedIfBoxcheckedIsNull()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0)
                ));
        $this->dispatch('/admin/news');
        $this->assertQueryContentContains('li',
                'News deletion failed with the following error: Checksum is not correct');
    }

    public function testNewsCanBeDeleted()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0),
                    'boxchecked' => 1
                ));
        $this->dispatch('/admin/news');
        $this->assertRedirectTo('/admin/news');
    }

    public function testUserCanSeeEditNewsForm()
    {
        $newsMapper = new Admin_Model_NewsMapper();
        $mdlNews = new Admin_Model_News(array(
            'title' => 'test title',
            'short_description' => 'test description',
            'detail_description' => 'detail description',
            'published' => '1',
            'published_at' => date("Y-m-d H:i:s")
        ));
        $newsMapper->save($mdlNews);
        $this->dispatch('/admin/news/edit/id/' . $mdlNews->getId());
        $this->assertController('news');
        $this->assertAction('edit');
        $newsMapper->delete($mdlNews->getId());
    }

    public function testUserCanSeeCreateNewsForm()
    {
        $this->dispatch('/admin/news/create');
        $this->assertController('news');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'News Manager: Add News');
    }

    public function testUserCanCreateNews()
    {
        $testNews = array(
            'id' => '',
            'title' => 'testNews',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testNews);
        $this->dispatch('/admin/news/create');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
        $newsMapper = new Admin_Model_NewsMapper();
        $newsMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE news');
    }

    public function testUserCanUpdateNews()
    {
        // create news
        $newsMapper = new Admin_Model_NewsMapper();
        $mdlNews = new Admin_Model_News(array(
            'title' => 'test title',
            'short_description' => 'test description',
            'detail_description' => 'detail description',
            'published' => '1',
            'published_at' => date("Y-m-d H:i:s")
        ));
        $newsMapper->save($mdlNews);
        // update news
        $testNews = array(
            'id' => $mdlNews->getId(),
            'title' => 'testNews',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testNews);
        $this->dispatch('/admin/news/edit');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
        $newsMapper = new Admin_Model_NewsMapper();
        $newsMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE news');
    }
}
