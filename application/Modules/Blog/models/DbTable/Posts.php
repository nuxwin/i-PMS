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
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Posts model class
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Blog_Model_DbTable_Posts extends Zend_Db_Table_Abstract
{
	/**
	 * Database table to operate
	 *
	 * @var string
	 */
	protected $_name = 'posts';

	/**
	 * Primary key
	 *
	 * @var string
	 */
	protected $_primary = 'pid';

	protected $_dependentTables = array(
		'Comment_Model_DbTable_Comments'
	);

	/**
	 * Table relations
	 *
	 * @var array
	 */
	protected $_referenceMap = array(
		'User' => array(
			SELF::COLUMNS => 'uid',
			SELF::REF_TABLE_CLASS => 'User_Model_DbTable_Users',
			SELF::REF_COLUMNS => 'uid',
			SELF::ON_DELETE => SELF::SET_NULL
		)
	);

	/**
	 * Returns a pageable list of posts without post body
	 *
	 * @param int $currentPage Current page
	 * @param int $itemsCount Item count per page
	 * @return Zend_Paginator
	 */
	public function getPageablePostsList($currentPage = 1, $itemsCount = 1)
	{
		$subSelect = $this->getAdapter()->select()
			->from('comments', new Zend_Db_Expr('COUNT(pid)'))
			->where('comments.pid = posts.pid');

		$select = $this->getAdapter()->select()
			->from('posts', array(
			                     'pid', 'uid', 'title', 'teaser', 'categorie', 'created_on',
			                     'comments_count' => new Zend_Db_Expr("($subSelect)")))
			->joinLeft('users', 'users.uid = posts.uid', array('username', 'firstname', 'lastname'))
			->order('posts.pid DESC');

		$pageablePosts = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$pageablePosts->setItemCountPerPage((int) $itemsCount);
		$pageablePosts->setCurrentPageNumber((int) $currentPage);

		return $pageablePosts;
	}

	/**
	 * Returns a pageable list of posts
	 *
	 * @param int $currentPage Current page
	 * @param int $itemsCount Item count per page
	 * @return Zend_Paginator
	 */
	public function getPageablePosts($currentPage = 1, $itemsCount = 15)
	{
		$pageablePosts = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect(
			$this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
				->setIntegrityCheck(false)
				->from('posts', array('pid', 'title', 'teaser', 'body', 'categorie', 'created_on', 'uid'))
				->joinLeft('users', 'users.uid = posts.uid', array('username', 'firstname', 'lastname'))
				->order('posts.pid DESC')
		));

		$pageablePosts->setItemCountPerPage((int) $itemsCount);
		$pageablePosts->setCurrentPageNumber((int) $currentPage);

		return $pageablePosts;
	}
}
