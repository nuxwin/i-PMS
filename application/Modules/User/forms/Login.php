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
 * Login form
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class User_Form_Login extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
	    $this->setName('loginForm');
	    $this->setElementsBelongTo('loginForm');

	    $element = new Zend_Form_Element_Text('username');
	    $element->setLabel('Username')
		    ->setRequired(true)
		    ->addFilter('StripTags')
		    ->addFilter('StringTrim');
	    $this->addElement($element);

	    $element = new Zend_Form_Element_Password('password');
	    $element->setLabel('Password')
		    ->setRequired(true)
		    ->addFilter('StripTags')
		    ->addFilter('StringTrim');
	    $this->addElement($element);

	    $element = new Zend_Form_Element_Checkbox('rememberMe');
	    $element->setLabel('Remember me');
	    $this->addElement($element);


	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = Zend_Controller_Front::getInstance()->getRequest();

	    $element = new Zend_Form_Element_Hidden('redirect');
	    $element->setValue($request->getParam('from', '/'))
		    ->setRequired(true)
		    ->addFilter('StripTags')
		    ->addFilter('StringTrim');
	    $element->getDecorator('HtmlTag')->setOption('class', 'hidden');
	    $element->getDecorator('Label')->setOption('tagClass', 'hidden');
	    $this->addElement($element);

	    $element = new Zend_Form_Element_Submit('submit');
	    $element->setLabel('Connection');
	    $this->addElement($element);
    }
}
