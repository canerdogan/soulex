<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_NewsMapper
 *
 * @author miholeus
 */
class Admin_Model_NewsMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_News
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_News';

    protected function createFromArray(array $array)
    {
        return new Admin_Model_News($array);
    }

    public function save(Admin_Model_News $news)
    {
        $data = $news->toArray();

        if (null === ($id = $news->getId())) {
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $news->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    /**
     * Finds data row by id and returns new object
     * If object was not found then we set initial null values
     * to object
     *
     * @param int $id
     * @throws UnexpectedValueException if news was not found
     * @return Admin_Model_News
     */
    public function findById($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new UnexpectedValueException("News by id " . $id . " not found");
        }
        $object = new Admin_Model_News();
        $row = $result->current();
        $object->setOptions($row->toArray());
        return $object;
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_NewsCollection all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        return new Admin_Model_NewsCollection($resultSet->toArray(), $this);
    }
    /**
     * Delete news by $id
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $where = $this->getDbTable()->getDefaultAdapter()->quoteInto('id = ?', $id);
        $this->getDbTable()->delete($where);
    }
    /**
     * Mass news deletion
     *
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
     * Sets published in where clause
     *
     * @param int $published
     * @return Admin_Model_NewsMapper
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
     * Simple search by title field using like operator
     *
     * @param string $value search value
     * @return Admin_Model_NewsMapper
     */
    public function search($value)
    {
        if(!empty($value)) {
            $value = str_replace('\\', '\\\\', $value);
            $value = addcslashes($value, '_%');
            $this->_select = $this->getSelect();
            $this->_select->where('title LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
}
