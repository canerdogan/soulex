<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_EventsMapper
 *
 * @author miholeus
 */
class Admin_Model_EventsMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_Events
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Events';
    protected function createFromArray(array $array)
    {
        return new Admin_Model_Events($array);
    }
    public function save(Admin_Model_Events $Events)
    {
        $data = array(
            'title'                 => $Events->getTitle(),
            'short_description'     => $Events->getShort_description(),
            'detail_description'    => $Events->getDetail_description(),
            'img_preview'           => $Events->getImg_preview(),
            'published'             => $Events->getPublished(),
            'updated_at'            => $Events->getUpdated_at(),
            'published_at'          => $Events->getPublished_at()
        );

        if (null === ($id = $Events->getId())) {
            $data['created_at'] = date("Y-m-d H:i:s");
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $Events->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    public function findById($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new UnexpectedValueException("Events by id " . $id . " not found");
        }
        $object = new Admin_Model_Events();
        $row = $result->current();
        $object->setOptions($row->toArray());
        return $object;
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_EventsCollection all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        return new Admin_Model_EventsCollection($resultSet->toArray(), $this);
    }

    public function delete($id)
    {
        $object = $this->findById($id);
        if(null !== $object) {
            $where = $this->getDbTable()->getDefaultAdapter()->quoteInto('id = ?', $id);
            $this->getDbTable()->delete($where);
        }
    }

    public function deleteBulk(array $ids)
    {
        if(count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }
    /**
     * Sets published in where clause
     *
     * @param int $published
     * @return void
     */
    public function published($published)
    {
        /**
         * isset added to prevent the clause when user is updated
         * and $enabled value comes as null
         */
        if($published != '*' && isset($published)) {
            $this->_select = $this->getSelect();
            $this->_select->where('published = ?', $published);
        }
        return $this;
    }
    /**
     * Simple search by title field using like operator
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
            $this->_select->where('title LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
}
