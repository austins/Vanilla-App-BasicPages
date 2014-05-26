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

/**
 * The BasicPages controller.
 *
 * Introduces common methods that child classes can use.
 */
class BasicPagesController extends Gdn_Controller {
    /** @var array List of objects to prep. They will be available as $this->$Name. */
    public $Uses = array();

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
}
