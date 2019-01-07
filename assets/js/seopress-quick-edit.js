(function($) {

   // we create a copy of the WP inline edit post function
   var $wp_inline_edit = inlineEditPost.edit;

   // and then we overwrite the function with our own code
   inlineEditPost.edit = function( id ) {

      // "call" the original WP edit function
      // we don't want to leave WordPress hanging
      $wp_inline_edit.apply( this, arguments );

      // now we take care of our business

      // get the post ID
      var $post_id = 0;
      if ( typeof( id ) == 'object' ) {
         $post_id = parseInt( this.getId( id ) );
      }

      if ( $post_id > 0 ) {
         // define the edit row
         var $edit_row = $( '#edit-' + $post_id );
         var $post_row = $( '#post-' + $post_id );

         // get the data
         var $seopress_title = $( '#seopress_title-' + $post_id ).text();
         var $seopress_desc = $( '#seopress_desc-' + $post_id ).text();
         var $seopress_tkw = $( '#seopress_tkw-' + $post_id ).text();
         var $seopress_canonical = $( '#seopress_canonical-' + $post_id ).text();

         // populate the data
         $edit_row.find( 'input[name="seopress_title"]' ).val( $seopress_title );
         $edit_row.find( 'textarea[name="seopress_desc"]' ).val( $seopress_desc );
         $edit_row.find( 'input[name="seopress_tkw"]' ).val( $seopress_tkw );
         $edit_row.find( 'input[name="seopress_canonical"]' ).val( $seopress_canonical );
      }
   };

})(jQuery);