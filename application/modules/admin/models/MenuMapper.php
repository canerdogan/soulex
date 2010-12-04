<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_MenuMapper maps Menu objects with database layer
 *
 * @author miholeus
 */
class Admin_Model_MenuMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_Menu
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Menu';
    protected function createFromArray(array $array)
    {
        return new Admin_Model_Menu($array);
    }
	/**
	 * Fetches menus
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_MenuCollection all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        return new Admin_Model_MenuCollection($resultSet->toArray(), $this);
    }

    public function save(Admin_Model_Menu $menu)
    {
        $data = array(
            'title'                 => $menu->getTitle(),
            'menutype'              => $menu->getMenutype(),
            'description'           => $menu->getDescription()
        );

        if (null === ($id = $menu->getId())) {
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $menu->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function findById($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new UnexpectedValueException("Menu by id " . $id . " not found");
        }
        $object = new Admin_Model_Menu();
        $row = $result->current();
        $object->setOptions($row->toArray());
        return $object;
    }
    /**
     * Delete menu by id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $where = $this->getDbTable()->getDefaultAdapter()->quoteInto('id = ?', $id);
        $this->getDbTable()->delete($where);
    }
}
