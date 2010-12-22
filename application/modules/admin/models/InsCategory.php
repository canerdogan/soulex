<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of InsCategory
 *
 * @author miholeus
 */
class Admin_Model_InsCategory extends Admin_Model_Abstract
{
    protected $id;
    protected $name;
    protected $image;
    protected $parent_id;

    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}