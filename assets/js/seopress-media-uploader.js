jQuery(document).ready(function($){
  const array = ["#seopress_social_knowledge_img", "#seopress_social_twitter_img", "#seopress_social_fb_img"]
  array.forEach(function (item) {
    var mediaUploader;
    $(item + '_upload').click(function(e) {
        e.preventDefault();
        // If the uploader object has already been created, reopen the dialog
          if (mediaUploader) {
          mediaUploader.open();
          return;
        }
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose Image',
          button: {
          text: 'Choose Image'
        }, multiple: false });
  
        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function() {
          attachment = mediaUploader.state().get('selection').first().toJSON();
          $(item + '_meta').val(attachment.url);
        });
        // Open the uploader dialog
        mediaUploader.open();
    });
  });
});