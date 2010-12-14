<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Description of MenuItem
 *
 * @author miholeus
 */
class Admin_Model_DbTable_MenuItem extends Zend_Db_Table_Abstract
{
    protected $_name = 'menu_items';

    public function moveBranchWhenNodeGoesDown($params)
    {
        $this->_db->beginTransaction();
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET right_key = right_key - ?"
                    . " WHERE right_key > ? AND right_key <= ?", array(
                    $params['skew_tree'],
                    $params['right_key'],
                    $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET left_key = left_key - ?"
                    . " WHERE left_key < ? AND left_key > ?", array(
                        $params['skew_tree'],
                        $params['left_key'],
                        $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET left_key = left_key + ?,"
                    . "right_key = right_key + ?, level = level + ? "
                    . " WHERE id IN (?) ", array(
                        $params['skew_edit'],
                        $params['skew_edit'],
                        $params['skew_level '],
                        implode(',', $params['id_edit'])
           ));
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
        }
    }

    public function moveBranchWhenNodeGoesUp($params)
    {
        $this->_db->beginTransaction();
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET right_key = right_key + ?"
                    . " WHERE right_key < ? AND right_key > ?", array(
                    $params['skew_tree'],
                    $params['left_key'],
                    $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET left_key = left_key + ?"
                    . " WHERE left_key < ? AND left_key > ?", array(
                        $params['skew_tree'],
                        $params['left_key'],
                        $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET left_key = left_key + ?,"
                    . "right_key = right_key + ?, level = level + ? "
                    . " WHERE id IN (?) ", array(
                        $params['skew_edit'],
                        $params['skew_edit'],
                        $params['skew_level '],
                        implode(',', $params['id_edit'])
           ));
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
        }
    }

    /**
     * Insert new row
     * 
     * @param array $data
     * @return int inserted $rowId
     */
    public function _insert(array $data)
    {
        $lft = 0;//top level element
		$level = 0;//top level of parent
        $parent_id = 0;
        
        if(isset($data['parent_id'])) {
            $parent_id = $data['parent_id'];
        }
        if(isset($data['level'])) {
            $level = $data['level'];
        }
        if(isset($data['lft'])) {
            $lft = $data['lft'];
        }

        $tree_data = array(
            'lft' => $lft + 1,
            'rgt' => $lft + 2,
            'level' => $level + 1,
            'parent_id' => $parent_id
        );
        $data = array_merge($data, $tree_data);

        $this->_db->beginTransaction();
        
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + 2 WHERE rgt > ?", $lft);
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + 2 WHERE lft > ?", $lft);

            $this->insert($data);

            $rowId = $this->_db->lastInsertId();

            $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
        }

        return $rowId;
    }
    /**
     * Update current row
     * 
     * @param array $data
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @param string $rgtKey of parent row
     */
    public function _update(array $data, $where, $rgtKey)
    {
        $row = $this->fetchRow($where);

        // 1
        $level  = $row->level;
        $left_key    = $row->lft;
        $right_key    = $row->rgt;
        // 2 level of new parent node (1 - for root)
        $level_up = $data['parentLevel'];

        // 3 right_key, left_key detection
        if($data['parent_id'] == $row->parent_id &&
                0 != $rgtKey) {// parent node is not changed
            if($row->parent_id != $data['parent_id']) {
                throw new Zend_Exception("You can't move node when parent"
                         . " node remains unchanged");
                $left_key_near = 1;// @todo fix it with correct value
            }
        } else {
            if(0 == $rgtKey) {
                // move node to root
                $maxRgtKeyRow = $this->findMaxRightKey();
                $right_key_near = $maxRgtKeyRow['max_right'];
            } else {
                // simple move to another node
                $right_key_near = $rgtKey - 1;
            }
        }

        // moving node up level
        $newLevel = $data['parentLevel'] + 1;
        if($row->level > $newLevel) {
            // right key of old parent node
            $right_key_row = $this->findParentRightKey($row->parent_id);
            $right_key_near = $right_key_row['rgt'];
        }

        $skew_level = $level_up - $level + 1; // moving node offset
        $skew_tree  = $right_key - $left_key + 1; // tree keys offset

        $id_edit = $this->getAllIdsOfMovingNodesInBranch($left_key, $right_key);

        $params = array(
            'skew_tree' => $skew_tree,
            'left_key' => $left_key,
            'right_key_near' => $right_key_near,
            'skew_level' => $skew_level,
            'id_edit' => $id_edit
        );

        if($right_key_near > $right_key) {// moving node up
            // editing node keys offset
            $skew_edit = $right_key_near - $left_key + 1;
            $params['skew_edit'] = $skew_edit;
            $this->moveBranchWhenNodeGoesUp($params);
        } else {// moving node down
            $skew_edit = $right_key_near - $left_key + 1 - $skew_tree;
            $params['skew_edit'] = $skew_edit;
            $this->moveBranchWhenNodeGoesDown($params);
        }

        $row->setFromArray($data);

        $row->save();
//        $this->update($data, $where);
    }
    /**
     * Delete row by its id
     *
     * @param int $id
     */
    public function _delete($id)
    {
       $this->_db->beginTransaction();

       try {
           $row = $this->find($id)->current();
           if(!$row) {
               throw new Zend_Exception('Menu Item with ID ' . $id . ' not found!');
           }
           
           $lft = $row->lft;
           $rgt = $row->rgt;
           $width = $rgt - $lft + 1;

           $this->delete('lft BETWEEN ' . $lft . ' AND ' . $rgt);

           $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt - "
                   . $width . " WHERE rgt > ?", $rgt);
           $this->_db->query("UPDATE " . $this->_name . " SET lft = lft - "
                   . $width . " WHERE lft > ?", $rgt);

           $this->_db->commit();

       } catch (Zend_Exception $e) {
           $this->_db->rollBack();
       }

    }
    /**
     *
     * @return Zend_Db_Table_Row_Abstract
     */
    public function findMaxLevel()
    {
        $select = $this->select()->from(array($this->_name), array( 'max_level' => 'MAX(level)'));
        return $this->fetchRow($select);
    }
    /**
     * Find MAX right key value of tree
     * 
     * @return Zend_Db_Table_Row_Abstract
     */
    private function findMaxRightKey()
    {
        $select = $this->select()->from(
            array($this->_name),
            array( 'max_right' => 'MAX(rgt)')
        );
        return $this->fetchRow($select);
    }
    /**
     * Find Right Key of Parent Node
     * 
     * @param int $id of parent node
     * @return Zend_Db_Table_Row_Abstract
     */
    private function findParentRightKey($id)
    {
        $select = $this->select()
                ->from(array($this->_name), array("rgt"))
                ->where("id = ?" , $id);
        return $this->fetchRow($select);
    }
    /**
     * Select ids of all nodes that exist in branch where
     * moving node is
     *
     * @param int $left_key
     * @param int $right_key
     * @return array
     */
    private function getAllIdsOfMovingNodesInBranch($left_key, $right_key)
    {
        $ids = array();
        $select = $this->select()
                ->from(array($this->_name), array("id"))
                ->where("lft >= ?" , $left_key)
                ->where("rgt <= ?", $right_key);
        $rows = $this->fetchAll($select)->toArray();
        if(is_array($rows) && count($rows) > 0) {
            foreach($rows as $row) {
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }
}
