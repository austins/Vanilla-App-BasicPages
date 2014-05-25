<?php if (!defined('APPLICATION'))
    exit();
/**
 * Basic Pages - An application for Garden & Vanilla Forums.
 * Copyright (C) 2013  Shadowdare
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
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
 * The Page controller.
 */
class PageController extends BasicPagesController {
    /** @var array List of objects to prep. They will be available as $this->$Name. */
    public $Uses = array('PageModel');

    /**
     * Loads default page view.
     *
     * @param string $PageUrlCode ; Unique page URL stub identifier.
     */
    public function Index($PageUrlCode = '') {
        $Page = $this->PageModel->GetByUrlCode($PageUrlCode);

        // Require the custom view permission if it exists.
        // Otherwise, the page is public by default.
        $ViewPermissionName = 'BasicPages.' . $PageUrlCode . '.View';
        if (array_key_exists($ViewPermissionName, Gdn::PermissionModel()->PermissionColumns()))
            $this->Permission($ViewPermissionName);

        // If page doesn't exist.
        if ($Page == null) {
            throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);

            return null;
        }

        // Get page data.
        $this->SetData('PageData', $Page);

        // Add body CSS class.
        $this->CssClass = 'Page-' . $Page->UrlCode;

        if (IsMobile())
            $this->CssClass .= ' PageMobile';

        // VERSION SPECIFIC CODE
        // Remove version_compare conditional when 2.1 becomes required by this app.
        // Runs if Vanilla version is NOT greater than or equal to 2.1b1.
        if (!version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
            $this->CssClass .= ' Page20';
        }

        // Set the canonical URL to have the proper page link.
        $this->CanonicalUrl(PageModel::PageUrl($Page));

        // Add modules
        $this->AddModule('GuestModule');
        $this->AddModule('SignedInModule');

        // Add CSS files
        $this->AddCssFile('page.css');

        // VERSION SPECIFIC CODE
        // Remove version_compare conditional when 2.1 becomes required by this app.
        // Runs if Vanilla version is greater than or equal to 2.1b1.
        if (version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
            $this->AddModule('NewDiscussionModule');
        } else {
            // Runs if Vanilla version is NOT greater than or equal to 2.1b1.
            $ApplicationFolder = $this->ApplicationFolder;
            $this->ApplicationFolder = 'vanilla';
            $this->AddModule('NewDiscussionModule');
            $this->ApplicationFolder = $ApplicationFolder;
        }

        // VERSION SPECIFIC CODE
        // Remove version_compare conditional when 2.1 becomes required by this app.
        // Runs if Vanilla version is greater than or equal to 2.1b1.
        if (version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
            $this->AddModule('DiscussionFilterModule');
            $this->AddModule('BookmarkedModule');
        } else {
            // Runs if Vanilla version is NOT greater than or equal to 2.1b1.
            // Bug fix for FetchView arguments in modules
            // that don't use 'vanilla' namespace in Vanilla 2.0.
            $ApplicationFolder = $this->ApplicationFolder;
            $this->ApplicationFolder = 'vanilla';
            $this->AddModule('BookmarkedModule');
            $this->ApplicationFolder = $ApplicationFolder;
        }

        // VERSION SPECIFIC CODE
        // Remove version_compare conditional when 2.1 becomes required by this app.
        // Runs if Vanilla version is greater than or equal to 2.1b1.
        if (version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
            $this->AddModule('DiscussionsModule');
            $this->AddModule('RecentActivityModule');
        }

        // VERSION SPECIFIC CODE
        // Remove version_compare conditional when 2.1 becomes required by this app.
        // Runs if Vanilla version is greater than or equal to 2.1b1.
        if (version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
            // Setup head.
            if (!$this->Data('Title')) {
                $Title = C('Garden.HomepageTitle');

                $DefaultControllerDestination = Gdn::Router()->GetDestination('DefaultController');
                if (($Title != '') && (strpos($DefaultControllerDestination, 'page/' . $Page->UrlCode) !== false)) {
                    // If the page is set as DefaultController.
                    $this->Title($Title, '');

                    // Add description meta tag.
                    $this->Description(C('Garden.Description', null));
                } else {
                    // If the page is NOT the DefaultController.
                    $this->Title($Page->Name);

                    // Add description meta tag.
                    $this->Description(SliceParagraph(Gdn_Format::PlainText($Page->Body, $Page->Format), 160));
                }
            }
        } else {
            // Runs if Vanilla version is NOT greater than or equal to 2.1b1.
            $this->Title($Page->Name);
        }

        $this->Render();
    }
}
