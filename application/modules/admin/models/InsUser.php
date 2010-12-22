<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of InsUser
 *
 * @author miholeus
 */
class Admin_Model_InsUser extends Admin_Model_Abstract
{
    protected $id;
    protected $firstname;
    protected $lastname;
    protected $middlename;
    protected $email;
    protected $username;
    protected $password;
    protected $birthdate;
    protected $image;
    protected $city;
    protected $country;
    protected $phone;
    protected $fax;
    protected $icq;
    protected $skype;
    protected $org;
    protected $org_address;
    protected $org_site;
    protected $org_about;
    protected $rating;
    protected $lat;
    protected $lng;
    protected $work_exp;
    protected $salary;
    protected $published;
    
    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}
