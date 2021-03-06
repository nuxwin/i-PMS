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
 * @package     iPMS
 * @subpackage  Comment
 * @category    Bootstrap
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Comment module bootstrap class
 *
 * @package     iPMS
 * @subpackage  Comment
 * @category    Bootstrap
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class Comment_Bootstrap extends Zend_Application_Module_Bootstrap
{
	/**
	 * Init routes
	 *
	 * @return void
	 */
	public function _initRoutes()
	{
		/**
		 * @var $router Zend_Controller_Router_Rewrite
		 */
		$router = $this->getApplication()->getResource('Router');
		$router->addConfig(new Zend_Config_Ini(__DIR__ . '/configs/routes.ini'));
	}
}
