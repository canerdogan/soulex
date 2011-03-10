<?php
/**
 * Model_PageMapper is designed to incapsulate database operations
 * within class.
 * Create/update/delete page operations use transactions to
 * set up correct values of left/right keys in nested set.
 *
 * @author miholeus
 */

class Model_PageMapper extends Soulex_Model_DbTable_Abstract_PrefixedTable
{
	protected $_name = 'pages';

	protected $_dependentTables = 'Model_ContentNode';
    /**
     *
     * @var Zend_Db_Table_Select
     */
    protected $_select;
	/*
	protected $_referenceMap = array(
		'Page' => array(
			'columns' => 'parent_id',
			'refTableClass' => 'Model_page',
			'refColumns' => array('id'),
			'onDelete' => self::CASCADE,
			'onUpdate' => self::RESTRICT
		)
	);
	*/
    /**
     * Form nodes array according to page data
     * Formed node's array look like this:
     * array('type', 'module', 'controller', 'action', 'value')
     *
     * @param array $data page's data
     * @return array node's data information
     */
    protected function formNodesData($data)
    {
        $nodesData = array();
        if(isset($data['nodes']) && count($data['nodes']) > 0) {
            foreach($data['nodes'] as $nodeName => $nodeValue) {
                $nodesData[$nodeName] = array_merge($nodeValue, array(
                    'value' => $data[$nodeName]
                ));
            }
        }
        return $nodesData;
    }
    /**
     * Save data nodes
     *
     * @param array $data nodes' data information
     * @param int $pageId id of page, where node will be saved
     * @param string $action default action set/create node
     */
    protected function saveNodesData(array $data, $pageId, $action = 'set')
    {
        if(count($data) > 0) {
            $mdlContentNode = new Model_ContentNode();
            foreach($data as $nodeName => $nodeData) {
                if($nodeData['type'] == 1) {// dynamic node
                    $_nodeData = array('module' => $nodeData['module'],
                        'controller' => $nodeData['controller'],
                        'action' => $nodeData['action']);
                    $mdlContentNode->{$action . 'Node'}($pageId,
                            $nodeName,
                            serialize($_nodeData),
                            $nodeData['type']
                    );
                } else { // static node
                    $mdlContentNode->{$action . 'Node'}($pageId,
                            $nodeName,
                            $nodeData['value'],
                            $nodeData['type']
                    );
                }
            }
        }
    }
	/**
	 * Creates new page
	 *
	 * @uses Model_ContentNode
	 *
	 * @param array $data page's data array
     *
	 * @return int id of created page
	 */
	public function createPage($data)
	{
		$lft = 0;//top level page
		$level = 0;//top level of parent

		$this->_db->beginTransaction();
		$this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + 2 WHERE rgt > ?", $lft);
		$this->_db->query("UPDATE " . $this->_name . " SET lft = lft + 2 WHERE lft > ?", $lft);

		$row = $this->createRow();
		$row->title = $data['title'];
		$row->uri = $data['uri'];
		$row->meta_keywords = $data['meta_keywords'];
		$row->meta_description = $data['meta_description'];
        $row->published = $data['published'];
		$row->lft = $lft + 1;
		$row->rgt = $lft + 2;
		$row->level = $level + 1;
        if(!empty($data['layout'])) {
            $row->layout = $data['layout'];
        } else {
            $row->layout = null;
        }
		$row->save();

		$pageId = $this->_db->lastInsertId();

		$this->_db->commit();

        // creating nodes data
        $nodesData = $this->formNodesData($data);
        $this->saveNodesData($nodesData, $pageId, 'create');

		return $pageId;
	}
	/**
	 * Updates selected page
	 *
	 * @param int $pageId
	 * @param array $data
	 * @return void
	 */
	public function updatePage($pageId, $data)
	{
		$row = $this->find($pageId)->current();
		if($row) {
			$row->title = $data['title'];
			$row->uri = $data['uri'];
			$row->meta_keywords = $data['meta_keywords'];
			$row->meta_description = $data['meta_description'];
            $row->published = $data['published'];
            if(!empty($data['layout'])) {
                $row->layout = $data['layout'];
            } else {
                $row->layout = null;
            }
			$row->save();

			unset($data['title'], $data['uri'], $data['meta_keywords'],
				$data['meta_description'], $data['published']);
            unset($data['id'], $data['submit']);// remove form id and submit button

            $mdlContentNode = new Model_ContentNode();
            // saving nodes data
            $nodesData = $this->formNodesData($data);
            $this->saveNodesData($nodesData, $pageId);


//            $pageNodes = $mdlContentNode->getPageNodes($pageId);
//
//            foreach($pageNodes->toArray() as $node) {
//                if(!array_key_exists($node['name'], $data)) {
//                    // remove unnessesary node
//                    $mdlContentNode->delete($this->getWhere($node['id']));
//                } else { // update node value
//                    $mdlContentNode->setNode($pageId, $node['name'], $data[$node['name']]);
//                }
//                unset($data[$node['name']]);
//            }
//
//            if(count($data) > 0) {
//                // adding new nodes
//                foreach($data as $key => $value) {
//                    $mdlContentNode->setNode($pageId, $key, $value);
//                }
//            }

		} else {
			throw new Zend_Exception('Update failed: no page found!');
		}
	}
	/**
	 * Deletes page
	 *
	 * @param int $id of page that will be deleted
	 * @return true if success
	 */
	public function deletePage($id)
	{
		$this->_db->beginTransaction();
		$row = $this->find($id)->current();
		if($row) {
			$myLeft = $row->lft;
			$myRight = $row->rgt;
			$myWidth = $myRight - $myLeft + 1;

			$this->_db->query("DELETE FROM " . $this->_name . " WHERE lft BETWEEN ? AND ?",
					array($myLeft, $myRight)
			);
			$this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt - ? WHERE rgt > ?",
					array($myWidth, $myRight)
			);
			$this->_db->query("UPDATE " . $this->_name . " SET lft = lft - ? WHERE lft > ?",
					array($myWidth, $myRight)
			);

			$this->_db->commit();

			return true;
		} else {
			$this->_db->rollBack();
			throw new Zend_Exception('Delete failed: no page found!');
		}
	}

	public function findPage($id, $whereStatement)
	{
        $where = $this->getDefaultAdapter()->quoteInto('id = ?', $id);
        if(is_array($whereStatement) && count($whereStatement) > 0) {
            foreach($whereStatement as $key => $val) {
                $where .= ' AND ' . $this->getDefaultAdapter()->quoteInto($key . ' = ?', $val);
            }
        }

		$row = $this->fetchRow($where);

		if($row) {
			$pageFields = $row->toArray();
			$mdlContentNode = new Model_ContentNode();
			$contentNodes = $row->findDependentRowset($mdlContentNode);
			$pageContent = array();
			if(count($contentNodes) > 0) {
				foreach($contentNodes as $node) { //looking for content fields
					$pageContent[$node['name']] = array(
                        'id'            => $node['id'],
                        'value'         => $node['value'],
                        'isInvokable'   => $node['isInvokable'],
                        'params'        => $node['params']
                    );
				}
			}
			return array_merge($pageFields, array('_data' => $pageContent));
		} else {
			return null;
		}
	}

    private function getWhere($id)
    {
        return $this->getAdapter()->quoteInto("id = ?", $id);
    }
    /**
     * Fetch all pages
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAllPages($where, $order)
    {
        $select = $this->select();
        if(null !== $where) {
            $select->where($where);
        }
        if(null !== $order) {
            $select->order($order);
        }

        return $this->fetchAll($select);
    }
    /**
     * Sets published in where clause
     *
     * @param int $enabled
     * @return void
     */
    public function published($published)
    {
        $this->_select = $this->getSelect();
        $this->_select->where('published = ?', $published);
    }
    /**
     * Simple search by title field using like operator
     *
     * @param string $value search value
     * @return void
     */
    public function search($value)
    {
        $this->_select = $this->getSelect();
        $this->_select->where('title LIKE ?', '%' . $value . '%');
    }

    public function getSelect()
    {
        if(null === $this->_select) {
            $this->_select = $this->select();
        }
        return $this->_select;
    }
    /**
	 * Fetches paginator
	 *
     * @return Zend_Paginator_Adapter_DbSelect
	 */
    public function fetchPaginator()
    {
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->_select);
        return $adapter;
    }
    /**
     * Sets ordering state
     *
     * @param string $spec the column and direction to sort by
     * @return void
     */
    public function order($spec)
    {
        $this->_select = $this->getSelect();
        $this->_select->order($spec);
    }
}