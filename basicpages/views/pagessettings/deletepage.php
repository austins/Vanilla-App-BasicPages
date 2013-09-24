<?php if (!defined('APPLICATION')) exit();
/**
 * Basic Pages - An application for Garden & Vanilla Forums.
 * Copyright (C) 2013  Livid Tech
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
?>
<h1><?php echo $this->Data('Title'); ?></h1>
<div class="Info">
   <?php echo T('BasicPages.Settings.DeletePage.Notice', 'Are you sure you want to delete this page? This action cannot be undone.'); ?>
</div>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<div class="Buttons<?php if($this->DeliveryType() == DELIVERY_TYPE_VIEW) echo ' Footer'; // Popup ?>">
   <?php
      echo $this->Form->Button(T('BasicPages.Settings.DeletePage.OK', 'OK'), array('class' => 'Button Primary'));
      echo Anchor(T('BasicPages.Settings.Cancel', 'Cancel'), 'pagessettings/allpages', 'Button');
   ?>
</div>
<?php echo $this->Form->Close();
