//Video Sitemap
jQuery(document).ready(function(){
    function seopress_call_media_uploader() {
        var mediaUploader;
        var thumb;
        jQuery('.seopress_video_thumbnail_upload').click(function(e) {
            e.preventDefault();
            $btn = jQuery(this);
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
              mediaUploader.open();
              return;
            }
            thumb = jQuery(this).prev();
            // Extend the wp.media object
            mediaUploader = wp.media.frames.file_frame = wp.media({
              title: 'Choose Image',
              button: {
              text: 'Choose Image'
            }, multiple: false });

            // When a file is selected, grab the URL and set it as the text field's value
            mediaUploader.on('select', function() {
              var attachment = mediaUploader.state().get('selection').first().toJSON();
              $btn.prev().val(attachment.url);
            });
            // Open the uploader dialog
            mediaUploader.open();
        });
    }
    seopress_call_media_uploader();

    var template = jQuery('#wrap-videos .video:last').clone();

    //accordion
    var stop = false;
    jQuery("#wrap-videos .video h3").click(function(event) {
        if (stop) {
            event.stopImmediatePropagation();
            event.preventDefault();
            stop = false;
        }
    });
    function seopress_call_video_accordion() {
        jQuery( "#wrap-videos .video" ).accordion({
            collapsible: true,
            active: false,
            heightStyle:"panel",
        });
    }
    seopress_call_video_accordion();

    //define counter
    var sectionsCount = jQuery('#wrap-videos').attr('data-count');

    //add new section
    jQuery('#add-video').click(function() {

        //increment
        sectionsCount++;

        //loop through each input
        var section = template.clone().find(':input').each(function(){
            //Stock input id
            var input_id = this.id;
            
            //Stock input name
            var input_name = this.name;

            //set id to store the updated section number
            var newId = this.id.replace(/^(\w+)\[.*?\]/, '$1['+sectionsCount+']');

            //Update input name
            jQuery(this).attr('name', input_name.replace(/^(\w+)\[.*?\]/, '$1['+sectionsCount+']'));
            
            //Clear input value
            if (! jQuery(this).hasClass('seopress_video_thumbnail_upload')) {
                jQuery(this).attr('value','');
            }

            //update for label
            if (jQuery(this).is(':checkbox')) {
                jQuery(this).parent().attr('for', input_id.replace(/^(\w+)\[.*?\]/, '$1['+sectionsCount+']'));
                jQuery(this).parent().attr('id', input_name.replace(/^(\w+)\[.*?\]/, '$1['+sectionsCount+']'));
            } else if(jQuery(this).hasClass('seopress_video_thumbnail_upload')) {
                //do nothing
            } else {
                jQuery(this).prev().attr('for', input_id.replace(/^(\w+)\[.*?\]/, '$1['+sectionsCount+']'));
                jQuery(this).prev().attr('id', input_name.replace(/^(\w+)\[.*?\]/, '$1['+sectionsCount+']'));
            }

            //update id
            this.id = newId;

        }).end()

        //inject new section
        .appendTo('#wrap-videos');
        seopress_call_video_accordion();
        jQuery( "#wrap-videos .video" ).accordion('destroy');
        seopress_call_video_accordion();
        jQuery("[id^=__wp-uploader-id-]").each(function() {
            jQuery(this).remove();
        });
        seopress_call_media_uploader();
        return false;
    });

    //remove section
    jQuery('#wrap-videos').on('click', '.remove-video', function() {
        //fade out section
        jQuery(this).fadeOut(300, function(){
            jQuery(this).parent().parent().parent().parent().remove();
            return false;
        });
        return false;
    });
});