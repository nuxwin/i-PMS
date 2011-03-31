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
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Account controller
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class AccountController extends Zend_Controller_Action
{

    /**
     * User identity
     *
     * @var string
     */
    protected $_identity = null;
    /**
     * Credential
     *
     * @var string
     */
    protected $_credential = null;

    /**
     * Log-in request and validation
     *
     * @return void
     */
    public function loginAction()
    {
        $form = new Form_Login();

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $this->_identity = $form->getValue('username');
            $this->_credential = $form->getValue('password');

            if ($this->_authenticateUser()) {
                $this->_redirect($form->getValue('redirect'));
            }
        }

        $this->view->assign('loginFrm', $form);
    }

    /**
     * Log out user and redirect to front page
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->_flushIdentity();
        $this->_redirect('/');
    }

    /**
     * Enable user to choose a new password
     *
     * @return void
     */
    public function lostPasswordAction()
    {
        // Todo not yet implementted
    }

    /**
     * User self-registration
     *
     * @return void
     */
    public function registerAction()
    {
        // Todo not yet implementted
    }

    /**
     * @return void
     */
    public function activate()
    {
        // Todo not yet implementted
    }

    /**
     * Flush user identity data
     *
     * @return void
     */
    protected function _flushIdentity()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * Authenticate an user
     *
     * @return void
     */
    protected function _authenticateUser()
    {
        if ($this->isOpenIdAuthentication()) {
            return $this->_openIdAuthentication();
        } else {
            return $this->_passwordAuthentication();
        }
    }

    /**
     * OpenId authentication
     *
     * @return void
     */
    protected function _openIdAuthentication()
    {
        // Todo not yet implemented
    }

    /**
     * Tells whether or not it's a query for an openId authentication
     *
     * @return bool TRUE if it's openId authentication, FALSE otherwise
     */
    protected function isOpenIdAuthentication()
    {
        if (($this->_request->getParam('openid_action', '*') == 'login')) {
            return true;
        }

        return false;
    }

    /**
     * Authentication against local password
     *
     * @return bool TRUE on authentication successful, FALSE otherwise
     */
    protected function _passwordAuthentication()
    {
        $authDbAdapter = new Zend_Auth_Adapter_DbTable(null,'users', 'username', 'password', 'MD5(?) AND active = 1');
        $authDbAdapter->setIdentity($this->_identity)->setCredential($this->_credential);
        $result = $authDbAdapter->authenticate();

        if (!$result->isValid()) {
            $this->_invalidCredentials($result);
            return false;
        } else {
            $this->_successfulAuthentication($authDbAdapter);
            return true;
        }
    }

    /**
     * Store identity and set successful authentication message
     *
     * @param  Zend_Auth_Adapter_DbTable $adapter authentication adapter
     * @return void
     */
    protected function _successfulAuthentication(Zend_Auth_Adapter_DbTable $adapter)
    {
        // Protection against session's fixation attacks
        Zend_Session::regenerateId();

        Zend_Auth::getInstance()->getStorage()->write($adapter->getResultRowObject(null, 'password'));

        // Todo send autologin
        //if($this->_request->getParam('autologin', false)) {
        //	$token = Model_Token::create($user);
        //}
        //echo strtotime("now +1 year");
        //if params[:autologin] && Setting.autologin?
        //	token = Token.create(:user => user, :action => 'autologin')
        //	cookies[:autologin] = { :value => token.value, :expires => 1.year.from_now }
        //end

        return true;
    }

    /**
     * Set error message
     *
     * @param Zend_Auth_Result $authenticationResult
     * @return void
     */
    protected function _invalidCredentials(Zend_Auth_Result $authResult)
    {
        $messages = $authResult->getMessages();
        $this->view->errorMessage = $messages[0];
        // Log -> "Failed login for '#{params[:username]}' from #{request.remote_ip} at #{Time.now.utc}";
        // Flash error -> invalid credential
    }

}
