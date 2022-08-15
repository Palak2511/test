var repeatable_field = {
    init: function(){
        this.addRow();
        this.removeRow();
        this.addImageUploader();
        this.removeImage();
         this.dragnDrop();
    },
     dragnDrop: function(){

        jQuery("#ask-sortable").sortable();
       
        jQuery("#ask-sortable").disableSelection();
    },
    addRow: function(){
        jQuery(document).on('click', '#add-row', function (e) {
            e.preventDefault();
            var row = jQuery('.empty-row.screen-reader-text').clone(true);
            row.removeClass('empty-row screen-reader-text');
            row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
        });
    },
    removeRow: function(){
        jQuery(document).on('click', '.remove-row', function () {
            jQuery(this).parents('tr').remove();
            return false;
        });
    },
    addImageUploader: function(){
        jQuery(document).on('click', '.ask-upload_image_button', function (event) {
            event.preventDefault();
           
            var inputField = jQuery(this).prev('.nts-logo');
            // Create the media frame.
            var pevent = event,
                button = jQuery(this),
                file_frame = wp.media.frames.items = wp.media({
                    title: 'Add to Gallery',
                    button: {
                        text: 'Select'
                    },
                    library: {
                            type: [ 'video', 'image' ]
                    },
                }).on('select', function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;
                   button.next().val(attachment.id);
                   button.next().before('<div><img src="' + attachment_thumbnail.url + '" width="150px" height="150px" /></div>');
                   button.parent().find('.ask-remove_image_button').show();
                    button.hide();

                }).open();
        });
    }, 

    removeImage: function(){
        jQuery(document).on('click', '.ask-remove_image_button', function (event) {
            event.preventDefault();

            jQuery(this).parent().find('.ask-logo').val('');
            jQuery(this).parent().find('.ask-upload_image_button').show();
            jQuery(this).hide();
            jQuery(this).parent().find('img').remove();

        });
    }

};


jQuery(document).ready(function ($) {
   repeatable_field.init();
});
