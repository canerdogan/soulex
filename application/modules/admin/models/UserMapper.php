<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Admin_Model_UserMapper
 *
 * @author miholeus
 */
class Admin_Model_UserMapper extends Admin_Model_DataMapper_Abstract
{
    const ERR_USER_EXISTS = 1;
    
    protected $_dbTableClass = 'Admin_Model_DbTable_User';
    protected $markForDeletion = false;
    /**
     * Objects that were marked for deletion
     *
     * @var array
     */
    protected $deletedObjects = array();

    protected function createFromArray(array $array)
    {
        return new Admin_Model_User($array);
    }
    public function save(Admin_Model_User $user)
    {
        $data = $user->toArray();
        unset($data['lastvisitDate']);

        if(!empty($data['password'])) {
            $data['password'] = $user->generatePassword($data['password']);
        }

        if (null === ($id = $user->getId())) {
            // checks user existance, throws exception if found one
            $this->checkUserExistanceByUsername($data['username']);

            $data['registerDate'] = date("Y-m-d H:i:s");

            try {
                $this->getDbTable()->insert($data);
                $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
                $user->setId($insertedId);
                $user->setRegisterDate($data['registerDate']);
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }

        } else {
            unset($data['registerDate']);

            try {
                $rowUser = $this->getDbTable()->find($id)->current();
                if($rowUser) {
                    /**
                     * username and password can not be null
                     */
                    if(null === $data['username']) {
                        unset($data['username']);
                    }
                    if(empty($data['password'])) {
                        unset($data['password']);
                    }
                    $this->getDbTable()->update($data, array('id = ?' => $id));
                } else {
                    throw new UnexpectedValueException("User with id " . $id . " not found");
                }
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }
        }
        return $user;
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_UserCollection
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        return new Admin_Model_UserCollection($resultSet->toArray(), $this);
    }
    /**
     * Fetches users due to a select statement
     *
     * @return Admin_Model_User array all set
     */
    public function fetch()
    {
        return $this->fetchAll($this->_select);
    }

    public function delete($id)
    {
        $auth = Zend_Auth::getInstance();
        $myId = $auth->getIdentity()->id;
        if($myId == $id) {
            throw new InvalidArgumentException("You cannot delete yourself");
        }
        $user = $this->findById($id);
        $this->markForDeletion = true;
        $this->deletedObjects[] = $user;
    }

    /**
     * Mass user deletion
     *
     * @uses delete
     * @param array $ids
     */
    public function deleteBulk($ids)
    {
        if(is_array($ids) && count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }
    /**
     * Finds data row by id and returns new object
     * If object was not found then we set initial null values
     * to object
     *
     * @param int $id
     * @throws UnexpectedValueException if user was not found
     * @return Admin_Model_User
     */
    public function findById($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new UnexpectedValueException("User by id " . $id . " not found");
        }
        $user = new Admin_Model_User();
        $row = $result->current();
        $user->setOptions($row->toArray());
        return $user;
    }

    public function updateLastVisitDate($userId)
    {
        $date = date("Y-m-d H:i:s");
        $where = $this->getDbTable()->getDefaultAdapter()
                 ->quoteInto('id = ?', $userId);
        $this->getDbTable()->update(array('lastvisitDate' => $date), $where);
    }
    /**
     * Sets role in where clause
     *
     * @param string $role
     * @return void
     */
    public function role($role)
    {
        if($role != '') {
            $this->_select = $this->getSelect();
            $this->_select->where('role = ?', $role);
        }
        return $this;
    }
    /**
     * Sets enabled in where clause
     *
     * @param int $enabled
     * @return void
     */
    public function enabled($enabled)
    {
        /**
         * isset added to prevent the clause when user is updated
         * and $enabled value comes as null
         */
        if($enabled != '*' && isset($enabled)) {
            $enabled = (int)$enabled;
            $this->_select = $this->getSelect();
            $this->_select->where('enabled = ?', $enabled);
        }
        return $this;
    }
    /**
     * Simple search by firstname field using like operator
     *
     * @param string $value search value
     * @return void
     */
    public function search($value)
    {
        if(!empty($value)) {
            $value = str_replace('\\', '\\\\', $value);
            $value = addcslashes($value, '_%');
            $this->_select = $this->getSelect();
            $this->_select->where('firstname LIKE ?', '%' . $value . '%');
        }
        return $this;
    }

    /**
     * Checks if username already exists and throws exception if found one
     *
     * @param string $name
     * @throws Zend_Exception
     * @return Admin_Model_User|null
     */
    public function checkUserExistanceByUsername($name)
    {
        $user = $this->findByUsername($name);
        if(null !== $user) {// username already exists
            $this->triggerErrorUserExists($name);
        }
        return $user;
    }

    /**
     * Finds user by username, returns null if nothing was found
     *
     * @param string $name
     * @return Admin_Model_User|null
     */
    public function findByUsername($name)
    {
        $row = $this->getDbTable()->fetchRow(
               $this->getDbTable()->getDefaultAdapter()
                    ->quoteInto('username = ?', $name));
        if(null !== $row) {
            return new Admin_Model_User($row->toArray());
        }
        return null;
    }
    /**
     * Deletes object in DB after mapper was destroyed
     */
    public function __destruct()
    {
        if(count($this->deletedObjects) > 0 && true === $this->markForDeletion) {
            foreach($this->deletedObjects as $key => $object) {
                $id = $object->getId();
                if(isset($id)) {
                    $where = $this->getDbTable()->getDefaultAdapter()
                            ->quoteInto('id = ?', $id);
                    $this->getDbTable()->delete($where);
                    $user = $this->getDbTable()->find($id);
                }
            }
        }
    }
    /**
     * Invoked in checkUserExistanceByUsername()
     *
     * @throws Zend_Exception
     */
    private function triggerErrorUserExists($name)
    {
        throw new Zend_Exception(
                'username ' . $name . ' already exists',
                self::ERR_USER_EXISTS
        );
    }
}
