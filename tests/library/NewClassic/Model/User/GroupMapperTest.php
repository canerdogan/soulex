<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of GroupMapperTest
 *
 * @author miholeus
 */
class NewClassic_Models_User_GroupMapperTest extends ControllerTestCase
{
    /**
     *
     * @var NewClassic_Model_User_Group
     */
    protected $_object;
    /**
     *
     * @var NewClassic_Model_User_GroupMapper
     */
    protected $_objectMapper;
    /**
     * Name of object class
     *
     * @var string
     */
    protected $_objectClass = 'NewClassic_Model_User_Group';
    /**
     * Name of object's collection class
     *
     * @var string
     */
    protected $_objectCollectionClass = 'NewClassic_Model_User_GroupCollection';
    /**
     * Name ob object mapper class
     *
     * @var string
     */
    protected $_objectMapperClass = 'NewClassic_Model_User_GroupMapper';

    protected function prepareData()
    {
        $fixture = FixtureLoader::getInstance();
        $data = $fixture->load('NewClassic/UserGroup');
        $this->_object->setOptions($data);
    }

    protected function setUp()
    {
        parent::setUp();
        try {
            $this->_object = new $this->_objectClass();
            $this->prepareData();
            $objectMapper = new $this->_objectMapperClass();
            $objectMapper->save($this->_object);
            $this->_objectMapper = $objectMapper;
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function testSave()
    {
        // object was created, now we have id
        $this->assertEquals(1, $this->_object->getId());
        $this->assertEquals(1, $this->_objectMapper->getDbTable()->fetchAll()->count());
    }

    public function testUpdate()
    {
        $name = 'foo';
        $this->_object->setName($name);
        // now we update object as object has its id
        $this->_objectMapper->save($this->_object);
        $updatedobject = $this->_objectMapper->findById($this->_object->getId());
        $this->assertEquals($name, $updatedobject->getName());
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
        $this->_objectMapper->getDbTable()->getAdapter()
                ->query('TRUNCATE TABLE ' . $this->_objectMapper->getDbTable()->getName());
    }
}