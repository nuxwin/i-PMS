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
 * Forum form
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Forum_Form_Forum extends Zend_Form
{

	/**
	 * Form initialization
	 *
	 * @return void
	 */
	public function init()
	{
		$this->setName('forumForm')
			->setAction('/forums/add')
			->setElementsBelongTo('forumForm')
			->setMethod('post')
			->setEnctype('application/x-www-form-urlencoded');

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Title')
			->setAttribs(array('class' =>'input-title', 'maxlength' => 130))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description')
			->setAttribs(array('class' => 'input-teaser', 'maxlength' => 255))
			//->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
			//->addValidator('NotEmpty');


		$order = new Zend_Form_Element_Text('order');
		$order->setLabel('Display Order')
			->setAttrib('maxlength', 5)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('Int')
			->addValidator('NotEmpty')
			->setValue('1');

		$submit = new Zend_Form_Element_Submit('forumSubmit');
		$submit->setLabel('Submit');

		$this->addElements(array($name, $description, $order, $submit));
	}
}
