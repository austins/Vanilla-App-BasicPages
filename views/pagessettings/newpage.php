<?php defined('APPLICATION') or exit();
$Page = $this->Data('Page');
$Session = Gdn::Session();

if (isset($this->_Definitions['CurrentFormat'])) {
    $FormatSelected = $this->_Definitions['CurrentFormat'];
} else {
    $FormatSelected = val('Format', $Page, $this->_Definitions['DefaultFormat']);
}

echo heading($this->data('Title'), '', '', [], '/pagessettings/allpages');

echo $this->Form->Open();
echo $this->Form->Errors();
?>
    <ul>
        <li class="form-group">
            <div class="label-wrap"><?php echo $this->Form->Label(T('BasicPages.Settings.PageTitle', 'Page Title'), 'Name'); ?></div>
            <div class="input-wrap"><?php echo $this->Form->TextBox('Name', array('maxlength' => 100)); ?></div>
        </li>
        <li id="UrlCode" class="form-group">
            <div class="label-wrap"><?php echo Wrap(T('BasicPages.Settings.PageUrl', 'Page URL'), 'strong') . ':'; ?></div>
            <div class="input-wrap category-url-code">
                <?php
                echo Gdn::Request()->Url('page', true);
                echo '/';
                echo Wrap(htmlspecialchars($this->Form->GetValue('UrlCode')));
                echo $this->Form->TextBox('UrlCode');
                echo anchor(t('edit'), '#', 'Edit btn btn-link');
                echo anchor(t('OK'), '#', 'Save btn btn-primary');
                ?>
            </div>
        </li>
    </ul>

    <section>
        <h2 class="subheading"><?php echo $this->Form->Label(T('BasicPages.Settings.NewPage.PageBody', 'Page Body'), 'Body'); ?></h2>

        <?php
        echo Wrap($this->Form->TextBox('Body',
            array('MultiLine' => true, 'format' => $FormatSelected, 'table' => 'Page', 'class' => 'TextBox')), 'div',
            array('class' => 'TextBoxWrapper'));
        ?>
    </section>

    <div id="AdvancedOptionsToggle" class="padded">
        <?php echo $this->Form->CheckBox('ShowAdvancedOptions', T('Show advanced options?')); ?>
    </div>

    <ul id="AdvancedOptions">
        <li class="form-group">
            <div class="label-wrap"><?php echo $this->Form->Label(T('Body Format'), 'Format'); ?></div>
            <div class="input-wrap"><?php echo $this->Form->DropDown('Format', $this->Data('Formats'), array('Value' => $FormatSelected)); ?></div>
        </li>
        <li class="form-group">
            <?php echo $this->Form->toggle('SiteMenuLink',
                T('BasicPages.Settings.NewPage.PageShowSiteMenuLink', 'Show header site menu link?')); ?>
        </li>
        <li class="form-group">
            <?php echo $this->Form->toggle('HidePageFromURL',
                T('BasicPages.Settings.NewPage.PageHidePageFromURL', 'Hide "/page" from the URL?')); ?>
        </li>
        <li class="form-group">
            <?php echo $this->Form->toggle('ViewPermission', T('BasicPages.Settings.NewPage.PageCustomViewPermission',
                'Use custom view permission for roles? If unchecked, then this page will visible to anyone.')); ?>
        </li>
    </ul>

<?php echo $this->Form->Close('Save');
