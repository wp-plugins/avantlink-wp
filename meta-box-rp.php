<?php

// use the admin_menu action to define the custom boxes
add_action('admin_menu', 'avantlink_rp_add_custom_box');

// use the save_post action to do something with the data entered
add_action('save_post', 'avantlink_rp_save_postdata');

// adds a custom section to the "advanced" Post and Page edit screens
function avantlink_rp_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'avantlink_rp_sectionid', __( 'AvantLink Related Products', 'avantlink_rp_textdomain' ),
                'avantlink_rp_inner_custom_box', 'post', 'side' );
    add_meta_box( 'avantlink_rp_sectionid', __( 'AvantLink Related Products', 'avantlink_rp_textdomain' ),
                'avantlink_rp_inner_custom_box', 'page', 'side' );
   } else {
    add_action('dbx_post_advanced', 'avantlink_rp_old_custom_box' );
    add_action('dbx_page_advanced', 'avantlink_rp_old_custom_box' );
  }
}

// prints the inner fields for the custom post/page section
function avantlink_rp_inner_custom_box() {

  global $post;

  // use nonce for verification
  echo '<input type="hidden" name="avantlink_rp_noncename" id="avantlink_rp_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  echo '<p><em><small>Customize the information used to show related products for this specific post.</small></em></p>';

  // the actual fields for data entry
  echo '<p><label for="avantlink_rp_keyword">' . __("Keywords/Phrases (comma separated): ", 'avantlink_rp_textdomain' ) . '</label></p><p>';

  $keyword = get_post_meta($post->ID, '_avantlink_rp_keyword', true);

  echo '<input type="text" name="avantlink_rp_keyword" value="'.$keyword.'" size="25" /></p>';

  echo '<p><label for="avantlink_rp_nkeyword">' . __("Negative Keywords (comma separated): ", 'avantlink_rp_textdomain' ) . '</label></p><p>';

  $nkeyword = get_post_meta($post->ID, '_avantlink_rp_nkeyword', true);

  echo '<input type="text" name="avantlink_rp_nkeyword" value="'.$nkeyword.'" size="25" /></p>';

  echo '<p><label for="avantlink_rp_num_results">' . __("Number of Results: ", 'avantlink_rp_textdomain' ) . '</label> ';

  $results = get_post_meta($post->ID, '_avantlink_rp_num_results', true);

  echo '<input type="text" name="avantlink_rp_num_results" value="'.$results.'" size="10" /></p>';

}

// prints the edit form for pre-WordPress 2.5 post/page
function avantlink_rp_old_custom_box() {

  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="avantlink_rp_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' .
        __( 'My Post Section Title', 'avantlink_rp_textdomain' ) . "</h3></div>";

  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

  avantlink_rp_inner_custom_box();

  // end wrapper

  echo "</div></div></fieldset></div>\n";

}

// when the post is saved, saves our custom data
function avantlink_rp_save_postdata( $post_id ) {

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['avantlink_rp_noncename'], plugin_basename(__FILE__) )) {
    return $post_id;
  }

  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  // to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
    return $post_id;


  // Check permissions
  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
      return $post_id;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
      return $post_id;
  }

  // OK, we're authenticated: we need to find and save the data

  $mydata = $_POST['avantlink_rp_keyword'];
  update_post_meta($post_id, '_avantlink_rp_keyword', $mydata);

  $mydata = $_POST['avantlink_rp_nkeyword'];
  update_post_meta($post_id, '_avantlink_rp_nkeyword', $mydata);

  $mydata = $_POST['avantlink_rp_num_results'];
  update_post_meta($post_id, '_avantlink_rp_num_results', $mydata);

  return true;

}

?>