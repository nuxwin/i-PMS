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
 * @package     iPMS_Models
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Access Control List model class (General rules)
 *
 * @category    iPMS
 * @package     iPMS_Models
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class Model_Acl extends Zend_Acl
{
	/**
	 * Singleton instance
	 *
	 * @var Model_Acl
	 */
	protected static $_instance = null;

	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 * @return void
	 */
    protected function __construct()
    {
	    // defines three base roles - "guest", "member", and "admin" - from which other roles may inherit.
		$this->addRole(new Zend_Acl_Role('guest'))
			->addRole(new Zend_Acl_Role('subscriber'), 'guest')
			->addRole(new Zend_Acl_Role('admin'), 'subscriber');

	    $this->add(new Zend_Acl_Resource('posts'));
		$this->add(new Zend_Acl_Resource('comments'), 'posts');

		// view permissions for guest and member

		$this->allow('guest', 'posts', 'view');
		$this->allow('guest', 'comments', 'index');
		$this->allow('guest', 'comments', 'add');
		$this->allow('subscriber', 'comments', 'add');
		//$this->allow('subscriber', 'comments', 'edit', new Model_Comments_Acl_Assert(););

		// All permissions for admin
	    $this->allow('admin');
    }

	/**
	 * Singleton pattern implementation makes "clone" unavailable
	 *
	 * @return void
	 */
	private function __clone()
	{}

	/**
	 * Returns an instance of Model_Acl
	 *
	 * Singleton pattern implementation
	 * 
	 * @static
	 * @return Model_Acl
	 */
	public static function getInstance()
	{
		if(null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}
