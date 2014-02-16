<?php if (!defined('APPLICATION')) exit();
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
 * The PagesSettings controller.
 */
class PagesSettingsController extends Gdn_Controller {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('PageModel', 'Form');
   
   /**
    * Configures navigation sidebar in Dashboard.
    *
    * @param $CurrentUrl; Path to current location in dashboard.
    */
   private function AddSideMenu($CurrentUrl) {
      // Only add to the assets if this is not a view-only request
      if($this->_DeliveryType == DELIVERY_TYPE_ALL) {
         $SideMenu = new SideMenuModule($this);
         $SideMenu->HtmlId = '';
         $SideMenu->HighlightRoute($CurrentUrl);
         $SideMenu->Sort = C('Garden.DashboardMenu.Sort');
         $this->EventArguments['SideMenu'] = &$SideMenu;
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
   public function AllPages() {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      // VERSION SPECIFIC CODE
      // Remove version_compare procedure when 2.1 becomes required by this app.
      // Runs if Vanilla version is greater than or equal to 2.1b1.
      if(version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
         // Add JavaScript files.
         if(version_compare(APPLICATION_VERSION, '2.2', '>=')) {
            // Version 2.2+
            // Version 2.1b1 doesn't have the new jQuery.
            // Nested sortable breaks with updated jQuery, so include old jQuery.
            $this->RemoveJsFile('jquery.js');
            $this->AddJsFile('js/library/nestedSortable.1.3.4/jquery-1.7.2.min.js', '', array('Sort' => 0));
         }
         $this->AddJsFile('js/library/nestedSortable.1.3.4/jquery-ui-1.8.11.custom.min.js');
         $this->AddJsFile('js/library/nestedSortable.1.3.4/jquery.ui.nestedSortable.js');
      } else {
         $this->AddJsFile('js/library/nestedSortable.1.2.1/jquery-ui-1.8.2.custom.min.js');
         $this->AddJsFile('js/library/nestedSortable.1.2.1/jquery.ui.nestedSortable.js');
      }
      $this->AddJsFile('pagessettings-allpages.js');
      
      // Get page data
      $this->SetData('PageData', $this->PageModel->GetAll());
      
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
      
      // VERSION SPECIFIC CODE
      // Remove version_compare procedure when 2.1 becomes required by this app.
      // Runs if Vanilla version is greater than or equal to 2.1b1.
      if(version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
         $ValidTransient = Gdn::Request()->IsPostBack();
      } else {
         $ValidTransient = Gdn::Session()->ValidateTransientKey($TransientKey);
         if($this->_DeliveryType == DELIVERY_TYPE_ALL)
            $this->_DeliveryType = DELIVERY_TYPE_BOOL;
      }
      
      if($ValidTransient) {
         $TreeArray = GetValue('TreeArray', $_POST);
         $Saves = $this->PageModel->SaveSort($TreeArray);
         $this->SetData('Result', TRUE);
         $this->SetData('Saves', $Saves);
      }
      
      // Renders true/false instead of template.
      $this->Render();
   }

   /**
    * Loads view for creating a new page.
    *
    * @param object $Page; Not NULL when editing a valid page.
    */
   public function NewPage($Page = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      // Add JavaScript files.
      $this->AddJsFile('jquery-ui.js');
      $this->AddJsFile('jquery.autogrow.js');
      $this->AddJsFile('pagessettings-newpage.js');
      
      // Temporary fix for loading ButtonBar CSS file if ButtonBar is enabled.
      if(Gdn::PluginManager()->CheckPlugin('ButtonBar'))
         $this->AddCssFile('buttonbar.css', 'plugins/ButtonBar');
         
      // VERSION SPECIFIC CODE
      // Remove version_compare procedure when 2.1 becomes required by this app.
      // Runs if Vanilla version is NOT greater than or equal to 2.1b1.
      if(!version_compare(APPLICATION_VERSION, '2.1b1', '>=')
            && Gdn::PluginManager()->CheckPlugin('ButtonBar')) {
         // Include JS files for non-core ButtonBar plugin for Vanilla 2.0.
         $this->AddJsFile('buttonbar.js', 'plugins/ButtonBar');
         $this->AddJsFile('jquery.hotkeys.js', 'plugins/ButtonBar');
      }
      
      // Prep Model
      $this->Form->SetModel($this->PageModel);
      
      // If form wasn't submitted.
      if($this->Form->IsPostBack() == FALSE) {
         // Prep form with current data for editing         
         if(isset($Page)) {
            $this->Form->SetData($Page);
            
            // VERSION SPECIFIC CODE
            // Remove version_compare procedure when 2.1 becomes required by this app.
            // Runs if Vanilla version is NOT greater than or equal to 2.1b1.
            if(!version_compare(APPLICATION_VERSION, '2.1b1', '>=')) {
               foreach($Page as $Property => $Value) {
                  $this->Form->SetFormValue($Property, $Value);
               }
            }
            
            $this->Form->AddHidden('UrlCodeIsDefined', '1');
            
            if(Gdn::Router()->MatchRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix)) {
               $this->Form->SetValue('HidePageFromURL', '1');
               $this->Form->SetFormValue('HidePageFromURL', '1');
            }
         } else {
            $this->Form->AddHidden('UrlCodeIsDefined', '0');
         }
      } else {
         // Form was submitted.
         $FormValues = $this->Form->FormValues();
         
         if(isset($Page)) {
            $FormValues['PageID'] = $Page->PageID;
            $this->Form->SetFormValue('PageID', $Page->PageID);
         }
         
         // Validate form values.
         if($FormValues['Name'] == '')
            $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorName', 'Page title is required.'), 'Name');
         if($FormValues['Body'] == '')
            $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorBody', 'Page body is required.'), 'Body');
         
         // Format Name
         $FormValues['Name'] = Gdn_Format::Text($FormValues['Name']);
         
         // Validate UrlCode.
         if($FormValues['UrlCode'] == '')
            $FormValues['UrlCode'] = $FormValues['Name'];
         $FormValues['UrlCode'] = Gdn_Format::Url($FormValues['UrlCode']);
         $this->Form->SetFormValue('UrlCode', $FormValues['UrlCode']);
         
         $SQL = Gdn::Database()->SQL();

         // Check if editing and if slug is same as one currently set in PageID.
         if(isset($Page)) {
            $ValidPageID = $SQL
               ->Select('p.UrlCode')
               ->From('Page p')
               ->Where('p.PageID', $Page->PageID)
               ->Get()
               ->FirstRow();
         }

         // Make sure that the UrlCode is unique among pages.
         $InvalidUrlCode = $SQL
            ->Select('p.PageID')
            ->From('Page p')
            ->Where('p.UrlCode', $FormValues['UrlCode'])
            ->Get()
            ->NumRows();

         if((isset($Page) && $InvalidUrlCode && ($ValidPageID->UrlCode != $FormValues['UrlCode']))
               || ((!isset($Page) && $InvalidUrlCode)))
            $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorUrlCode', 'The specified URL code is already in use by another page.'), 'UrlCode');
         
         // Check if user does not have permission to check RawBody.
         $Session = Gdn::Session();
         if(!$Session->CheckPermission('Garden.Settings.Manage'))
            $FormValues['RawBody'] = '0';
         
         // Make sure sort is set if new page.
         if(!$Page) {
            $LastSort = $this->PageModel->GetLastSort();
            $FormValues['Sort'] = $LastSort + 1;
         }
         
         // If all form values are validated.
         if($this->Form->ErrorCount() == 0) {
            $PageID = $this->PageModel->Save($FormValues);
            
            $ValidationResults = $this->PageModel->ValidationResults();
            $this->Form->SetValidationResults($ValidationResults);

            // Create and clean up routes for UrlCode.
            if($Page->UrlCode != $FormValues['UrlCode']) {
               if(Gdn::Router()->MatchRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix))
                  Gdn::Router()->DeleteRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix);
            }

            if($FormValues['HidePageFromURL'] == '1'
                  && !Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix)) {
               Gdn::Router()->SetRoute(
                  $FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix,
                  'page/' . $FormValues['UrlCode'] . $this->PageModel->RouteTargetSuffix,
                  'Internal'
               );
            } elseif($FormValues['HidePageFromURL'] == '0'
                        && Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix)) {
               Gdn::Router()->DeleteRoute($FormValues['UrlCode'] . $this->PageModel->RouteExpressionSuffix);
            }

            // Set up a custom view permission.
            // The UrlCode must be validated before this code.
            $ViewPermissionName = 'BasicPages.' . $FormValues['UrlCode'] . '.View';
            $PermissionTable = Gdn::Database()->Structure()->Table('Permission');
            $ViewPermissionExists = $PermissionTable->ColumnExists($ViewPermissionName);

            // Check if the user checked the setting to enable the custom view permission.
            if($FormValues['ViewPermission'] == '1') {
               // Check if the permission does not exist.
               if(!$ViewPermissionExists) {
                  $PermissionModel = Gdn::PermissionModel();

                  // Create the custom view permission.
                  $PermissionModel->Define($ViewPermissionName);

                  // Set initial permission for the Administrator role.
                  $PermissionModel->Save(array(
                        'Role' => 'Administrator',
                        $ViewPermissionName => 1
                  ));
               }
            } elseif($ViewPermissionExists) {
               // Delete the custom view permission if it exists.
               $PermissionTable->DropColumn($ViewPermissionName);
            }
            
            if($this->DeliveryType() == DELIVERY_TYPE_ALL) {
               if(strtolower($this->RequestMethod) == 'newpage')
                  Redirect('pagessettings/allpages#Page_' . $PageID);
               
               $this->InformMessage('<span class="InformSprite Check"></span>' . T('BasicPages.Settings.NewPage.Saved', 'The page has been saved successfully. <br />Go back to ') .
                       Anchor(T('BasicPages.Settings.AllPages', 'all pages'), 'pagessettings/allpages') . T('BasicPages.Settings.NewPage.Saved2', ' or ') . Anchor(T('BasicPages.Settings.NewPage.ViewPage', 'view the page'), PageModel::PageUrl($Page)) . '.',
                       'Dismissable AutoDismiss HasSprite');
            }
         }
      }
      
      // Setup head.
      if($this->Data('Title')) {
         $this->AddSideMenu();
         $this->Title($this->Data('Title'));
      } else {
         $this->AddSideMenu('pagessettings/newpage');
         $this->Title(T('BasicPages.Settings.NewPage', 'New Page'));
      }
      $this->Render();
   }
   
   /**
    * Wrapper for the NewPage view.
    *
    * @param int $PageID; Page ID for getting page data.
    */
   public function EditPage($PageID = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $Page = $this->PageModel->GetByID($PageID);
      if($Page != NULL) {
         $this->View = 'newpage';
         $this->Title(T('BasicPages.Settings.EditPage', 'Edit Page'));
         $this->NewPage($Page);
         return NULL;
      }
      
      throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);
      return NULL;
   }
   
   /**
    * Loads view for deleting a page.
    *
    * @param int $PageID; Page ID for deleting page data.
    */
   public function DeletePage($PageID = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $Page = $this->PageModel->GetByID($PageID);
      if($Page != NULL) {
         // Form was submitted with OK
         if($this->Form->AuthenticatedPostBack()) {
            $this->PageModel->Delete($PageID);
            
            // Clean up routes for UrlCode.
            if(Gdn::Router()->MatchRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix))
               Gdn::Router()->DeleteRoute($Page->UrlCode . $this->PageModel->RouteExpressionSuffix);
            
            if($this->DeliveryType() == DELIVERY_TYPE_ALL) // Full Page
               Redirect('pagessettings/allpages');
            elseif($this->DeliveryType() == DELIVERY_TYPE_VIEW) // Popup
               $this->RedirectUrl = Url('pagessettings/allpages');
         }
      
         $this->AddSideMenu();
         $this->Title(T('BasicPages.Settings.DeletePage', 'Delete Page'));
         $this->Render();
         return NULL;
      }
      
      throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);
      return NULL;
   }
   
   /**
    * Include JS, CSS, and modules used by all methods of this controller.
    * Called by dispatcher before controller's requested method.
    */
   public function Initialize() {
      if($this->DeliveryType() == DELIVERY_TYPE_ALL)
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
      
      // VERSION SPECIFIC CODE
      // Remove version_compare conditional when 2.1 becomes required by this app.
      // Runs if Vanilla version is greater than or equal to 2.1b1.
      if(version_compare(APPLICATION_VERSION, '2.1b1', '>='))
         Gdn_Theme::Section('Dashboard');
   }
}
