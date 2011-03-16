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
 * @package     iPMS_Widgets
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Widget that display a login form
 *
 * @category    i-PMS
 * @package     Widgets
 * @subpackage  Widgets_Login
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class Widget_Login_Login extends iPMS_Widget
{

    /**
     * Make widget content available for the view
     *
     * Implements {@link iPMS_Widget_Interface::widget()}
     *
     * @return string Login form
     */
    public function widget()
    {
        $auh = Zend_Auth::getInstance();

        if (!$auh->hasIdentity()) {
            $request = $this->getRequest();
            if ($request->isPost() && is_array($request->getPost('login'))) {
                $form = $this->_getForm();
                if ($form->isValid($request->getPost())) {
                    // Perform authentication against database
                    $userModel = new Model_DbTable_Users();
                    $userModel->setIdentity($form->getValue('username'))
                            ->setCredential($form->getValue('password'));
                    $authResult = $auh->authenticate($userModel);
                    if ($authResult->isValid()) {
                        Zend_Session::regenerateId(); // Protection against session's fixation attacks
                        return '';
                    }
                }
            }

            return $this->_getForm()->render();
        }

        return '';
    }

    /**
     * Widget dashboard settings form
     *
     * Implements {@link iPMS_Widget_Interface::dashboard()}
     *
     * @return void
     */
    public function dashboard()
    {
        //return $this->buildDashboardSettingsForm($this->getParams())->render();
    }

    /**
     * Returns HTML login form
     *
     * @return Zend_Form
     */
    protected function _getForm()
    {
        $form = new Form_Login();
        $form->setElementsBelongTo('login');

        return $form;
    }

    /**
     * Update widget options (either widget property or parameter)
     *
     * Implements {@link iPMS_Widget_Interface::update()}
     *
     * @return void
     */
    public function update()
    {

    }
}
