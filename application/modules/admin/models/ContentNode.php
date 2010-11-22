<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_ContentNode is model that manipulates content nodes
 * objects
 *
 * @author miholeus
 */
class Admin_Model_ContentNode extends Admin_Model_Abstract
{
    protected $id;
    protected $name;
    protected $value;
    protected $isInvokable;
    protected $params;
    protected $page_id;

    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }

    public function setPageId($id)
    {
        $this->page_id = $id;
        return $this;
    }

    public function getPageId()
    {
        return $this->page_id;
    }

    public function getContentValue($param = null)
    {
        if($this->getIsInvokable()) { // dynamic node
            $content = unserialize($this->value);
            return isset($content[$param]) ? $content[$param] : null;
        } else {
            // if node is dynamic and
            // param is setted up to module, controller, action
            return $param === null ? $this->value : null;
        }
    }

    public function getParams()
    {
        return unserialize($this->params);
    }
    /**
     *
     * @param array $params
     * @return Admin_Model_ContentNode
     */
    public function setParams($params)
    {
        $this->params = serialize($params);
        return $this;
    }
}
