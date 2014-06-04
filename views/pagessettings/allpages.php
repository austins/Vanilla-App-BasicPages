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

$Pages = $this->Data('Page')->Result();
?>
<h1><?php echo $this->Data('Title'); ?></h1>
<div class="Box Aside" style="text-align: center; padding: 10px;"><a
        href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=72R6B2BUCMH46" target="_blank"><img
            src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="" style="vertical-align: middle;"/></a>
</div>
<div class="Info">
    <?php echo T('BasicPages.Settings.AllPages.Welcome', 'Welcome to the Basic Pages application by'); ?> <a
        href="http://vanillaforums.org/profile/addons/16014/Shadowdare" target="_blank">Shadowdare</a>!

    <br/><br/><?php echo T('BasicPages.Settings.AllPages.Donate',
        'If you find this application useful and would like to support the developer, please make a donation.'); ?>
</div>

<h1><?php echo T('BasicPages.Settings.AllPages.ManagePages', 'Manage Pages'); ?></h1>
<div class="Info">
    <?php echo T('BasicPages.Settings.AllPages.Info', 'With this application, you can create basic pages.'); ?>

    <br/><br/><?php echo T('BasicPages.Settings.AllPages.SortPages',
        'Drag and drop the pages to change their order in the header site menu. The order is saved after you drag them.'); ?>

    <br/><br/><?php echo T('BasicPages.Settings.AllPages.GetStarted',
        'Get started by clicking the button below to create a new page.'); ?>
</div>
<div class="FilterMenu">
    <?php echo Anchor(T('BasicPages.Settings.NewPage', 'New Page'), '/pagessettings/newpage', 'SmallButton'); ?>
</div>

<?php
if (count($Pages) > 0):
    ?>
    <h1><?php echo T('BasicPages.Settings.AllPages.OrganizePages', 'Organize Pages'); ?></h1>
    <ol class="Sortable">
        <?php foreach ($Pages as $Page): ?>
            <li id="list_<?php echo $Page->PageID; ?>" class="NoNesting">
                <div>
                    <table>
                        <tbody>
                        <tr id="<?php echo 'Page_' . $Page->PageID; ?>">
                            <td><?php
                                echo '<strong>' . $Page->Name . '</strong>';
                                $PageUrl = PageModel::PageUrl($Page);
                                echo '<br />' . Anchor($PageUrl, $PageUrl);
                                ?></td>
                            <td class="Buttons"><?php
                                echo Anchor(T('BasicPages.Settings.AllPages.PageEdit', 'Edit'),
                                    '/pagessettings/editpage/' . $Page->PageID, array('class' => 'SmallButton Edit'));
                                echo ' ';
                                echo Anchor(T('BasicPages.Settings.AllPages.PageDelete', 'Delete'),
                                    '/pagessettings/deletepage/' . $Page->PageID,
                                    array('class' => 'SmallButton Delete Popup'));
                                ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
<?php
else:
    echo '<h1>' . T('BasicPages.Settings.AllPages.Pages', 'Pages') . '</h1>';
    echo '<div class="Info">';
    echo T('BasicPages.Settings.AllPages.NoPages',
        'No pages currently exist. Create a new page by clicking the button above.');
    echo '</div>';
endif;
?>
