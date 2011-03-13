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
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Login form
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Form_Login extends Zend_Form
{

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
	$this->setName('loginFrm');

	$redirect = new Zend_Form_Element_Hidden('redirect');
	$fc = Zend_Controller_Front::getInstance();
	$redirect->setValue($fc->getRequest()->getRequestUri())
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');

	$username = new Zend_Form_Element_Text('username');
	$username->setLabel('Username')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');

	$password = new Zend_Form_Element_Password('password');
	$password->setLabel('Password')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');

	$submit = new Zend_Form_Element_Submit('submit');
	//$redirect = new Zend_Form_Element_Hidden('redirect');

	$submit->setAttrib('id', 'submit')
		->setLabel('Connection');

	$this->addElements(array($username, $password, $redirect, $submit));
    }

}
