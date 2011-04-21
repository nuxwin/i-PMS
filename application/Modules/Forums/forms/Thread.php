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
 * @category    iPMS
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Threads form
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Forums_Form_Thread extends Zend_Form
{
	/**
	 * Form initialization
	 *
	 * @return void
	 */
	public function init()
	{
	}

	/**
	 * Sets form to build
	 *
	 * @throws iPMS_Exception
	 * @param  $name
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
	}

	/**
	 * Build add form
	 *
	 * @return void
	 */
	protected function addForm()
	{
		$this->setName('threadForm')
			->setElementsBelongTo('threadForm')
			->setMethod('post')
			->setEnctype('application/x-www-form-urlencoded');

		$subject = new Zend_Form_Element_Text('subject');
		$subject->setLabel('Thread Subject')
			->setAttribs(array('class' => 'input-title', 'maxlength' => 130))
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

		$submit = new Zend_Form_Element_Submit('forumSubmit');
		$submit->setLabel('Submit');

		$this->addElements(array($subject, $message, $submit));
	}

	/**
	 * Build replyForm
	 *
	 * @return void
	 */
	protected function replyForm()
	{
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

		$posthash = new Zend_Form_Element_Hash('posthash');

		$submit = new Zend_Form_Element_Submit('forumSubmit');
		$submit->setLabel('Post Reply');

		$this->addElements(array($subject, $message, $posthash, $submit));
	}
}
