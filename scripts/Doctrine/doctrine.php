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

$configFile = getcwd() . DIRECTORY_SEPARATOR . 'cli-config.php';

if (file_exists($configFile)) {
    if ( ! is_readable($configFile)) {
        trigger_error('Configuration file [' . $configFile . '] does not have read permission.', E_USER_ERROR);
    }

    require $configFile;
} else {
	trigger_error(
		'Configuration file [' . $configFile . '] was not found.', E_USER_ERROR
	);
}

$helperSet = null;
foreach ($GLOBALS as $helperSetCandidate) {
	if ($helperSetCandidate instanceof \Symfony\Component\Console\Helper\HelperSet) {
		$helperSet = $helperSetCandidate;
		break;
	}
}

// Run console
\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet);
