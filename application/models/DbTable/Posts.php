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
 * Post model class
 * 
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Model_DbTable_Posts extends Zend_Db_Table_Abstract
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
    protected $_primary = 'id';
    protected $_dependentTables = array(
	'Model_DbTable_Comments'
    );
    /**
     * Table relations
     *
     * @var array
     */
    protected $_referenceMap = array(
	'User' => array(
	    SELF::COLUMNS => 'author_id',
	    SELF::REF_TABLE_CLASS => 'Model_DbTable_Users',
	    SELF::REF_COLUMNS => 'id',
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
    public function getPageablePostsList($currentPage = 1, $itemsCount = 10)
    {
	$pageablePosts = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect(
					$this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
					->setIntegrityCheck(false)
					->from('posts', array('id', 'author_id', 'title', 'teaser', 'categorie', 'created_at'))
					->join('users', 'users.id = posts.author_id', array('username', 'firstname', 'lastname'))
					->order('posts.id DESC')
		));

	$pageablePosts->setItemCountPerPage($itemsCount);
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
    public function getPageablePosts($currentPage = 1, $itemsCount = 10)
    {
	$pageablePosts = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect(
					$this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
					->setIntegrityCheck(false)
					->from('posts', array('id', 'title', 'teaser', 'body', 'categorie', 'created_at', 'author_id'))
					->join('users', 'users.id = posts.author_id', array('username', 'firstname', 'lastname'))
					->order('posts.id DESC')
		));

	$pageablePosts->setItemCountPerPage($itemsCount);
	$pageablePosts->setCurrentPageNumber((int) $currentPage);

	return $pageablePosts;
    }

}
