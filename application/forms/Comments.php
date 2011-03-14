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
 * @author      Laurent Declercq <laurent.declercq@i-mscp.net>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Comments form
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Form_Comments extends Zend_Form
{

    /**
     * Initialize comment form
     *
     * @return void
     */
    public function init()
    {
        $this->setName('commentsFrm');
        $this->setAction('comments/add');

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
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator('NotEmpty');

            $this->addElements(array($name, $email, $website));
        }

        // Comment field
        $comment = new Zend_Form_Element_Textarea('body');
        $comment->setLabel('Comment')
                ->setRequired(true)
                ->setAttrib('rows', 7)
                ->setAttrib('cols', 30)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        // Submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Comment')->setAttribs(array('id' => 'comment-submit'));

        // Add elements to the form
        $this->addElements(array($comment, $submit));
    }

}
