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
 * Comments form
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Blog_Form_Comments extends Zend_Form
{

	/**
	 * Initialize comment form
	 *
	 * @return void
	 */
	public function init()
	{
		$this->setName('commentsForm');
		$this->setAction('comment/add');

		if (!Zend_Auth::getInstance()->hasIdentity()) {
			// Name field
			$name = new Zend_Form_Element_Text('name');
			$name->setLabel('Name')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

			// Email field
			$email = new Zend_Form_Element_Text('email');
			$email->setLabel('Email')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

			// Website field
			$website = new Zend_Form_Element_Text('website');
			$website->setLabel('WebSite')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

			$this->addElements(array($name, $email, $website));
			$this->_setRequiredSuffix();
		}

		// Comment field
		$comment = new Zend_Form_Element_Textarea('body');
		$comment->setLabel('Comment')
			->setAttrib('rows', 7)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Add comment')->setAttribs(array('id' => 'comment-submit'));

		// Add elements to the form
		$this->addElements(array($comment, $submit));
	}

	/**
	 * Sets required suffix for all required elements
	 *
	 * @return void
	 */
	protected function _setRequiredSuffix()
	{
		foreach ($this->getElements() as $element) {
			if ($element->isRequired()) {
				$element->addDecorator('Label', array('tag' => 'dt',
				                                     'escape' => false,
				                                     'requiredSuffix' => ' <span>*</span>'));
			}
		}
	}
}
