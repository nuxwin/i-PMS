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
 * Users model class
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Model_DbTable_Users extends Zend_Db_Table_Abstract implements Zend_Auth_Adapter_Interface
{

    /**
     * Database table to operate
     *
     * @var string
     */
    protected $_name = 'users';
    /**
     * Primary key
     *
     * @var string
     */
    protected $_primary = 'id';
    /**
     * Dependent database tables
     *
     * @var array
     */
    protected $_dependentTables = array(
	'Model_DbTable_Posts',
	'Model_DbTable_Comments',
	'Model_DbTable_Tokens'
    );
    /**
     * Identity value
     *
     * @var string
     */
    protected $_authIdentity = null;
    /**
     * Credential values
     *
     * @var string
     */
    protected $_authCredential = null;

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Model_DbTable_Users Provides a fluent interface
     */
    public function setIdentity($value)
    {
	$this->_authIdentity = $value;
	return $this;
    }

    /**
     * setCredential() - set the credential value to be used, optionally can specify a treatment
     * to be used, should be supplied in parameterized form, such as 'MD5(?)' or 'PASSWORD(?)'
     *
     * @param  string $credential
     * @return Model_DbTable_Users Provides a fluent interface
     */
    public function setCredential($credential)
    {
	$this->_authCredential = $credential;
	return $this;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.
     *
     * This method is called to attempt an authentication.
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
	$authDbAdapter = new Zend_Auth_Adapter_DbTable($this->getAdapter(),
			$this->_name, 'username', 'password', 'MD5(?)'
	);

	$authDbAdapter->setIdentity($this->_authIdentity)->setCredential($this->_authCredential);
	$result = $authDbAdapter->authenticate($authDbAdapter);

	if ($result->isValid()) {
	    $identity = $authDbAdapter->getResultRowObject(null, 'password');
	    if ($identity->active) {
		$this->update(array('last_login_on' => time()), array('id = ?' => $identity->id));
		$result = new Zend_Auth_Result($result->getCode(), $identity, $result->getMessages());
	    } else {
		$result = new Zend_Auth_Result(
				Zend_Auth_Result::FAILURE, null, array('User is not active!')
		);
	    }
	}

	return $result;
    }

}
