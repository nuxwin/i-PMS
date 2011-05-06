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
 * Check PHP version (5.3.0 or newer )
 */
if (version_compare(phpversion(), '5.3.0', '<') === true) {
	die('Error: Your PHP version is ' . phpversion() . ". i-MSCP requires PHP 5.3.0 or newer.\n");
}

defined('SERVER_NAME') || define('SERVER_NAME', $_SERVER['SERVER_NAME']);
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__)));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
defined('THEME_PATH') || define('THEME_PATH', realpath(dirname(__FILE__) . '/themes'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(ROOT_PATH . '/library', get_include_path())));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$app = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$app->bootstrap()->run();
