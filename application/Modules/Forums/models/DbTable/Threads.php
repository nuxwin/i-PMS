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
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Forums_Model_DbTable_Threads extends Zend_Db_Table_Abstract
{
	/**
     * Database table to operate
     *
     * @var string
     */
    protected $_name = 'forums_threads';

    /**
     * Primary key
     *
     * @var string
     */
    protected $_primary = 'tid';


    /**
     * Table relations
     *
     * @var array
     */
    protected $_referenceMap = array(
        'threads' => array(
            SELF::COLUMNS => 'fid', // What is the colonne that reference ?
            SELF::REF_TABLE_CLASS => 'Forums_Model_DbTable_Forums',
            SELF::REF_COLUMNS => 'fid', // Whate is the referenced column in the forums table ?
        )
    );
}
