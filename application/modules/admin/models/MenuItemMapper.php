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
class Admin_Model_MenuItemMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_MenuItem
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_MenuItem';
    /**
     *
     * @var Admin_Model_MenuItem
     */
    protected $_object = 'Admin_Model_MenuItem';
    /**
     *
     * @var Admin_Model_MenuItemCollection
     */
    protected $_collection = 'Admin_Model_MenuItemCollection';
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
    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {
        return array(
            'menu_id'               => $object['menu_id'],
            'label'                 => $object['label'],
            'uri'                   => $object['uri'],
            'position'              => $object['position'],
            'published'             => $object['published'],
            'parent_id'             => $object['parent_id'],
        );
    }

    /**
     * Saves menu item
     *
     * @param Admin_Model_MenuItem $menu
     * @return Admin_Model_MenuItem
     */
    public function save(Admin_Model_MenuItem $menu)
    {
        $data = $this->prepareDataForSave($menu);

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
