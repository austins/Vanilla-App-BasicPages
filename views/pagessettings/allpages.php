<?php defined('APPLICATION') or exit();

$Pages = $this->Data('Pages')->Result();
?>
<h1><?php echo $this->Data('Title'); ?></h1>
<div class="Box Aside" style="text-align: center; padding: 10px;"><a
        href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=72R6B2BUCMH46" target="_blank"><img
            src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="" style="vertical-align: middle;"/></a>
</div>
<div class="Info">
    <?php echo T('BasicPages.Settings.AllPages.Welcome', 'Welcome to the Basic Pages application by'); ?> <a
        href="https://github.com/austins" target="_blank">Austin S.</a>!

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
/* Disable pager for now, but keep functionality for later.
$PagerOptions = array('Wrapper' => '<span class="PagerNub">&#160;</span><div %1$s>%2$s</div>', 'RecordCount' => $this->Data('CountPages'), 'CurrentRecords' => $this->Data('Pages')->NumRows());
if ($this->Data('_PagerUrl'))
    $PagerOptions['Url'] = $this->Data('_PagerUrl');
*/

if (count($Pages) > 0):
    ?>
    <h1><?php echo T('BasicPages.Settings.AllPages.OrganizePages', 'Organize Pages'); ?></h1>
    <?php
    /* Disable pager for now, but keep functionality for later.
    echo '<div class="PageControls Top">';
    PagerModule::Write($PagerOptions);
    echo '</div>';
    */
    ?>
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
    /* Disable pager for now, but keep functionality for later.
    echo '<div class="PageControls Bottom">';
    PagerModule::Write($PagerOptions);
    echo '</div>';
    */
    ?>
<?php
else:
    echo '<h1>' . T('BasicPages.Settings.AllPages.Pages', 'Pages') . '</h1>';
    echo '<div class="Info">';

    if((int)$this->Data('CountPages') === 0)
        echo T('BasicPages.Settings.AllPages.NoPages',
            'No pages currently exist. Create a new page by clicking the button above.');
    else
        echo T('BasicPages.Settings.AllPages.NoPagesOnPage',
            'No pages exist on this index page.');

    echo '</div>';
endif;
?>
