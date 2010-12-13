<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */
/**
 * Admin_Model_User is a simple Model.
 * All properties should be declared as protected due to toArray method
 * that uses reflection properties
 *
 * @author miholeus
 *
 * @todo use lazy instantiation
 * @todo set property for modified values in parent class, insert/update
 *       only modified values
 *
 * @method Admin_Model_User setId(int $id)
 * @method Admin_Model_User setUsername(string $username)
 * @method Admin_Model_User setEmail(string $email)
 * @method Admin_Model_User setPassword(string $password)
 * @method Admin_Model_User setFirstname(string $firstname)
 * @method Admin_Model_User setLastname(string $lastname)
 * @method Admin_Model_User setEnabled(bool $enabled)
 * @method Admin_Model_User setRegisterDate(string $date)
 * @method Admin_Model_User setLastvisitDate(string $date)
 * @method Admin_Model_User setRole(string $role)
 * @method Admin_Model_User getId()
 * @method Admin_Model_User getUsername()
 * @method Admin_Model_User getEmail()
 * @method Admin_Model_User getPassword()
 * @method Admin_Model_User getFirstname()
 * @method Admin_Model_User getLastname()
 * @method Admin_Model_User getEnabled()
 * @method Admin_Model_User getRegisterDate()
 * @method Admin_Model_User getLastvisitDate()
 * @method Admin_Model_User getRole()
 */
class Admin_Model_User extends Admin_Model_Abstract
{   
    protected $id;
    protected $username;
    protected $email;
    protected $password;
    protected $firstname;
    protected $lastname;
    protected $enabled;
    protected $registerDate;
    protected $lastvisitDate;
    protected $role;

    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }

    public function generatePassword($password)
    {
        return md5($password);
    }
}
