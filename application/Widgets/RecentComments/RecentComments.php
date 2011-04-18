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
 * Widgets display the most recent approved comments
 *
 * @category    i-PMS
 * @package     Widgets
 * @subpackage  Widgets_RecentComments
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class Widgets_RecentComments_RecentComments extends iPMS_Widget
{
    /**
     * Make widget content available for the view
     *
     * Implements {@link iPMS_Widget_Interface::widget()}
     *
     * @return array array of comments
     */
    public function widget()
    {
        $model = new Model_DbTable_Comments();
        $comments = $model->fetchAll(null, 'id', 5)->toArray();

        return $comments;
    }

    /**
     * Make the widget settings form available for the dashboard
     *
     * @abstract
     * @param  $settings current widget settings
     * @return void
     */
    public function dashboard($settings)
    {
        // todo add class for specific widget subform treatment.
    }

    /**
     * Update Widgets settings
     *
     * Implements {@link iPMS_Widget_Interface::update()}
     *
     * @param  array $settings array settings to be updated
     * @param  array $oldSettings old settings
     * @return array settings to save
     */
    public function update($settings, $oldSettings)
    {
        // not yet implemented
    }
}
