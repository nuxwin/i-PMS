<?php
/**
 * i-PMS - internet Project Management System
 * Copyright (C) 2011 by Laurent Declercq
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package     iPMS
 * @subpackage  Forum
 * @category    Forms
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Threads form
 *
 * @package     iPMS
 * @subpackage  Forum
 * @category    Forms
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class Forum_Form_Thread extends Zend_Form
{
	/**
	 * Sets form to build
	 *
	 * @throws iPMS_Exception
	 * @param  $name name of form to build
	 * @return void
	 */
	public function setForm($name)
	{
		$method = strtolower($name) . 'Form';

		if (method_exists($this, $method)) {
			call_user_func_array(array($this, $method), array());
		} else {
			throw new iPMS_Exception(sprintf("Methods 'Forum_Form_Thread::%s' doesn't exists", $method));
		}

		$this->_setRequiredSuffix();
	}

	/**
	 * Build add form
	 *
	 * @return void
	 */
	protected function addForm()
	{
		$this->setName('threadForm')
			->setElementsBelongTo('threadForm');

		$element = new Zend_Form_Element_Text('subject');
		$element->setLabel('Thread Subject')
			->setAttribs(array('class' => 'input-title', 'maxlength' => 130))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$this->addElement($element);

		$element = new Zend_Form_Element_Textarea('body');
		$element->setLabel('Your Message')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$this->addElement($element);

		$element = new Zend_Form_Element_Submit('forumSubmit');
		$element->setLabel('Submit');
		$this->addElement($element);
	}

	/**
	 * Build replyForm
	 *
	 * @return void
	 */
	protected function replyForm()
	{

		$this->setName('replyForm')
			->setElementsBelongTo('replyForm');

        $element = new Zend_Form_Element_Text('subject', array('disableLoadDefaultDecorators' => true));
        $element->addDecorator('ViewHelper')
	        ->setAttribs(array('class' => 'input-title', 'maxlength' => 126, 'tabindex' => 1))
	        ->addFilter('StripTags')
	        ->addFilter('StringTrim');
		$this->addElement($element);

		$element = new Zend_Form_Element_Textarea('body', array('disableLoadDefaultDecorators' => true));
		$element->addDecorator('ViewHelper')
			->setRequired(true)
			->addErrorMessage('Message cannot be empty!')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');
		$this->addElement($element);

		$element = new Zend_Form_Element_Hash('token', array('disableLoadDefaultDecorators' => true));
		$element->addDecorator('ViewHelper')
			->addErrorMessage('Form must not be resubmitted');
		$this->addElement($element);

		$element = new Zend_Form_Element_Submit('replySubmit', array('disableLoadDefaultDecorators' => true));
		$element->addDecorator('ViewHelper')
			->setLabel('Post Reply');
		$this->addElement($element);

		$element = new Zend_Form_Element_Reset('reset', array('disableLoadDefaultDecorators' => true));
		$element->addDecorator('ViewHelper')
			->setLabel('Reset');
		$this->addElement($element);

        $this->clearDecorators();
		$this->addDecorator('FormElements')
	         ->addDecorator('Form');

		/*
		$this->setName('replyForm')
			->setElementsBelongTo('replyForm')
			->setMethod('post')
			->setEnctype('application/x-www-form-urlencoded');

		$subject = new Zend_Form_Element_Text('subject');
		$subject->setLabel('Post Subject')
			->setAttribs(array('class' => 'input-title', 'maxlength' => 126))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Your Message')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$posthash = new Zend_Form_Element_Hash('token');

		$submit = new Zend_Form_Element_Submit('forumSubmit');
		$submit->setLabel('Post Reply');

		$this->addElements(array($subject, $message, $posthash, $submit));
		 */
	}
}
