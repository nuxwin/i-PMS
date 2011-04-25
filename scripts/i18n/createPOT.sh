#!/bin/sh
#
# i-PMS - internet Project Management System
# Copyright (C) 2011 by Laurent Declercq
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
#
# @category    iPMS
# @package     iPMS_Scripts
# @subpackage  i18n
# @copyright   2011 by i-PMS | http://i-pms.net
# @author      Laurent Declercq <laurent.declercq@i-mscp.net>
# @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2

################################################################################
# Script short description
#
# This script create a new POT file by retrieving all messages from iPMS files 
# *.php, *.phtml and *.xml files)
#
# Note to developers:
#
# I. PHP and PHTML files:
#
# When you add translation strings in files, don't forget to check that the
# directories that contain your files are correctly listed in the xgettext
# command of the PHP Section bellow.
#
# II. XML files
#
# When you add translation strings in files, don't forget to check that the
# directories that contain your files are correctly listed in the xgettext
# command of the XML Section bellow. Also, don't forget to add all needed
# keywords.
#
#
########################################################################################################################

set -e

LANGUAGE_DIRECTORY="../../application/languages"
APPLICATION_DIRECTORY="../../application"
THEMES_DIRECTORY="../../themes"

# PHP Section
/usr/bin/xgettext --language=PHP \
-d "i-PMS" \
--keyword="setLabel" \
--keyword="translate" \
--keyword="plural:1,2" \
${APPLICATION_DIRECTORY}/Modules/*/*/*.php \
${APPLICATION_DIRECTORY}/Modules/*/models/DbTable/*.php \
${THEMES_DIRECTORY}/*/templates/*/*.phtml \
${THEMES_DIRECTORY}/*/templates/modules/*/scripts/*/*.phtml \
--from-code=utf-8 \
-p ${LANGUAGE_DIRECTORY}/po \
-o "iPMS.pot"

# XML Section
#/usr/bin/xgettext --language=Glade \
#--package-name="i-PMS" \
#--package-version="0.0.1" \
#--copyright-holder="i-PMS translation Team" \
#--msgid-bugs-address="i18n@i-mscp.net" \
#-d "i-PMS" \
#--keyword="label" \
#--keyword="plural:1,2" \
#${APPLICATION_DIRECTORY}/configs/menus/*.xml \
#--from-code=utf-8 \
#--no-wrap \
#-p ${LANGUAGE_DIRECTORY}/po \
#-o "iPMS.pot" -j -s

exit 0
