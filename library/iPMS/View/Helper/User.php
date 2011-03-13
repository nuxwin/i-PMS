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
 * User view Helper
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  View_Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_View_Helper_User extends Zend_View_Helper_Abstract
{

    /**
     * Identity
     *
     * @var object|null
     */
    protected $_auth = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
	$this->_auth = Zend_Auth::getInstance()->getIdentity();
	if (null == $this->_auth) {
	    $this->_auth = 'guest';
	}
    }

    /**
     * Return user helper
     *
     * @return iPMS_View_Helper_UserIdentity
     */
    public function User()
    {
	return $this;
    }

    /**
     * Whether the user is guest
     *
     * @return bool
     */
    public function isGuest()
    {
	return ($this->_auth == 'guest');
    }

    /**
     * Whether the user is logged
     *
     * @return bool
     */
    public function isLogged()
    {
	return (!$this->isGuest());
    }

    /**
     * Returns identity property
     *
     * @param  $name Identity property name
     * @return string|null identity property or null
     */
    public function __get($name)
    {
	if (is_object($this->_auth) && isset($this->_auth->{$name})) {
	    return $this->_auth->$name;
	}
	return null;
    }

    /**
     * String representation
     *
     * @return string User Username
     */
    public function __toString()
    {
	if (is_object($this->_auth)) {
	    return$this->_auth->username;
	}

	return 'guest';
    }

}
