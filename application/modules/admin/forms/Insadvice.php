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
 *
 * @author miholeus
 */
class Admin_Form_Insadvice extends Admin_Form_Template_Simple
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

        // date field
        $date = $this->createElement('text', 'date');
        $date->setLabel('Date');
        $date->setAttrib('size', 80);
        $date->setRequired(true);
        $this->addElement($date);

        // teaser field
        $teaser = $this->createElement('textarea', 'teaser');
        $teaser->setLabel('Teaser');
        $teaser->setAttrib('cols', 60);
        $teaser->setAttrib('rows', 3);
        $this->addElement($teaser);

        // text field
        $content = new Soulex_Form_Element_TinyMce('text');
        $this->removeDecorators($content);
        $content->setAttrib('style', 'width: 100%; height: 300px;');
        $content->setOptions(array(
            'label'      => 'Text: ',
            'mode'       => 'exact',
            'elements' => 'text',
            'editorOptions' => new Zend_Config_Ini(APPLICATION_PATH . '/configs/tinymce.ini', 'administrator')
        ));
		$this->addElement($content);

        // file field
        $file = $this->createElement('text', 'file');
        $file->setLabel('File');
        $file->setAttrib('size', 80);
        $this->addElement($file);

        // cat_id field
        $catId = $this->createElement('select', 'cat_id');
        $catId->setLabel("Select a category:");
        $catId->setAttrib('class', 'checklist');
        $this->addElement($catId);

        // user_id field
        $userId = $this->createElement('select', 'user_id');
        $userId->setLabel("Select a User:");
        $userId->setAttrib('class', 'checklist');
        $this->addElement($userId);

        // draft field
        $draft = $this->createElement('radio', 'draft');
        $draft->setLabel('Draft');
        $draft->addMultiOption(0, 'Yes');
        $draft->addMultiOption(1, 'No');
        $this->addElement($draft);

        // id hidden field
        $id = $this->createElement('hidden', 'id');
        $this->addElement($id);

 		// submit button
		$this->addElement('submit', 'submit', array('label' => 'Submit', 'order' => 999) );
    }
    /**
     * Add Options to Select element
     *
     * @param string $name of select element
     * @param array $array
     * @param array $keyValue contains key-value pairs used to populate
     * select element
     */
    public function addOptions($name, $array, $keyValue)
    {
        $select = $this->getElement($name);
        foreach($array as $option) {
            $key = $option[$keyValue['key']];
            $value = '';
            if(is_array($keyValue['value'])) {
                foreach($keyValue['value'] as $val) {
                    $value .= $option[$val] . ' ';
                }
            } else {
                $value .= $option[$keyValue['value']];
            }
            $select->addMultiOption($key, $value);
        }
    }
}