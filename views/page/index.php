<?php defined('APPLICATION') or exit();
$Session = Gdn::Session();

// Format page body.
$PageBody = $this->Page->Body;
if ($this->Page->Format === 'RawHtmlLineBreaks') {
    $PageBody = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $PageBody);
    $PageBody = FixNl2Br($PageBody);
} else if ($this->Page->Format !== 'RawHtml') {
    $PageBody = Gdn_Format::To($PageBody, $this->Page->Format);
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
    <div id="PageBody"><?php echo $PageBody; ?></div>
    <?php $this->FireEvent('AfterPageBody'); ?>
</div>
