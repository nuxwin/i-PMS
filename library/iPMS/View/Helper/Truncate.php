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
 * Truncate view Helper
 *
 * This view helper truncates a string
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  View_Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_View_Helper_Truncate extends Zend_View_Helper_Abstract
{

    /**
     * Truncate string
     *
     * @param   string $str string to be truncated
     * @param   int $maxLength max string length
     * @param   string $replacement replacement for string
     * @param   bool $atSpace whether string must be truncated at space
     * @return  string truncated string
     */
    public function truncate($str, $maxLength = 30, $replacement = '...', $atSpace = true)
    {
        $maxLength -= strlen($replacement);
        $stringLength = strlen($str);

        if ($stringLength <= $maxLength) {
            return $str;
        } elseif($atSpace && ($space_position = strrpos($str, ' ', $maxLength - $stringLength))) {
            $maxLength = $space_position;
        }

        return substr_replace($str, $replacement, $maxLength);
    }
}
