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
 * Widgets that display a login form
 *
 * @category    i-PMS
 * @package     Widgets
 * @subpackage  Widgets_Login
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class Widgets_Login_Login extends iPMS_Widget
{

    /**
     * Implements {@link iPMS_Widget_Interface::widget()}
     *
     * @param Zend_Controller_Request_Http $request
     * @return Zend_Form|null A Zend_Form instance or null if user is already authenticated
     */
    public function widget(Zend_Controller_Request_Http $request)
    {
        $auh = Zend_Auth::getInstance();

        if(!$auh->hasIdentity()) {
            $form = $this->getForm('loginForm');

            if($request->isPost() && is_array($request->getPost('loginForm')) &&
               $form->isValid($request->getPost('loginForm'))) {

                $userModel = new Model_DbTable_Users();
                $userModel->setIdentity($form->getValue('username'))
                    ->setCredential($form->getValue('password'));
                $authResult = $auh->authenticate($userModel);

                if ($authResult->isValid()) {
                    Zend_Session::regenerateId(); // Protection against session's fixation attacks
                    return null;
                }
            }

            return $form;
        }

        return null;
    }

    /**
     * Implements {@link iPMS_Widget_Interface::dashboard()}
     *
     * @return Zend_Form Dashboard for for setting purpose
     */
    public function dashboard($settings)
    {
        return $this->getForm('dashboardForm');
    }


    /**
     * Implements {@link iPMS_Widget_Interface::update()}
     *
     * @param  array $settings array that contain settings to be updated
     * @param  array $oldSettings array that contains old settings
     * @return array settings to save
     */
    public function update($settings, $oldSettings)
    {
 
    }
}
