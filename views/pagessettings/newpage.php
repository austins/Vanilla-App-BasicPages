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

$Session = Gdn::Session();
?>
<h1><?php echo $this->Data('Title'); ?></h1>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
   <li>
      <?php
         echo $this->Form->Label(T('BasicPages.Settings.PageTitle', 'Page Title'), 'Name');
         echo $this->Form->TextBox('Name', array('maxlength' => 100, 'class' => 'InputBox'));
      ?>
   </li>
   <li id="UrlCode">
      <?php
         echo Wrap(T('BasicPages.Settings.NewPage.PageUrl', 'Page URL:'), 'strong') . ' ';
         echo Gdn::Request()->Url('page', TRUE);
         echo '/';
         echo Wrap(htmlspecialchars($this->Form->GetValue('UrlCode')));
         echo $this->Form->TextBox('UrlCode');
         echo Anchor(T('edit'), '#', 'Edit');
         echo Anchor(T('OK'), '#', 'Save SmallButton');
		?>
   </li>
   <li>
      <?php
			echo $this->Form->Label(T('BasicPages.Settings.NewPage.PageBody', 'Page Body'), 'Body');
         
         // Include ButtonBar if it is enabled.
         if(Gdn::PluginManager()->CheckPlugin('ButtonBar')) {
            $ButtonBarView = Gdn::Controller()->FetchView('buttonbar','','plugins/ButtonBar');
            echo Wrap($ButtonBarView, 'div');
         }
         
         echo Wrap($this->Form->TextBox('Body', array('MultiLine' => TRUE, 'format' => 'Html', 'table' => 'Page', 'class' => 'TextBox BodyBox')), 'div', array('class' => 'TextBoxWrapper'));
         echo $this->Form->Hidden('Format', array('Value' => 'Html'));
         
         // Include HtmlHelp text if ButtonBar is not enabled.
         if(!Gdn::PluginManager()->CheckPlugin('ButtonBar'))
            echo Wrap(T('BasicPages.Settings.NewPage.HtmlHelp', 'You can use <b><a href="http://htmlguide.drgrog.com/cheatsheet.php" target="_new">Simple HTML</a></b> in your post.'), 'div', array('class' => 'PageBodyMarkupHint'));
      ?>
   </li>
   <?php if($Session->CheckPermission('Garden.Settings.Manage')): ?>
      <li>
         <?php echo $this->Form->CheckBox('RawBody', T('BasicPages.Settings.NewPage.PageRawBody', 'Disable automatic body formatting and allow raw HTML?')); ?>
      </li>
   <?php endif; ?>
   <li>
      <?php echo $this->Form->CheckBox('SiteMenuLink', T('BasicPages.Settings.NewPage.PageShowSiteMenuLink', 'Show header site menu link?')); ?>
   </li>
   <li>
      <?php echo $this->Form->CheckBox('HidePageFromURL', T('BasicPages.Settings.NewPage.PageHidePageFromURL', 'Hide "/page" from the URL?')); ?>
   </li>
   <li>
      <?php echo $this->Form->CheckBox('ViewPermission', T('BasicPages.Settings.NewPage.PageCustomViewPermission', 'Use custom view permission for roles? If unchecked, then this page is visible to anyone.')); ?>
   </li>
</ul>
<div class="Buttons">
   <?php
      echo $this->Form->Button(T('BasicPages.Settings.NewPage.Save', 'Save'), array('class' => 'Button Primary'));
      echo Anchor(T('BasicPages.Settings.Cancel', 'Cancel'), 'pagessettings/allpages', 'Button');
   ?>
</div>
<?php echo $this->Form->Close();
