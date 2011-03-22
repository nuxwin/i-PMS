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
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Model for widgets
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Model_DbTable_Widgets extends Zend_Db_Table_Abstract
{

    /**
     * Database table to operate on
     *
     * @var string
     */
    protected $_name = 'widgets';

    /**
     * Primary key
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns options of all active widgets
     *
     * @return array
     */
    public function getActiveWidgetsOptions()
    {
        $select = $this->select();
        $select->from($this, 'options')
               ->where('is_active = ?', 1, Zend_Db::INT_TYPE);

        $options = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $options = array_map('unserialize', $options);

        return $options;
    }
}
