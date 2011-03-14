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
 * @package     iPMS_Plugins
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Plugin that check user permissions before dispatching
 */
class Plugin_PermissionsCheck extends Zend_Controller_Plugin_Abstract
{

    /**
     * @var Zend_Auth
     */
    protected $_auth = null;
    /**
     * @var Model_Acl
     */
    protected $_acl = null;
    /**
     * Default role
     *
     * @var string
     */
    protected $_defaultRole = 'guest';

    /**
     * Constructor
     *
     * @param Zend_Auth $auth
     * @param Zend_Acl $acl
     * @return void
     */
    public function __construct(Zend_Auth $auth, Zend_Acl $acl)
    {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

    /**
     * Check permissions before dispatch process
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $resource = $request->getControllerName();
        $action = $request->getActionName();

        if ($this->_auth->hasIdentity()) {
            $identity = $this->_auth->getStorage()->read();
            $role = $identity->role;
        } else {
            $role = $this->_defaultRole;
        }

        if ($this->_acl->has($resource) && !$this->_acl->isAllowed($role, $resource, $action)) {
            $request->setControllerName('error')->setActionName('deny');
        }
    }

    /**
     * Set default role
     *
     * @param  string $role default role
     * @return void
     */
    public function setDefaultRole($defaultRole)
    {
        $this->_defaultRole = (string)$role;
    }

}
