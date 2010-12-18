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
class Admin_Model_DbTable_MenuItem extends Soulex_Model_DbTable_Abstract_PrefixedTable
{
    protected $_name = 'menu_items';

    private function moveBranchWhenNodeGoesDown($params)
    {
        $this->_db->beginTransaction();
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt - ?"
                    . " WHERE rgt > ? AND rgt <= ?", array(
                    $params['skew_tree'],
                    $params['right_key'],
                    $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft - ?"
                    . " WHERE lft < ? AND lft > ?", array(
                        $params['skew_tree'],
                        $params['left_key'],
                        $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + ?,"
                    . "rgt = rgt + ?, level = level + ? "
                    . " WHERE id IN (?) ", array(
                        $params['skew_edit'],
                        $params['skew_edit'],
                        $params['skew_level '],
                        implode(',', $params['id_edit'])
           ));
           $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    private function moveBranchWhenNodeGoesUp($params)
    {
        $this->_db->beginTransaction();
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + ?"
                    . " WHERE rgt < ? AND rgt > ?", array(
                    $params['skew_tree'],
                    $params['left_key'],
                    $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + ?"
                    . " WHERE lft < ? AND lft > ?", array(
                        $params['skew_tree'],
                        $params['left_key'],
                        $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + ?,"
                    . "rgt = rgt + ?, level = level + ?"
                    . " WHERE id IN (?)", array(
                        $params['skew_edit'],
                        $params['skew_edit'],
                        $params['skew_level'],
                        implode(',', $params['id_edit'])
           ));
           $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Insert new row
     * 
     * @param array $data
     * @param int $rgtKey of parent node
     * @return int inserted $rowId
     */
    public function _insert(array $data, $rgtKey)
    {
        if(0 != $rgtKey) {
            $lft = $rgtKey;//top level element
        } else {
            $lft = $this->findMaxRightKey() + 1;// if node is inserted to root
        }

		$level = 0;// top level of parent
        $parent_id = 0;
        
        if(isset($data['parent_id'])) {
            $parent_id = $data['parent_id'];
        }
        if(isset($data['level'])) {
            $level = $data['level'];
        }
//        if(isset($data['lft'])) {
//            $lft = $data['lft'];
//        }

        $tree_data = array(
            'lft' => $lft,
            'rgt' => $lft + 1,
            'level' => $level + 1,
            'parent_id' => $parent_id
        );
        $data = array_merge($data, $tree_data);

//        if(0 == $rgtKey) {// if node has no parent
//            $rightKey = $this->findMaxRightKey() + 1;
//        }
        $rightKey = $lft;
//        var_dump($rightKey);
//                var_dump($data);die();
        $this->_db->beginTransaction();

        try {

            if($rgtKey > 0) {
                $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + 2,"
                        . "rgt = rgt + 2 WHERE lft > ?", $rightKey);
            }
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + 2"
                    . " WHERE rgt >= ? AND lft < ?", array($rightKey, $rightKey));

            try {
                $this->insert($data);
                $rowId = $this->_db->lastInsertId();
            } catch (Zend_Exception $e) {
                throw $e;
            }

            $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
            throw new RuntimeException("Row insertion failed " . $e->getMessage());
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
        $level_up = isset($data['level']) ? $data['level'] : 1;
        $data['level'] = $level_up;// in case we move to root

        // 3 right_key, left_key detection
        if($data['parent_id'] == $row->parent_id) {// parent node is not changed
            unset($data['level']);
            $row->setFromArray($data);
            $row->save();
            return true;
        } else {
            if(0 == $rgtKey) {
                // move node to root
                $right_key_near = $this->findMaxRightKey();
            } else {
                // simple move to another node
                $right_key_near = $rgtKey - 1;
            }
        }
        throw new RuntimeException("you can not move one node to another, sorry :(");
//        // moving node up level
//        $newLevel = $level_up + 1;
//        if($row->level > $newLevel) {
//            // right key of old parent node
//            $right_key_row = $this->findParentRightKey($row->parent_id);
//            $right_key_near = $right_key_row['rgt'];
//        }

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
//            $skew_edit = $right_key_near - $skew_edit;
            $params['skew_edit'] = $skew_edit;
        var_dump($data);
        print '<br />параметры дерева <br />';
        var_dump($right_key_near, $left_key, $right_key, $params);
        print '<br />уровень нового узла';
        var_dump($level_up);
        die();
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
               throw new InvalidArgumentException('menu item with id ' . $id . ' not found!');
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

       } catch (Exception $e) {
           $this->_db->rollBack();
           throw new RuntimeException("Row deletion failed " . $e->getMessage());
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
     * @return int
     */
    private function findMaxRightKey()
    {
        $select = $this->select()->from(
            array($this->_name),
            array( 'max_right' => 'MAX(rgt)')
        );
        $row = $this->fetchRow($select);
        return $row['max_right'];
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
