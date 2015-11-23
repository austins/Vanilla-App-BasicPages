<?php defined('APPLICATION') or exit();
/**
 * Copyright (C) 2013  Austin S.
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// An associative array of information about this application.
$ApplicationInfo['BasicPages'] = array(
    'Name' => 'Basic Pages',
    'Description' => "Basic Pages is an application that provides a way for you to create basic public pages for static content in Garden.",
    'Version' => '2.1.6',
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'Author' => "Austin S.",
    'AuthorUrl' => 'https://github.com/austins',
    'Url' => 'http://vanillaforums.org/addon/basicpages-application',
    'License' => 'GNU GPL2',
    'SetupController' => 'setup',
    'SettingsUrl' => 'pagessettings/allpages'
);
