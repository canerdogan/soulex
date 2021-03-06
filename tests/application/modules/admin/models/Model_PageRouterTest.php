<?php
require_once 'PHPUnit/Framework.php';
require_once 'Zend/Exception.php';

require_once dirname(__FILE__).'/../../../../../application/modules/admin/models/PageRouter.php';

/**
 * Test class for Model_PageRouter.
 * Generated by PHPUnit on 2010-05-04 at 14:08:58.
 */
class Admin_Model_PageRouterTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Model_PageRouter
     * @access protected
     */
    protected $object;
    /**
     * Routes test path
     *
     * @var string
     */
    protected $_routesPath;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
        $this->_routesPath = dirname(__FILE__) . '/../../../configs/routes.test.ini';
//        $this->object = Admin_Model_PageRouter::getInstance($this->_routesPath);
        /**
         * we use constructor because bootstrap process defines main routes file
         */
        $this->object = new Admin_Model_PageRouter($this->_routesPath);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     * testGetInstance().
     */
    public function testGetInstance() {
        $this->assertClassHasStaticAttribute('_router', 'Admin_Model_PageRouter');
    }

    /**
     * @todo remove require_once calls, find some solution!
     */
    public function testCreateRoute() {

        require_once 'Zend/Config/Ini.php';
        require_once 'Zend/Config/Writer/Ini.php';

        $pageId = 1;
        $uri = 'foobar.html';
        $this->object->createRoute(1, $uri);

        $config = new Zend_Config_Ini($this->_routesPath);

        $this->assertEquals($uri, $config->production->routes->foobar->route);
        $this->assertEquals('frontend', $config->production->routes->foobar->defaults->module);
        $this->assertEquals('page', $config->production->routes->foobar->defaults->controller);
        $this->assertEquals('open', $config->production->routes->foobar->defaults->action);
        $this->assertEquals($pageId, $config->production->routes->foobar->defaults->id);

        $this->object->deleteRoute($uri);

    }

    /**
     * testUpdateRoute().
     */
    public function testUpdateRoute() {
        $pageId = 1;
        $oldUri = 'foobar.html';
        $newUri = 'onetwoo.html';
        $this->object->createRoute(1, $oldUri);

        $this->object->updateRoute($pageId, $newUri, $oldUri);

        $config = new Zend_Config_Ini($this->_routesPath);

        $this->assertEquals($newUri, $config->production->routes->onetwoo->route);
        $this->assertEquals('page', $config->production->routes->onetwoo->defaults->controller);
        $this->assertEquals('open', $config->production->routes->onetwoo->defaults->action);
        $this->assertEquals($pageId, $config->production->routes->onetwoo->defaults->id);

        $this->object->deleteRoute($newUri);
    }

    /**
     * testDeleteRoute().
     */
    public function testDeleteRoute() {

        $pageId = 1;
        $uri = 'foobar.html';

        $this->object->createRoute($pageId, $uri);
        $this->object->deleteRoute($uri);

        $config = new Zend_Config_Ini($this->_routesPath);
        $this->assertNull($config->production->foobar);
    }
}
?>
