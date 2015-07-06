<?php defined('APPLICATION') or exit(); // Make sure this file can't get accessed directly
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

// Use this file to do any database changes for your application.

if (!isset($Drop))
    $Drop = false; // Safe default - Set to TRUE to drop the table if it already exists.

if (!isset($Explicit))
    $Explicit = true; // Safe default - Set to TRUE to remove all other columns from table.

$Database = Gdn::Database();
$SQL = $Database->SQL(); // To run queries.
$Construct = $Database->Structure(); // To modify and add database tables.
$Validation = new Gdn_Validation(); // To validate permissions (if necessary).

/**
 * Column() has the following arguments:
 *
 * @param string $Name Name of the column to create.
 * @param string $Type Data type of the column. Length may be specified in parenthesis.
 *    If an array is provided, the type will be set as "enum" and the array's values will be assigned as the column's enum values.
 * @param string $NullOrDefault Default is FALSE. Whether or not nulls are allowed, if not a default can be specified.
 *    TRUE: Nulls allowed. FALSE: Nulls not allowed. Any other value will be used as the default (with nulls disallowed).
 * @param string $KeyType Default is FALSE. Type of key to make this column. Options are: primary, key, or FALSE (not a key).
 *
 * @see /library/database/class.generic.structure.php
 */

// Construct the Page table.
$Construct->Table('Page');

$Construct
    ->PrimaryKey('PageID')
    ->Column('Name', 'varchar(100)', false, 'fulltext')
    ->Column('UrlCode', 'varchar(255)', false, 'unique')
    ->Column('Body', 'longtext', false, 'fulltext')
    ->Column('Format', 'varchar(20)', true)
    ->Column('DateInserted', 'datetime', false, 'index')
    ->Column('DateUpdated', 'datetime', true)
    ->Column('Sort', 'int', true)
    ->Column('SiteMenuLink', 'tinyint(1)', '0')
    ->Column('ViewPermission', 'tinyint(1)', '0')
    ->Column('InsertUserID', 'int', false, 'key')
    ->Column('UpdateUserID', 'int', true)
    ->Column('InsertIPAddress', 'varchar(15)', true)
    ->Column('UpdateIPAddress', 'varchar(15)', true)
    ->Set($Explicit, $Drop);

// Update procedures from previous versions of Basic Pages.
if (C('BasicPages.Version')) {
    // For Basic Pages installations older than v1.5.
    // Update routes to pages with old expression suffix to new expression suffix.
    if (version_compare(C('BasicPages.Version'), '1.5', '<')) {
        $PageModel = new PageModel();
        $Pages = $PageModel->Get()->Result();

        $OldRouteExpressionSuffix = '(/.*)?$';

        foreach ($Pages as $Page) {
            if (Gdn::Router()->MatchRoute($Page->UrlCode . $OldRouteExpressionSuffix)) {
                Gdn::Router()->DeleteRoute($Page->UrlCode . $OldRouteExpressionSuffix);

                Gdn::Router()->SetRoute(
                    $Page->UrlCode . $PageModel->RouteExpressionSuffix,
                    'page/' . $Page->UrlCode . $PageModel->RouteTargetSuffix,
                    'Internal'
                );
            }
        }
    }
}

// Set current BasicPages.Version everytime the application is enabled.
$ApplicationInfo = array();
include(CombinePaths(array(PATH_APPLICATIONS . DS . 'basicpages' . DS . 'settings' . DS . 'about.php')));
$Version = ArrayValue('Version', ArrayValue('BasicPages', $ApplicationInfo, array()), 'Undefined');
SaveToConfig('BasicPages.Version', $Version);
