<?php
if (!defined('APPLICATION'))
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

$Session = Gdn::Session();

// Format Body
if ($this->Page->Format === 'RawHtml') {
    $FormatBody = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $this->Page->Body);
    $FormatBody = FixNl2Br($FormatBody);
} else {
    $FormatBody = Gdn_Format::To($this->Page->Body, $this->Page->Format);
}
?>
<div id="Page_<?php echo $this->Page->PageID; ?>" class="PageContent Page-<?php echo $this->Page->UrlCode; ?>">
    <?php $this->FireEvent('BeforePageOptions'); ?>
    <?php if ($Session->CheckPermission('Garden.Settings.Manage')): ?>
        <div class="Options">
         <span class="ToggleFlyout OptionsMenu">
            <span class="OptionsTitle" title="<?php echo T('Options'); ?>"><?php echo T('Options'); ?></span>
             <?php echo Sprite('SpFlyoutHandle', 'Arrow'); ?>
             <ul class="Flyout MenuItems" style="display: none;">
                 <?php echo Wrap(Anchor(T('BasicPages.Settings.EditPage', 'Edit Page'),
                     'pagessettings/editpage/' . $this->Page->PageID, 'EditPage'), 'li'); ?>
             </ul>
         </span>
        </div>
    <?php endif; ?>
    <h1 id="PageTitle" class="H"><?php echo $this->Page->Name; ?></h1>
    <?php $this->FireEvent('AfterPageTitle'); ?>
    <div id="PageBody"><?php echo $FormatBody; ?></div>
    <?php $this->FireEvent('AfterPageBody'); ?>
</div>
