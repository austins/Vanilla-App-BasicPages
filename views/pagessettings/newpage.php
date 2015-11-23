<?php defined('APPLICATION') or exit();
$Page = $this->Data('Page');
$Session = Gdn::Session();

if(isset($this->_Definitions['CurrentFormat']))
    $FormatSelected = $this->_Definitions['CurrentFormat'];
else
    $FormatSelected = GetValue('Format', $Page, $this->_Definitions['DefaultFormat']);
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
            echo Gdn::Request()->Url('page', true);
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

            echo Wrap($this->Form->TextBox('Body',
                array('MultiLine' => true, 'format' => $FormatSelected, 'table' => 'Page', 'class' => 'TextBox')), 'div',
                array('class' => 'TextBoxWrapper'));
            ?>
        </li>
        <li>
            <?php echo $this->Form->CheckBox('ShowAdvancedOptions', T('Show advanced options?')); ?>

            <ul id="AdvancedOptions">
                <li>
                    <?php
                    echo $this->Form->Label(T('Body Format'), 'Format');
                    echo $this->Form->DropDown('Format', $this->Data('Formats'), array('Value' => $FormatSelected));
                    ?>
                </li>
                <li>
                    <?php echo $this->Form->CheckBox('SiteMenuLink',
                        T('BasicPages.Settings.NewPage.PageShowSiteMenuLink', 'Show header site menu link?')); ?>
                </li>
                <li>
                    <?php echo $this->Form->CheckBox('HidePageFromURL',
                        T('BasicPages.Settings.NewPage.PageHidePageFromURL', 'Hide "/page" from the URL?')); ?>
                </li>
                <li>
                    <?php echo $this->Form->CheckBox('ViewPermission', T('BasicPages.Settings.NewPage.PageCustomViewPermission',
                        'Use custom view permission for roles? If unchecked, then this page will visible to anyone.')); ?>
                </li>
            </ul>
        </li>
    </ul>
    <div class="Buttons">
        <?php
        echo $this->Form->Button(T('BasicPages.Settings.NewPage.Save', 'Save'), array('class' => 'Button Primary'));
        echo Anchor(T('BasicPages.Settings.Cancel', 'Cancel'), 'pagessettings/allpages', 'Button');
        ?>
    </div>
<?php echo $this->Form->Close();
