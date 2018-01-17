<?php defined('APPLICATION') or exit();

$Pages = $this->Data('Pages')->Result();

echo heading($this->Data('Title'), t('BasicPages.Settings.NewPage', 'New Page'), '/pagessettings/newpage');
?>
<section class="padded clearfix">
    <div class="pull-right">
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=72R6B2BUCMH46"
           target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt=""
                                style="vertical-align: middle;"/></a>
    </div>

    <div>
        <?php echo T('BasicPages.Settings.AllPages.Welcome', 'Welcome to the Basic Pages application by'); ?> <a
                href="https://github.com/austins" target="_blank">Austin S.</a>!

        <br><br><?php echo T('BasicPages.Settings.AllPages.Donate',
            'If you find this application useful and would like to support the developer, please make a donation.'); ?>
    </div>
</section>

<div class="padded">
    <?php
    if (count($Pages) > 0):
        ?>
        <header class="subheading-block">
            <h2 class="subheading-title"><?php echo T('BasicPages.Settings.AllPages.OrganizePages', 'Organize Pages'); ?></h2>

            <div class="subheading-description">
                <?php echo T('BasicPages.Settings.AllPages.SortPages',
                    'Drag and drop the pages to change their order in the header site menu. The order is saved after you drag them.'); ?>
            </div>
        </header>

        <div class="table-wrap padded-top">
            <table border="0" cellpadding="0" cellspacing="0" class="table-data js-tj Sortable" id="PageTable">
                <thead>
                <tr id="0">
                    <th><?php echo t('BasicPages.Settings.PageTitle', 'Page Title'); ?></th>
                    <th class="column-xl"><?php echo t('BasicPages.Settings.PageUrl', 'Page URL'); ?></th>
                    <th class="options column-sm"></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($Pages as $Page): ?>
                    <tr id="<?php echo 'Page_' . $Page->PageID; ?>">
                        <td><?php echo '<strong>' . $Page->Name . '</strong>'; ?></td>
                        <td>
                            <?php
                            $PageUrl = PageModel::PageUrl($Page);
                            echo Anchor($PageUrl, $PageUrl);
                            ?>
                        </td>
                        <td class="options">
                            <div class="btn-group">
                                <?php
                                echo anchor(dashboardSymbol('edit'), '/pagessettings/editpage/' . $Page->PageID, 'btn btn-icon', ['aria-label' => t('Edit'), 'title' => t('Edit')]);
                                echo anchor(dashboardSymbol('delete'), '/pagessettings/deletepage/' . $Page->PageID, 'js-modal btn btn-icon', ['aria-label' => t('Delete'), 'title' => t('Delete')]);
                                ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php
    else:
        echo '<h2 class="subheading">' . T('BasicPages.Settings.AllPages.Pages', 'Pages') . '</h2>';

        echo '<div>';
        if ((int)$this->Data('CountPages') === 0)
            echo T('BasicPages.Settings.AllPages.NoPages',
                'No pages currently exist. Create a new page by clicking the "New Page" button above.');
        else
            echo T('BasicPages.Settings.AllPages.NoPagesOnPage',
                'No pages exist on this index page.');
        echo '</div>';
    endif;
    ?>
</div>
