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
 * The PagesSettings controller.
 */
class PagesSettingsController extends Gdn_Controller {
    /** @var array List of objects to prep. They will be available as $this->$Name. */
    public $Uses = array('PageModel', 'Form');

    /**
     * Configures navigation sidebar in Dashboard.
     *
     * @param $CurrentUrl ; Path to current location in dashboard.
     */
    private function AddSideMenu($CurrentUrl = '') {
        // Only add to the assets if this is not a view-only request
        if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
            $SideMenu = new SideMenuModule($this);
            $SideMenu->HtmlId = '';

            if ($CurrentUrl != '')
                $SideMenu->HighlightRoute($CurrentUrl);

            $SideMenu->Sort = C('Garden.DashboardMenu.Sort');
            $this->EventArguments['SideMenu'] = & $SideMenu;
            $this->FireEvent('GetAppSettingsMenuItems');
            $this->AddModule($SideMenu, 'Panel');
        }
    }

    /**
     * Loads default view for this controller.
     */
    public function Index() {
        // Check permission
        $this->Permission('Garden.Settings.Manage');

        $this->View = 'allpages';
        $this->AllPages();
    }

    /**
     * Loads view with list of all pages.
     */
    public function AllPages($IndexPage = false) {
        // Check permission
        $this->Permission('Garden.Settings.Manage');

        if (version_compare(APPLICATION_VERSION, '2.2', '>=')) {
            // Version 2.2+
            // Version 2.1b1 doesn't have the new jQuery.
            // Nested sortable breaks with updated jQuery, so include old jQuery.
            $this->RemoveJsFile('jquery.js');
            $this->AddJsFile('js/library/nestedSortable.1.3.4/jquery-1.7.2.min.js', '', array('Sort' => 0));
        }

        $this->AddJsFile('js/library/nestedSortable.1.3.4/jquery-ui-1.8.11.custom.min.js');
        $this->AddJsFile('js/library/nestedSortable.1.3.4/jquery.ui.nestedSortable.js');
        $this->AddJsFile('pagessettings-allpages.js');

        $Offset = 0;
        $Limit = null;
        /* Disable pager for now, but keep functionality for later.
        // Determine offset from $IndexPage
        $IndexPageLimit = C('BasicPages.Pages.PerPage', 20);
        list($Offset, $Limit) = OffsetLimit($IndexPage, $IndexPageLimit);
        $IndexPage = PageNumber($Offset, $Limit);
        */

        // Get page data
        $this->SetData('Pages', $this->PageModel->Get($Offset, $Limit));

        /* Disable pager for now, but keep functionality for later.
        // Build the pager.
        $CountPages = $this->PageModel->GetCount();
        $this->SetData('CountPages', $CountPages);
        $PagerFactory = new Gdn_PagerFactory();
        $this->EventArguments['PagerType'] = 'Pager';
        $this->FireEvent('BeforeBuildPager');
        $this->Pager = $PagerFactory->GetPager($this->EventArguments['PagerType'], $this);
        $this->Pager->ClientID = 'Pager';
        $this->Pager->Configure(
            $Offset,
            $Limit,
            $CountPages,
            '/pagessettings/allpages/%1$s/'
        );
        if (!$this->Data('_PagerUrl'))
            $this->SetData('_PagerUrl', '/pagessettings/allpages/{Page}/');
        $this->SetData('_IndexPage', $IndexPage);
        $this->SetData('_Limit', $Limit);
        $this->FireEvent('AfterBuildPager');
        */

        $this->AddSideMenu('pagessettings/allpages');
        $this->Title(T('BasicPages.Settings.AllPages', 'All Pages'));
        $this->Render();
    }

    /**
     * Sorting display order of pages.
     * Accessed by AJAX so its default is to only output true/false.
     */
    public function SortPages() {
        // Check permission
        $this->Permission('Garden.Settings.Manage');

        // Set delivery type to true/false.
        $TransientKey = GetIncomingValue('TransientKey');

        $ValidTransient = Gdn::Request()->IsPostBack();
        if ($ValidTransient) {
            $TreeArray = GetValue('TreeArray', $_POST);
            $Saves = $this->PageModel->SaveSort($TreeArray);
            $this->SetData('Result', true);
            $this->SetData('Saves', $Saves);
        }

        // Renders true/false instead of template.
        $this->Render();
    }

    /**
     * Loads view for creating a new page.
     *
     * @param object $Page ; Not NULL when editing a valid page.
     */
    public function NewPage($Page = null) {
        // Check permission
        $this->Permission('Garden.Settings.Manage');

        // Add JavaScript files.
        $this->AddJsFile('jquery-ui.js');
        $this->AddJsFile('jquery.autogrow.js');
        $this->AddJsFile('pagessettings-newpage.js');

        // Prep Model
        $this->Form->SetModel($this->PageModel);

        // Set format data.
        $this->SetData('Formats', $this->GetFormats());
        $this->AddDefinition('DefaultFormat', C('BasicPages.DefaultFormatter', C('Garden.InputFormatter', 'Html')));

        // If form wasn't submitted.
        if ($this->Form->IsPostBack() == false) {
            // Prep form with current data for editing
            if (isset($Page)) {
                $this->SetData('Page', $Page);
                $this->Form->SetData($Page);

                // Send CurrentFormat value to the page to be used for
                // setting the selected value of the formats drop-down.
                $this->AddDefinition('CurrentFormat', $Page->Format);

                $this->Form->AddHidden('UrlCodeIsDefined', '1');

                if (Gdn::Router()->MatchRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix)) {
                    $this->Form->SetValue('HidePageFromURL', '1');
                    $this->Form->SetFormValue('HidePageFromURL', '1');
                }
            } else {
                $this->Form->AddHidden('UrlCodeIsDefined', '0');
            }
        } else {
            // Form was submitted.
            $FormValues = $this->Form->FormValues();

            if (isset($Page)) {
                $FormValues['PageID'] = $Page->PageID;
                $this->Form->SetFormValue('PageID', $Page->PageID);
            }

            // Validate form values.
            if ($FormValues['Name'] == '')
                $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorName', 'Page title is required.'), 'Name');
            if ($FormValues['Body'] == '')
                $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorBody', 'Page body is required.'), 'Body');

            // Format Name
            $FormValues['Name'] = Gdn_Format::Text($FormValues['Name']);

            // Validate UrlCode.
            if ($FormValues['UrlCode'] == '')
                $FormValues['UrlCode'] = $FormValues['Name'];

            // Format the UrlCode.
            $FormValues['UrlCode'] = Gdn_Format::Url($FormValues['UrlCode']);
            $this->Form->SetFormValue('UrlCode', $FormValues['UrlCode']);

            $SQL = Gdn::Database()->SQL();

            // Make sure that the UrlCode is unique among pages.
            $SQL->Select('p.PageID')
                ->From('Page p')
                ->Where('p.UrlCode', $FormValues['UrlCode']);

            if (isset($Page))
                $SQL->Where('p.PageID <>', $Page->PageID);

            $UrlCodeExists = isset($SQL->Get()->FirstRow()->PageID);

            if ($UrlCodeExists)
                $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorUrlCode',
                    'The specified URL code is already in use by another page.'), 'UrlCode');

            // Make sure sort is set if new page.
            if (!$Page) {
                $LastSort = $this->PageModel->GetLastSort();
                $FormValues['Sort'] = $LastSort + 1;
            }

            // Send CurrentFormat value to the page to be used for
            // setting the selected value of the formats drop-down.
            $this->AddDefinition('CurrentFormat', $FormValues['Format']);

            // Explicitly cast these values to an integer data type in case
            // they are equal to '' to be valid with MySQL strict mode, etc.
            $FormValues['SiteMenuLink'] = (int)$FormValues['SiteMenuLink'];

            // If all form values are validated.
            if ($this->Form->ErrorCount() == 0) {
                $PageID = $this->PageModel->Save($FormValues);

                $ValidationResults = $this->PageModel->ValidationResults();
                $this->Form->SetValidationResults($ValidationResults);

                // Create and clean up routes for UrlCode.
                if ($Page->UrlCode != $FormValues['UrlCode']) {
                    if (Gdn::Router()->MatchRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix))
                        Gdn::Router()->DeleteRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix);
                }

                if ($FormValues['HidePageFromURL'] == '1'
                    && !Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix)
                ) {
                    Gdn::Router()->SetRoute(
                        $FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix,
                        'page/' . $FormValues['UrlCode'] . $this->PageModel->RouteTargetSuffix,
                        'Internal'
                    );
                } elseif ($FormValues['HidePageFromURL'] == '0'
                    && Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix)
                ) {
                    Gdn::Router()->DeleteRoute($FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix);
                }

                // Set up a custom view permission.
                // The UrlCode must be unique and validated before this code.
                $ViewPermissionName = 'BasicPages.' . $FormValues['UrlCode'] . '.View';
                $PermissionTable = Gdn::Database()->Structure()->Table('Permission');
                $PermissionModel = Gdn::PermissionModel();

                // If a page is being edited, then check if UrlCode was changed by the user
                // and rename the custom view permission column for the page if it exists accordingly,
                // to keep the permission table clean.
                if (isset($Page) && ($Page->UrlCode != $FormValues['UrlCode'])) {
                    $OldViewPermissionName = 'BasicPages.' . $Page->UrlCode . '.View';
                    $PermissionModel->Undefine($OldViewPermissionName);

                    // The column must be dropped for now, because the RenameColumn method
                    // has a bug, which has been reported.
                    //$PermissionTable->RenameColumn($OldViewPermissionName, $ViewPermissionName);
                }

                $ViewPermissionExists = $PermissionTable->ColumnExists($ViewPermissionName);

                // Check if the user checked the setting to enable the custom view permission.
                if ((bool)$FormValues['ViewPermission']) {
                    // Check if the permission does not exist.
                    if (!$ViewPermissionExists) {
                        // Create the custom view permission.
                        $PermissionModel->Define($ViewPermissionName);

                        // Set initial permission for the Administrator role.
                        $PermissionModel->Save(array(
                            'Role' => 'Administrator',
                            $ViewPermissionName => 1
                        ));
                    }
                } elseif ($ViewPermissionExists) {
                    // Delete the custom view permission if it exists.
                    $PermissionTable->DropColumn($ViewPermissionName);
                }

                if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
                    if (strtolower($this->RequestMethod) == 'newpage')
                        Redirect('pagessettings/allpages#Page_' . $PageID);

                    $this->InformMessage('<span class="InformSprite Check"></span>' . T('BasicPages.Settings.NewPage.Saved',
                            'The page has been saved successfully. <br />Go back to ') .
                        Anchor(T('BasicPages.Settings.AllPages', 'all pages'),
                            'pagessettings/allpages') . T('BasicPages.Settings.NewPage.Saved2',
                            ' or ') . Anchor(T('BasicPages.Settings.NewPage.ViewPage', 'view the page'),
                            PageModel::PageUrl($FormValues['UrlCode'])) . '.',
                        'Dismissable AutoDismiss HasSprite');
                }
            }
        }

        // Setup head.
        if ($this->Data('Title')) {
            $this->AddSideMenu();
            $this->Title($this->Data('Title'));
        } else {
            $this->AddSideMenu('pagessettings/newpage');
            $this->Title(T('BasicPages.Settings.NewPage', 'New Page'));
        }
        $this->Render();
    }

    private function GetFormats() {
        $Formats = array(
            'Html' => 'HTML',
            'Markdown' => 'Markdown',
            'BBCode' => 'BBCode',
            'RawHtml' => 'Raw HTML',
            'RawHtmlLineBreaks' => 'Raw HTML (Automatic Line Breaks)'
        );

        return $Formats;
    }

    /**
     * Wrapper for the NewPage view.
     *
     * @param int $PageID ; Page ID for getting page data.
     */
    public function EditPage($PageID = null) {
        // Check permission
        $this->Permission('Garden.Settings.Manage');

        $Page = $this->PageModel->GetByID($PageID);
        if ($Page != null) {
            $this->View = 'newpage';
            $this->Title(T('BasicPages.Settings.EditPage', 'Edit Page'));
            $this->NewPage($Page);

            return null;
        }

        throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);

        return null;
    }

    /**
     * Loads view for deleting a page.
     *
     * @param int $PageID ; Page ID for deleting page data.
     */
    public function DeletePage($PageID = null) {
        // Check permission
        $this->Permission('Garden.Settings.Manage');

        $Page = $this->PageModel->GetByID($PageID);
        if ($Page != null) {
            // Form was submitted with OK
            if ($this->Form->AuthenticatedPostBack()) {
                $this->PageModel->Delete($PageID);

                // Clean up routes for UrlCode.
                if (Gdn::Router()->MatchRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix))
                    Gdn::Router()->DeleteRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix);

                if ($this->DeliveryType() == DELIVERY_TYPE_ALL) // Full Page
                    Redirect('pagessettings/allpages');
                elseif ($this->DeliveryType() == DELIVERY_TYPE_VIEW) // Popup
                    $this->RedirectUrl = Url('pagessettings/allpages');
            }

            $this->AddSideMenu();
            $this->Title(T('BasicPages.Settings.DeletePage', 'Delete Page'));
            $this->Render();

            return null;
        }

        throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);

        return null;
    }

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

        $this->AddCssFile('admin.css');
        $this->AddCssFile('pagessettings.css');

        // Call Gdn_Controller's Initialize() as well.
        $this->MasterView = 'admin';
        parent::Initialize();

        Gdn_Theme::Section('Dashboard');
    }
}
