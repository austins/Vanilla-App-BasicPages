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

/**
 * The Page controller.
 */
class PageController extends Gdn_Controller {
    /** @var array List of objects to prep. They will be available as $this->$Name. */
    public $Uses = array('PageModel');

    protected $Page = null;

    /**
     * Include JS, CSS, and modules used by all methods of this controller.
     * Called by dispatcher before controller's requested method.
     */
    public function Initialize() {
        if ($this->DeliveryType() == DELIVERY_TYPE_ALL)
            $this->Head = new HeadModule($this);
        $this->AddJsFile('jquery.js');
        $this->AddJsFile('jquery.livequery.js');
        $this->AddJsFile('jquery.form.js');
        $this->AddJsFile('jquery.popup.js');
        $this->AddJsFile('jquery.gardenhandleajaxform.js');
        $this->AddJsFile('global.js');

        $this->AddCssFile('style.css');

        parent::Initialize();

        $this->FireEvent('AfterInitialize');
    }

    /**
     * Loads default page view.
     *
     * @param string $PageUrlCode ; Unique page URL stub identifier.
     */
    public function Index($PageUrlCode = '') {
        $this->Page = $this->PageModel->GetByUrlCode($PageUrlCode);

        // Require the custom view permission if it exists.
        // Otherwise, the page is public by default.
        $ViewPermissionName = 'BasicPages.' . $PageUrlCode . '.View';
        if (array_key_exists($ViewPermissionName, Gdn::PermissionModel()->PermissionColumns()))
            $this->Permission($ViewPermissionName);

        // If page doesn't exist.
        if ($this->Page == null) {
            throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);

            return null;
        }

        $this->SetData('Page', $this->Page, false);

        // Add body CSS class.
        $this->CssClass = 'Page-' . $this->Page->UrlCode;

        if (IsMobile())
            $this->CssClass .= ' PageMobile';

        // Set the canonical URL to have the proper page link.
        $this->CanonicalUrl(PageModel::PageUrl($this->Page));

        // Add modules
        $this->AddModule('GuestModule');
        $this->AddModule('SignedInModule');

        // Add CSS files
        $this->AddCssFile('page.css');

        $this->AddModule('NewDiscussionModule');
        $this->AddModule('DiscussionFilterModule');
        $this->AddModule('BookmarkedModule');
        $this->AddModule('DiscussionsModule');
        $this->AddModule('RecentActivityModule');

        // Setup head.
        if (!$this->Data('Title')) {
            $Title = C('Garden.HomepageTitle');

            $DefaultControllerDestination = Gdn::Router()->GetDestination('DefaultController');
            if (($Title != '') && (strpos($DefaultControllerDestination, 'page/' . $this->Page->UrlCode) !== false)) {
                // If the page is set as DefaultController.
                $this->Title($Title, '');

                // Add description meta tag.
                $this->Description(C('Garden.Description', null));
            } else {
                // If the page is NOT the DefaultController.
                $this->Title($this->Page->Name);

                // Add description meta tag.
                $this->Description(SliceParagraph(Gdn_Format::PlainText($this->Page->Body, $this->Page->Format), 160));
            }
        }

        $this->Render();
    }
}
