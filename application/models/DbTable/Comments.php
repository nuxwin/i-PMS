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
 * Comments Model class
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Model_DbTable_Comments extends Zend_Db_Table_Abstract implements Zend_Acl_Resource_Interface, Zend_Acl_Assert_Interface
{
	/**
	 * Database table to operate
	 *
	 * @var string
	 */
	protected $_name = 'comments';

	/**
	 * Primary key
	 *
	 * @var string
	 */
	protected $_primary = 'id';

	/**
	 * Table relations
	 * 
	 * @var array
	 */
	protected $_referenceMap = array(

		// If the parent post is deleted, all related comments are deleted too
		'Post' => array(
			SELF::COLUMNS           => 'post_id',
			SELF::REF_TABLE_CLASS   => 'Model_DbTable_Posts',
			SELF::REF_COLUMNS       => 'id',
			SELF::ON_DELETE         => SELF::CASCADE
		),

		// If the  author account is deleted, we set all his comments ('FK') to null (user not registered)
		'user' => array(
			SELF::COLUMNS           => 'author_id',
			SELF::REF_TABLE_CLASS   => 'Model_DbTable_Users',
			SELF::REF_COLUMNS       => 'id',
			SELF::ON_DELETE         => SELF::SET_NULL
		),
	);

	/**
	 * Resource owner identifier
	 * @var int
	 */
	protected $_resourceOwnerId = null;

	/**
	 * Resource string identifier
	 * @var string
	 */
	protected $_resourceId = 'comment';


	/**
	 * Retrieves all comments that belong to one object
	 *
	 * @param  $parent Zend_Db_Table_Row_Abstract
	 * @return Zend_Db_Table_Rowset_Abstract Query result from $dependentTable
	 */
	public function getComments(Zend_Db_Table_Row_Abstract $parent)
	{
		$comments = $parent->findDependentRowset(
			$this, null, $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
				->setIntegrityCheck(false)
				->joinLeft('users', '`users`.`id` = `comments`.`author_id`', 'avatar')
		);

		return $comments;
	}

	/**
	 * Implements Zend_Acl_Resource_Interface
	 *
	 * @return string Resource string identifier
	 */
	public function getResourceId()
	{
		return $this->_resourceId;
	}

    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Zend_Acl                    $acl
     * @param  Zend_Acl_Role_Interface     $role
     * @param  Zend_Acl_Resource_Interface $comment
     * @param  string                      $privilege
     * @return boolean
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $user = null,
                           Zend_Acl_Resource_Interface $comment = null, $privilege = null)
    {
	    if($user->id = $comment->author_id) {
		    return true;
	    } else {
		    return false;
	    }
    }
}
