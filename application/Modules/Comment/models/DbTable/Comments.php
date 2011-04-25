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
 * Comments Model class
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Comment_Model_DbTable_Comments extends Zend_Db_Table_Abstract
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
    protected $_primary = 'cid';

    /**
     * Table relations
     *
     * @var array
     */
    protected $_referenceMap = array(
        // If the parent post is deleted, all related comments are deleted too
        'Post' => array(
            SELF::COLUMNS => 'pid',
            SELF::REF_TABLE_CLASS => 'Blog_Model_DbTable_Posts',
            //SELF::REF_COLUMNS => 'pid',
            SELF::ON_DELETE => SELF::CASCADE
        ),
        // If the  author account is deleted, we set all his comments ('FK') to null (user not registered)
        'User' => array(
            SELF::COLUMNS => 'uid', // FK
            SELF::REF_TABLE_CLASS => 'Model_DbTable_Users', // Parent table
            //SELF::REF_COLUMNS => 'uid', // PK
            SELF::ON_DELETE => SELF::SET_NULL
        ),
    );

    /**
     * Retrieves all comments that belong to one object
     *
     * @param  $parent Zend_Db_Table_Row_Abstract
     * @return Zend_Db_Table_Rowset_Abstract Query result from $dependentTable
     */
    public function getComments($pid)
    {
	    $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
		    ->setIntegrityCheck(false)
		    ->where('pid = ?', $pid, Zend_Db::INT_TYPE)
	        ->joinLeft('users', '`users`.`uid` = `comments`.`uid`', 'avatar')
		    ->order('pid DESC');

	    return $this->fetchAll($select)->toArray();
    }
}
