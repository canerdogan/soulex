<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of Admin_Model_UserTest
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
}
