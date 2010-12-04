<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of MenuMapperTest
 *
 * @author miholeus
 */
class Admin_Model_MenuMapperTest extends ControllerTestCase
{
    /**
     *
     * @var Admin_Model_Menu
     */
    private $_object;
    /**
     *
     * @var Admin_Model_MenuMapper
     */
    private $_objectMapper;

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new Admin_Model_Menu();
        $this->_object->setTitle('test_title')
            ->setMenutype('menuleft')
            ->setDescription('test description');
        $objectMapper = new Admin_Model_MenuMapper();
        $objectMapper->save($this->_object);
        $this->_objectMapper = $objectMapper;
    }

    public function testSave()
    {
        // object was created, now we have id
        $this->assertEquals(1, $this->_object->getId());
        $this->assertEquals(1, $this->_objectMapper->getDbTable()->fetchAll()->count());
    }

    public function testUpdate()
    {
        $title = 'foo';
        $this->_object->setTitle($title);
        // now we update object as object has its id
        $this->_objectMapper->save($this->_object);
        $updatedobject = $this->_objectMapper->findById($this->_object->getId());
        $this->assertEquals($title, $updatedobject->getTitle());
    }

    public function testFetchAll()
    {
        $objects = $this->_objectMapper->fetchAll();
        $this->assertEquals(1, count($objects));
        $this->assertType('Admin_Model_MenuCollection', $objects);
    }

    public function testFindByIdThrowsExceptionOnNotExistingObject()
    {
        try {
            $this->_objectMapper->findById(99);
            $this->fail('findById should throw exception if no object exists');
        } catch (UnexpectedValueException $e) {
            // SUCCESS
            $this->assertTrue(true);
        }
    }

    public function testDelete()
    {
        $this->_objectMapper->delete(1);
        unset($this->_objectMapper);
        try {
            $mapper = new Admin_Model_MenuMapper();
            $mapper->findById(1);
            $this->fail('Menu with id 1 was not deleted');
        } catch (UnexpectedValueException $e) {
            // SUCCESS
            $this->assertTrue(true);
        }
    }

    protected function tearDown()
    {
        parent::tearDown();
        $objectMapper = new Admin_Model_MenuMapper();
        $objectMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE menus');
    }
}
