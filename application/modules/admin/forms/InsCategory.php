<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of InsadviceCategory
 *
 * @author miholeus
 */
class Admin_Form_InsCategory extends Admin_Form_Template_Simple
{
    public function init()
    {
        $this->setAttrib('id', 'item-form');
        $this->setAttrib('class', 'form-validate');
        $this->setName('adminForm');

        $this->setMethod('post');

		// title text field
		$name = $this->createElement('text', 'name');
		$name->setLabel('Name');
		$name->setAttrib('size', 80);
		$name->setRequired(true);
		$this->addElement($name);

        // id hidden field
        $id = $this->createElement('hidden', 'id');
        $this->addElement($id);

 		// submit button
		$this->addElement('submit', 'submit', array('label' => 'Submit', 'order' => 999) );
    }
}