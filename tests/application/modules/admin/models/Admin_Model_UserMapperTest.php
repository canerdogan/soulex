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
    private $_user;
    /**
     *
     * @var Admin_Model_UserMapper
     */
    private $_userMapper;

    protected function setUp()
    {
        parent::setUp();
        $this->_user = new Admin_Model_User();
        $this->_user->setUsername('miholeus')
            ->setEmail('me@miholeus.com')
            ->setPassword('123')
            ->setFirstname('miholeus')
            ->setLastname('webmaster')
            ->setEnabled(true)
            ->setRole('administrator');
        $userMapper = new Admin_Model_UserMapper();
        $userMapper->save($this->_user);
        $this->_userMapper = $userMapper;
    }
    
    public function testSave()
    {
        // user was created, now we have id
        $this->assertEquals(1, $this->_user->getId());
        $this->assertEquals(1, $this->_userMapper->getDbTable()->fetchAll()->count());
    }

    public function testUpdate()
    {
        $firstname = 'foo';
        $this->_user->setFirstname($firstname);
        // now we update user as user has its id
        $this->_userMapper->save($this->_user);
        $updatedUser = $this->_userMapper->findById($this->_user->getId());
        $this->assertEquals($firstname, $updatedUser->getFirstname());
    }

    public function testFindById()
    {
        $user = $this->_userMapper->findById(1);
        $this->assertEquals(1, $user->getId());
    }

    public function testFindByIdThrowsExceptionOnNotExistingUser()
    {
        try {
            $this->_userMapper->findById(99);
            $this->fail('findById should throw exception if no user exists');
        } catch (UnexpectedValueException $e) {
            // SUCCESS
            $this->assertTrue(true);
        }
    }

    public function testFetchAll()
    {
        $users = $this->_userMapper->fetchAll();
        $this->assertEquals(1, count($users));
        $this->assertType('Admin_Model_UserCollection', $users);
    }

    public function testDelete()
    {
        $this->_userMapper->delete(1);
        unset($this->_userMapper);
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
        $user = new Admin_Model_User();
        $user->setUsername('superman')
            ->setEmail('superman@miholeus.com')
            ->setPassword('12345')
            ->setFirstname('superman')
            ->setLastname('webmaster')
            ->setEnabled(false)
            ->setRole('guest');
        $this->_userMapper->save($user);
        $userCollection = $this->_userMapper->fetchAll();
        $ids = array();
        foreach($userCollection as $key => $singleUser) {
            $ids[] = $singleUser->getId();
        }
        $this->_userMapper->deleteBulk($ids);
        unset($this->_userMapper);
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
        $userMapper = new Admin_Model_UserMapper();
        $userMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE users');
    }
}