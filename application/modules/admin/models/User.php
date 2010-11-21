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
