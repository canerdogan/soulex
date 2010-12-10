<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * User fixture
 * Sets up user authentication
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Fixture_User extends ControllerTestCase
{
    /**
     *
     * @var Admin_Model_User
     */
    private static $_userInstance = null;
    public static function getUserInstance()
    {
        if(null === self::$_userInstance) {
            self::$_userInstance = new Admin_Model_User(array(
            'username' => 'admin',
            'email' => 'me@miholeus.com',
            'password' => '1',
            'firstname' => 'miholeus',
            'lastname' => 'unknown',
            'enabled' => '1',
            'role' => 'administrator'
        ));
        }
        return self::$_userInstance;
    }
    /**
     * Saves User in database
     */
    public static function loadUser()
    {
        $userMapper = new Admin_Model_UserMapper();
        $userMapper->save(self::getUserInstance());
    }
    public function authenticate()
    {
        self::loadUser();
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => self::$_userInstance->getUsername(),
                    "password" => self::$_userInstance->getPassword()
        ));
        $this->dispatch('/admin/');
        $_POST = array();// @todo replace with ZF function
        $this->getRequest()->setMethod('GET');
    }

    public static function destroy()
    {
        $userMapper = new Admin_Model_UserMapper();
        $userMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE users');
        self::$_userInstance = null;
    }
}
