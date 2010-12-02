<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Tests set/get methods of Admin_Model_User class,
 * also toArray() method and setOptions() method
 *
 * @author miholeus
 */
class Admin_Model_UserTest extends ControllerTestCase
{
    protected $data;
    protected function setUp()
    {
        parent::setUp();
        $this->data = array(
            'id' => 2,
            'username' => 'miholeus',
            'email' => 'me@miholeus.com',
            'password' => '123',
            'firstname' => 'miholeus',
            'lastname' => 'webmaster',
            'enabled' => true,
            'registerDate' => '2010-10-19 17:15:00',
            'lastvisitDate' => '2010-11-19 18:12:23',
            'role' => 'administrator'
        );
    }

    public function testAccessorsAndMutators()
    {
        $object = new Admin_Model_User();

        foreach(array_keys($this->data) as $prop) {
            $getPropName = "get" . ucfirst($prop);
            $setPropName = "set" . ucfirst($prop);
            $this->assertNull($object->$getPropName());
            // setting property value
            $object->$setPropName($this->data[$prop]);
            $this->assertEquals($this->data[$prop], $object->$getPropName());
        }
    }

    public function testBadGetSetMethodsThrowExceptions()
    {
        $object = new Admin_Model_User();
        try {
            $this->assertNull($object->getFoo());
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Getting property error(.*)/', $e->getMessage());
        }

        try {
            $this->assertNull($object->setFoo("bar"));
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Setting property error(.*)/', $e->getMessage());
        }
    }

    public function testIdOnlySetOnce()
    {
        $object = new Admin_Model_User();
        $object->setId(2);
        $this->assertEquals(2, $object->getId());
        $object->setId(10);
        $this->assertEquals(2, $object->getId());
    }
    /**
     * If chaining is not supported you'll get fatall error
     */
    public function testSettersCanGenerateChainCalss()
    {
        $object = new Admin_Model_User();

        $object->setUsername($this->data['username'])
            ->setEmail($this->data['email'])
            ->setPassword($this->data['password'])
            ->setFirstname($this->data['firstname'])
            ->setLastname($this->data['lastname'])
            ->setEnabled($this->data['enabled'])
            ->setRegisterDate($this->data['registerDate'])
            ->setLastvisitDate($this->data['lastvisitDate'])
            ->setRole($this->data['role']);
        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }

    public function testToArrayIsCorrect()
    {
        $object = new Admin_Model_User();
        $object->setId($this->data['id'])
            ->setUsername($this->data['username'])
            ->setEmail($this->data['email'])
            ->setPassword($this->data['password'])
            ->setFirstname($this->data['firstname'])
            ->setLastname($this->data['lastname'])
            ->setEnabled($this->data['enabled'])
            ->setRegisterDate($this->data['registerDate'])
            ->setLastvisitDate($this->data['lastvisitDate'])
            ->setRole($this->data['role']);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testSetOptions()
    {
        $object = new Admin_Model_User($this->data);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testUserImplementsIterator()
    {
        $object = new Admin_Model_User($this->data);
        // testing offsetExists method
        $this->assertTrue(isset($object['username']));
        // testing offsetGet method
        foreach($this->data as $key => $value) {
            $this->assertEquals($value, $object[$key]);
        }
        // testing offsetSet method
        $object['username'] = 'superman';
        $this->assertEquals('superman', $object->getUsername());
        // testing offsetUnset method
        unset($object['username']);
        $this->assertType('null', $object->getUsername());
    }
}
