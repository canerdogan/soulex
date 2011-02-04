<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of ModelMapperTest
 *
 * @author miholeus
 */
class NewClassic_Models_Object_ModelMapperTest extends ControllerTestCase
{
    /**
     *
     * @var NewClassic_Models_Object_Model
     */
    protected $_object;
    /**
     *
     * @var NewClassic_Models_Object_ModelMapper
     */
    protected $_objectMapper;
    /**
     * Name of object class
     *
     * @var string
     */
    protected $_objectClass = 'NewClassic_Models_Object_Model';
    /**
     * Name of object's collection class
     *
     * @var string
     */
    protected $_objectCollectionClass = 'NewClassic_Models_Object_ModelCollection';
    /**
     * Name ob object mapper class
     *
     * @var string
     */
    protected $_objectMapperClass = 'NewClassic_Models_Object_ModelMapper';

    protected function prepareData()
    {
        // should prepare object data
        return;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new $this->_objectClass();
        $this->prepareData();
        $objectMapper = new $this->_objectMapperClass();
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
        $this->assertType($this->_objectCollectionClass, $objects);
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
            $this->_objectMapper->findById(1);
            $this->fail('Object with id 1 was not deleted');
        } catch (UnexpectedValueException $e) {
            // SUCCESS
            $this->assertTrue(true);
        }
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->_objectMapper->getDbTable()->getDefaultAdapter()
                ->query('TRUNCATE TABLE ' . $this->_objectMapper->getDbTable()->getName());
    }
}