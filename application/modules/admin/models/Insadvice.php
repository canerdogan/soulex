<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of Insadvice
 * @method Admin_Model_insadvice getId()
 *
 * @author miholeus
 */
class Admin_Model_Insadvice extends Admin_Model_Abstract
{
    protected $id;
    protected $name;
    protected $date;
    protected $teaser;
    protected $text;
    protected $file;
    protected $cat_id;
    protected $user_id;
    protected $draft;
    
    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}