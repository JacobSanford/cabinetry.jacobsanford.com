Drupal.behaviors.cabinetryCoreDialogCancelBind = {
    attach: function (context, settings) {
        jQuery('#ui-dialog-cancel-button', context).on('click', function(){
            jQuery('.ui-dialog-content').dialog('close');
            return false;
        });
    }
};
