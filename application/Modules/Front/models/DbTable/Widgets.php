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
 * Model for widgets
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
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
     * Returns widgets options
     *
     * @param bool $onlyActive tells whether only actives widgets must be fetched
     * @return array
     */
    public function getOptions($onlyActives = false)
    {
        $select = $this->select();
        $select->from($this, 'options');

        if($onlyActives) {
            $select->where('is_active = ?', 1, Zend_Db::INT_TYPE);
        }

        $options = $select->query()->fetchAll(Zend_DB::FETCH_COLUMN);

        return array_map('unserialize', $options);
    }

    /**
     * Updates existing rows
     *
     * Override parent method to serialize widget options if present
     *
     * @param  array $data  column-value pairs.
     * @param  array|string $where an SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int The number of rows updated.
     */
    public function update(array $data, $where)
    {
        if(array_key_exists('options', $data)) {
            $data['options'] = serialize($data['options']);
        }

        return parent::update($data, $where);
    }

}
