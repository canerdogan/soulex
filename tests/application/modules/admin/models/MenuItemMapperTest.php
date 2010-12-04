<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of MenuItemMapperTest
 *
 * @author miholeus
 */
class Admin_Model_MenuItemMapperTest extends ControllerTestCase
{
    /**
     *
     * @var Admin_Model_MenuItem
     */
    private $_object;
    /**
     *
     * @var Admin_Model_MenuItemMapper
     */
    private $_objectMapper;

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new Admin_Model_MenuItem();
        $this->_object->setMenu_id(1)
            ->setLabel('testing label')
            ->setUri('/just-a-test.html')
            ->setPosition(100)
            ->setPublished(true)
            ->setLft(1)
            ->setRgt(2)
            ->setParent_id(0)
            ->setLevel(1);
        $objectMapper = new Admin_Model_MenuItemMapper();
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
        $label = 'foo';
        $this->_object->setLabel($label);
        // now we update object as object has its id
        $this->_objectMapper->save($this->_object);
        $updatedobject = $this->_objectMapper->findById($this->_object->getId());
        $this->assertEquals($label, $updatedobject->getLabel());
    }

    public function testFetchAll()
    {
        $objects = $this->_objectMapper->fetchAll();
        $this->assertEquals(1, count($objects));
        $this->assertType('Admin_Model_MenuItemCollection', $objects);
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
            $mapper = new Admin_Model_MenuItemMapper();
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
        $objectMapper = new Admin_Model_MenuItemMapper();
        $objectMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE menu_items');
    }
}
