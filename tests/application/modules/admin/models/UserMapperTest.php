<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of Admin_Model_UserMapperTest
 *
 * @author miholeus
 */
class Admin_Model_UserMapperTest extends ControllerTestCase
{
    /**
     *
     * @var Admin_Model_User
     */
    private $_object;
    /**
     *
     * @var Admin_Model_UserMapper
     */
    private $_objectMapper;

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new Admin_Model_User();
        $this->_object->setUsername('miholeus')
            ->setEmail('me@miholeus.com')
            ->setPassword('123')
            ->setFirstname('miholeus')
            ->setLastname('webmaster')
            ->setEnabled(true)
            ->setRole('administrator');
        $objectMapper = new Admin_Model_UserMapper();
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
        $firstname = 'foo';
        $this->_object->setFirstname($firstname);
        // now we update object as object has its id
        $this->_objectMapper->save($this->_object);
        $updatedobject = $this->_objectMapper->findById($this->_object->getId());
        $this->assertEquals($firstname, $updatedobject->getFirstname());
    }

    public function testFindById()
    {
        $object = $this->_objectMapper->findById(1);
        $this->assertEquals(1, $object->getId());
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

    public function testFetchAll()
    {
        $objects = $this->_objectMapper->fetchAll();
        $this->assertEquals(1, count($objects));
        $this->assertType('Admin_Model_UserCollection', $objects);
    }

    public function testDelete()
    {
        $this->_objectMapper->delete(1);
        unset($this->_objectMapper);
        try {
            $mapper = new Admin_Model_UserMapper();
            $mapper->findById(1);
            $this->fail('User with id 1 was not deleted');
        } catch (UnexpectedValueException $e) {
            // SUCCESS
            $this->assertTrue(true);
        }
    }

    public function testDeleteBulk()
    {
        // create 1 more user
        $object = new Admin_Model_User();
        $object->setUsername('superman')
            ->setEmail('superman@miholeus.com')
            ->setPassword('12345')
            ->setFirstname('superman')
            ->setLastname('webmaster')
            ->setEnabled(false)
            ->setRole('guest');
        $this->_objectMapper->save($object);
        $objectCollection = $this->_objectMapper->fetchAll();
        $ids = array();
        foreach($objectCollection as $key => $singleObject) {
            $ids[] = $singleObject->getId();
        }
        $this->_objectMapper->deleteBulk($ids);
        unset($this->_objectMapper);
        $this->markTestSkipped();

//        $mapper = new Admin_Model_UserMapper();
//        try {
//            $user = $mapper->findById(1);
//            var_dump($user);
//            $this->fail("User 1 was not deleted");
//        } catch (UnexpectedValueException $e) {
//            // SUCCESS
//            $this->assertTrue(true);
//        }
//
//        try {
//            $mapper->findById(2);
//            $this->fail("User 2 was not deleted");
//        } catch (UnexpectedValueException $e) {
//            // SUCCESS
//            $this->assertTrue(true);
//        }
    }

    protected function tearDown()
    {
        parent::tearDown();
        $objectMapper = new Admin_Model_UserMapper();
        $objectMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE users');
    }
}