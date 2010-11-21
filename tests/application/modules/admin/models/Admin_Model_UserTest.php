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
    public function testAccessorsAndMutators()
    {
        $user = new Admin_Model_User();
        $data = array(
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

        foreach(array_keys($data) as $prop) {
            $getPropName = "get" . ucfirst($prop);
            $setPropName = "set" . ucfirst($prop);
            $this->assertNull($user->$getPropName());
            // setting property value
            $user->$setPropName($data[$prop]);
            $this->assertEquals($data[$prop], $user->$getPropName());
        }
    }

    public function testBadGetSetMethodsThrowExceptions()
    {
        $user = new Admin_Model_User();
        try {
            $this->assertNull($user->getFoo());
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Getting property error(.*)/', $e->getMessage());
        }

        try {
            $this->assertNull($user->setFoo("bar"));
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Setting property error(.*)/', $e->getMessage());
        }
    }

    public function testIdOnlySetOnce()
    {
        $user = new Admin_Model_User();
        $user->setId(2);
        $this->assertEquals(2, $user->getId());
        $user->setId(10);
        $this->assertEquals(2, $user->getId());
    }
    /**
     * If chaining is not supported you'll get fatall error
     */
    public function testSettersCanGenerateChainCalss()
    {
        $user = new Admin_Model_User();

        $user->setUsername('miholeus')
            ->setEmail('me@miholeus.com')
            ->setPassword('123')
            ->setFirstname('miholeus')
            ->setLastname('webmaster')
            ->setEnabled(true)
            ->setRegisterDate('2010-10-19 17:15:00')
            ->setLastvisitDate('2010-11-19 18:12:23')
            ->setRole('administrator');
        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }

    public function testToArrayIsCorrect()
    {
        $data = array(
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
        $user = new Admin_Model_User();
        $user->setUsername('miholeus')
                ->setId(2)
                ->setEmail('me@miholeus.com')
                ->setPassword('123')
                ->setFirstname('miholeus')
                ->setLastname('webmaster')
                ->setEnabled(true)
                ->setRegisterDate('2010-10-19 17:15:00')
                ->setLastvisitDate('2010-11-19 18:12:23')
                ->setRole('administrator');
        $this->assertEquals($data, $user->toArray());
    }

    public function testSetOptions()
    {
        $data = array(
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
        $user = new Admin_Model_User($data);
        $this->assertEquals($data, $user->toArray());
    }

    public function testUserImplementsIterator()
    {
        $data = array(
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
        $user = new Admin_Model_User($data);
        // testing offsetExists method
        $this->assertTrue(isset($user['username']));
        // testing offsetGet method
        foreach($data as $key => $value) {
            $this->assertEquals($value, $user[$key]);
        }
        // testing offsetSet method
        $user['username'] = 'superman';
        $this->assertEquals('superman', $user->getUsername());
        // testing offsetUnset method
        unset($user['username']);
        $this->assertType('null', $user->getUsername());
    }
}
