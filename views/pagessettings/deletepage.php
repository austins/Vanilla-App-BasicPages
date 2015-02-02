<?php defined('APPLICATION') or exit(); ?>
    <h1><?php echo $this->Data('Title'); ?></h1>
    <div class="Info">
        <?php echo T('BasicPages.Settings.DeletePage.Notice',
            'Are you sure you want to delete this page? This action cannot be undone.'); ?>
    </div>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
    <div class="Buttons<?php if ($this->DeliveryType() == DELIVERY_TYPE_VIEW)
        echo ' Footer'; // Popup ?>">
        <?php
        echo $this->Form->Button(T('BasicPages.Settings.DeletePage.OK', 'OK'), array('class' => 'Button Primary'));
        echo Anchor(T('BasicPages.Settings.Cancel', 'Cancel'), 'pagessettings/allpages', 'Button');
        ?>
    </div>
<?php echo $this->Form->Close();
