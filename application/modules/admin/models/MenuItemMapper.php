<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_MenuItemMapper is a mapper for Admin_Model_MenuItem
 *
 * @author miholeus
 */
class Admin_Model_MenuItemMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_MenuItem
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_MenuItem';
    protected function createFromArray(array $array)
    {
        return new Admin_Model_MenuItem($array);
    }
	/**
	 * Fetches menus
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_MenuItemCollection all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        return new Admin_Model_MenuItemCollection($resultSet->toArray(), $this);
    }
    /**
     * Fetch Menu Items groupped by parent ids
     *
     * @return array Admin_Model_MenuItem
     */
    public function fetchAllGrouppedByParentId($where = null, $order = null)
    {
        $menuItems = $this->fetchAll($where, $order);
        $items = array();
        foreach($menuItems as $item) {
            $parentId = $item->getParent_id();
            $items[$parentId][] = $item;
        }
        return $items;
    }
    /**
     *
     * @param Admin_Model_MenuItem $menu
     * @return Admin_Model_MenuItem
     */
    public function save(Admin_Model_MenuItem $menu)
    {
        $data = array(
            'menu_id'                => $menu->getMenu_id(),
            'label'                 => $menu->getLabel(),
            'uri'                   => $menu->getUri(),
            'position'              => $menu->getPosition(),
            'published'             => $menu->getPublished(),
            'parent_id'             => $menu->getParent_id(),
        );

        if(0 != $menu->getParent_id()) {
            $parentMenu = $this->findById($menu->getParent_id());

            $data['level'] = $parentMenu->getLevel();// parent level
//            $data['lft'] = $parentMenu->getLft();

            $rgtKey = $parentMenu->getRgt();
        } else {
            $rgtKey = 0;
        }
        
        if (null === ($id = $menu->getId())) {
            $insertedId = $this->getDbTable()->_insert($data, $rgtKey);
            $menu->setId($insertedId);
        } else {
            $this->getDbTable()->_update($data, array('id = ?' => $id), $rgtKey);
        }
    }
    /**
     * Find menu item by its id
     * 
     * @param int $id
     * @param Admin_Model_MenuItem $menu
     * @return Admin_Model_MenuItem $menu
     */
    public function findById($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new UnexpectedValueException("MenuItem by id " . $id . " not found");
        }
        $object = new Admin_Model_MenuItem();
        $row = $result->current();
        $object->setOptions($row->toArray());
        return $object;
    }
    /**
     * @return int max level
     */
    public function findMaxLevel()
    {
        $row = $this->getDbTable()->findMaxLevel();
        return $row['max_level'];
    }
    /**
     * Delete Menu Item by its id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $this->getDbTable()->_delete($id);
    }
    /**
     * Sets published in where clause
     * @param int $published
     * @return Admin_Model_MenuItemMapper
     */
    public function published($published)
    {
        /**
         * isset added to prevent the clause when user is updated
         * and $enabled value comes as null
         */
        if($published != '*' && isset($published)) {
            $published = (int)$published;
            $this->_select = $this->getSelect();
            $this->_select->where('published = ?', $published);
        }
        return $this;
    }
    /**
     * Sets Menu Id in where clause
     *
     * @param int $menuId
     * @return Admin_Model_MenuItemMapper
     */
    public function menuId($menuId)
    {
        if(!empty($menuId)) {
            $this->_select = $this->getSelect();
            $this->_select->where('menu_id = ?', $menuId);
        }
        return $this;
    }
    /**
     * Sets level in where clause
     *
     * @param int $lvl
     * @return Admin_Model_MenuItemMapper
     */
    public function level($lvl)
    {
        if(!empty($lvl)) {
            $this->_select = $this->getSelect();
            $this->_select->where('level = ?', $lvl);
        }
        return $this;
    }
    /**
     * Simple search by firstname field using like operator
     *
     * @param string $value search value
     * @return Admin_Model_MenuItemMapper
     */
    public function search($value)
    {
        if(!empty($value)) {
            $value = str_replace('\\', '\\\\', $value);
            $value = addcslashes($value, '_%');
            $this->_select = $this->getSelect();
            $this->_select->where('label LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
}
