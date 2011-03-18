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
 * @package     iPMS_Widgets
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Widgets display a RSS feed
 *
 * @category    i-PMS
 * @package     Widgets
 * @subpackage  Widgets_RssFeed
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class Widget_RssFeed_RssFeed extends iPMS_Widget
{

    /**
     * Make widget content available for the view
     *
     * Implements {@link iPMS_Widget_Interface::widget()}
     *
     * @return mixed widget content
     */
    public function widget()
    {

    }

    /**
     * Widgets dashboard settings form
     *
     * Implements {@link iPMS_Widget_Interface::dashboard()}
     *
     * @return void
     */
    public function dashboard()
    {
        //return $this->buildDashboardSettingsForm($this->getParams())->render();
    }

    /**
     * Update widget options (either widget property or parameter)
     *
     * Implements {@link iPMS_Widget_Interface::update()}
     *
     * @return void
     */
    public function update()
    {

    }
}
