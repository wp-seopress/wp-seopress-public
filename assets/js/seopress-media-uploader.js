jQuery(document).ready(function($){
  var mediaUploader;

  $('#seopress_social_fb_img_upload').click(function(e) {
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
      $('#seopress_social_fb_img_meta').val(attachment.url);
    });

    // Open the uploader dialog
    mediaUploader.open();
  });
});

jQuery(document).ready(function($){
  var mediaUploader;
  $('#seopress_social_twitter_img_upload').click(function(e) {
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
        $('#seopress_social_twitter_img_meta').val(attachment.url);
      });
      // Open the uploader dialog
      mediaUploader.open();
  });
});

jQuery(document).ready(function($){
  var mediaUploader;
  $('#seopress_social_knowledge_img_upload').click(function(e) {
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
        $('#seopress_social_knowledge_img_meta').val(attachment.url);
      });
      // Open the uploader dialog
      mediaUploader.open();
  });
});