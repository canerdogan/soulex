<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of Feedback
 *
 * @author miholeus
 */
class Frontend_Form_Feedback extends Zend_Form
{
    public function init()
    {
        $this->setAttrib('id', 'contact');
        $this->setAttrib('name', 'sendform');
        $this->setMethod('post');
        // name
        $name = $this->createElement('text', 'name');
        $name->setRequired(true);
        $name->setLabel('Ф.И.О.');
        $name->addErrorMessage('Поле Ф.И.О обязательно для заполнения!');
        $name->addFilter('StripTags');
        $this->addElement($name);
        // email
        $email = $this->createElement('text', 'email');
        $email->setRequired(true);
        $email->setLabel('E-mail');
        $email->addErrorMessage('Поле E-mail обязательно для заполнения');
        $email->addFilter('StripTags');
        $this->addElement($email);
        // tel
        $tel = $this->createElement('text', 'tel');
        $tel->setLabel('Телефон');
        $tel->addFilter('StripTags');
        $this->addElement($tel);
        // theme
        $theme = $this->createElement('text', 'theme');
        $theme->setRequired(true);
        $theme->setLabel('Тема вопроса');
        $theme->addErrorMessage('Поле Тема сообщения обязательно для заполнения');
        $theme->addFilter('StripTags');
        $this->addElement($theme);
        // text
        $text = $this->createElement('textarea', 'text');
        $text->setRequired(true);
        $text->setLabel('Сообщение');
        $text->addErrorMessage('Поле Сообщение обязательно для заполнения');
        $text->setAttrib('rows', 5);
        $text->addFilter('StripTags');
        $this->addElement($text);
        // Add a captcha
        $this->addElement('captcha', 'captcha', array(
            'label'      => 'Пожалуйста, введите символы, изображенные ниже:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
        // submit button
		$this->addElement('submit', 'submit', array('label' => 'Отправить') );
        // add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}