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

/**
 * class BasicPagesHooks
 *
 * A special function that is automatically run upon enabling this application.
 */
class BasicPagesHooks implements Gdn_IPlugin {
    /**
     * Add pages in the config value array by ID to header site menu.
     */
    public function Base_Render_Before($Sender) {
        if (isset($Sender->Menu) && $Sender->MasterView != 'admin') {
            $PageModel = new PageModel();
            $Pages = $PageModel->GetAllSiteMenuLink()->Result();

            foreach ($Pages as $Page) {
                // Check permission.
                $Permission = false;
                if ($Page->ViewPermission == '1')
                    $Permission = 'BasicPages.' . $Page->UrlCode . '.View';

                // Add link to the menu.
                $Sender->Menu->AddLink('BasicPages-' . $Page->PageID, $Page->Name, PageModel::PageUrl($Page),
                    $Permission, array('class' => 'Page-' . $Page->UrlCode));
            }
        }
    }

    /**
     * Add links to the settings of this application in the admin dashboard.
     */
    public function Base_GetAppSettingsMenuItems_Handler($Sender) {
        $Menu = $Sender->EventArguments['SideMenu'];
        $Section = 'Pages';
        $Namespace = 'pagessettings/';
        $Menu->AddLink($Section, T('BasicPages.Settings.AllPages', 'All Pages'), $Namespace . 'allpages',
            'Garden.Settings.Manage');
        $Menu->AddLink($Section, T('BasicPages.Settings.NewPage', 'New Page'), $Namespace . 'newpage',
            'Garden.Settings.Manage');
    }

    /**
     * If the Garden.PrivateCommunity config setting is enabled,
     * then bypass the sign-in redirection and let the Basic Pages
     * view permission logic handle the redirection for
     * pages requested by guests.
     *
     * @param $Sender Gdn_Dispatcher
     */
    public function Gdn_Dispatcher_BeforeBlockDetect_Handler($Sender) {
        if (C('Garden.PrivateCommunity', false)) {
            $BlockExceptions =& $Sender->EventArguments['BlockExceptions'];
            $PathRequest = Gdn::Request()->Path();
            $PageModel = new PageModel();

            // Handle path requests that match "page/urlcode"
            $PathMatch = 'page/';
            if (substr($PathRequest, 0, strlen($PathMatch)) === $PathMatch) {
                $Page = $PageModel->GetByUrlCode(substr($PathRequest, strlen($PathMatch), strlen($PathRequest)));

                // Only bypass Garden.PrivateCommunity redirection if custom page view permission is enabled.
                if (isset($Page->ViewPermission) && (bool)$Page->ViewPermission)
                    $BlockExceptions['/^page(\/.*)?$/'] = Gdn_Dispatcher::BLOCK_NEVER;
            } else if (!strstr($PathRequest, '/')) {
                // NOTE: Increases overhead every time the Dispatch method is called.
                //       There is room for optimization to be done here.
                //
                // Handle path requests which don't contain a forward slash
                // because the request could possibly be for a page with
                // a path of "urlcode"
                $Page = $PageModel->GetByUrlCode($PathRequest);

                // Only bypass Garden.PrivateCommunity redirection if custom page view permission is enabled.
                if (isset($Page->ViewPermission) && (bool)$Page->ViewPermission)
                    $BlockExceptions['/^' . $PathRequest . '(\/.*)?$/'] = Gdn_Dispatcher::BLOCK_NEVER;
            }
        }
    }

    /**
     * Special function automatically run upon clicking 'Enable' on this application.
     */
    public function Setup() {
        // Variables to be used by structure.php.
        $Database = Gdn::Database();
        $Config = Gdn::Factory(Gdn::AliasConfig);
        $Drop = (Gdn::Config('BasicPages.Version') === false) ? true : false;
        $Explicit = true;

        // Needed by structure.php to validate permission names.
        // $Validation = new Gdn_Validation();

        // Call structure.php to update database.
        include(PATH_APPLICATIONS . DS . 'basicpages' . DS . 'settings' . DS . 'structure.php');
    }

    /**
     * Special function automatically run upon clicking 'Disable' on this application.
     */
    public function OnDisable() {
        // Optional. Delete this if you don't need it.
    }

    /**
     * Special function automatically run upon clicking 'Remove' on this application.
     */
    public function CleanUp() {
        RemoveFromConfig('BasicPages.Version');
    }
}