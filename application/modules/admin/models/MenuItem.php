<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_MenuItem
 *
 * @method Admin_Model_MenuItem setId(int $id)
 * @method Admin_Model_MenuItem setMenu_id(int $id)
 * @method Admin_Model_MenuItem setLabel(string $label)
 * @method Admin_Model_MenuItem setUri(string $uri)
 * @method Admin_Model_MenuItem setPosition(int $position)
 * @method Admin_Model_MenuItem setPublished(bool $published)
 * @method Admin_Model_MenuItem setLft(int $lft)
 * @method Admin_Model_MenuItem setRgt(int $rgt)
 * @method Admin_Model_MenuItem setParent_id(int $parent_id)
 * @method Admin_Model_MenuItem setLevel(int $level)
 *
 * @method int getId()
 * @method int getMenu_id()
 * @method string getLabel()
 * @method string getUri()
 * @method int getPosition()
 * @method bool getPublished()
 * @method getLft getLft()
 * @method int getRgt()
 * @method int getParent_id()
 * @method int getLevel()
 *
 * @author miholeus
 */
class Admin_Model_MenuItem extends Admin_Model_Abstract
{
    protected $id;
    protected $menu_id;
    protected $label;
    protected $uri;
    protected $position;
    protected $published;
    protected $lft;
    protected $rgt;
    protected $parent_id;
    protected $level;
    
    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
    /**
     * Recursively add items to form's selectbox
     * 
     * @param array $items groupped by parent id
     * @param Admin_Form_Template_Simple $form
     * @param string $name of $form's selectbox
     * @param int $parentIdSelected OPTIONAL    Check $parentIdSelected option
     * @param int $parentId OPTIONAL    Start iterations on $parentId
     * @param int $lvl  OPTIONAL    Level of depth of iteratable item
     * @return void
     */
    public function processTreeElementForm($items, Admin_Form_Template_Simple $form,
            $name, $parentIdSelected = 0, $parentId = 0, $lvl = 1)
    {
        if(!isset($items[$parentId])) {
            return null;
        }

        foreach($items[$parentId] as $item) {
            $label = str_repeat('-', $lvl);
            $label .= $item->getLabel();
            $option = $form->addElementOption($name, $item->getId(), $label);
            if($item->getId() === $parentIdSelected) {
                $option->setValue($item->getId());
            }
            $this->processTreeElementForm($items, $form, $name, $parentIdSelected, $item->getId(), $lvl + 1);
        }
    }
}
