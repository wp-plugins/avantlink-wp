<?php

// use the admin_menu action to define the custom boxes
add_action('admin_menu', 'avantlink_paw_widget_add_custom_box');

// use the save_post action to do something with the data entered
add_action('save_post', 'avantlink_paw_widget_save_postdata');

// adds a custom section to the "advanced" Post and Page edit screens
function avantlink_paw_widget_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'avantlink_paw_widget_sectionid', __( 'AvantLink Product Ad Widget', 'avantlink_paw_widget_textdomain' ),
                'avantlink_paw_widget_inner_custom_box', 'post', 'side' );
//
// DGC 2011-02-04 : Disable this widget on the page administration for now (allow it only for posts)
// We don't have code in place in widget-product-ad-widget.php to display a PAW on a given page
//
//    add_meta_box( 'avantlink_paw_widget_sectionid', __( 'AvantLink Product Ad Widget', 'avantlink_paw_widget_textdomain' ),
//                'avantlink_paw_widget_inner_custom_box', 'page', 'side' );
   }
}

// prints the inner fields for the custom post/page section
function avantlink_paw_widget_inner_custom_box() {

  global $post;

  // use nonce for verification
  echo '<input type="hidden" name="avantlink_paw_widget_noncename" id="avantlink_paw_widget_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  echo '<p><em><small>To display, add the "AvantLink Product Ad Widget" widget to your theme\'s sidebar.</small></em></p>';

  echo '<p><label for="avantlink_paw_id">' . __("PAW: ", 'avantlink_paw_widget_textdomain' ) . '</label> ';

  $id = get_post_meta($post->ID, '_avantlink_paw_id', true);

  $affiliate_id = get_option('avantlink_affiliate_id');
  $auth_key = get_option('avantlink_auth_key');

  echo '<select name="avantlink_paw_id">';
  echo '<option value="0"></option>';
  $arrSubscriptionList = avantlink_api_get_subscriptions( $affiliate_id, $auth_key, 'paw' );
	foreach ($arrSubscriptionList as $arrSubscription) {
		$intSubscriptionId = $arrSubscription['Subscription_Id'];
		$strSubscriptionName = $arrSubscription['Subscription_Name'];

		if ($id != '' && $id == $intSubscriptionId) {
			$strSelected = ' selected="selected"';
		}
		else { $strSelected = ''; }

		echo '<option value="'. $intSubscriptionId . '"' . $strSelected . '>' . htmlspecialchars($intSubscriptionId . ' - ' . $strSubscriptionName) . '</option>';
	}
  echo '</select>';

  echo '<p>Manage your Product Ad Widget subscriptions <a target="_blank" href="https://www.avantlink.com/affiliate/product_ad_widget.php">here</a>.</p>';

}

// when the post is saved, saves our custom data
function avantlink_paw_widget_save_postdata( $post_id ) {

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['avantlink_paw_widget_noncename'], plugin_basename(__FILE__) )) {
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

  $paw_id = $_POST['avantlink_paw_id'];
  $paw_url = '';

  if ($paw_id > 0) {
    $affiliate_id = get_option('avantlink_affiliate_id');
	$auth_key = get_option('avantlink_auth_key');

	$arrSubscriptionList = avantlink_api_get_subscriptions( $affiliate_id, $auth_key, 'paw' );
	foreach ($arrSubscriptionList as $arrSubscription) {
	  $intSubscriptionId = $arrSubscription['Subscription_Id'];
	  $strSubscriptionUrl = $arrSubscription['Subscription_Url'];

	  if ($paw_id == $intSubscriptionId) {
	    $paw_url = $strSubscriptionUrl;
		break;
	  }
	}
  }

  update_post_meta($post_id, '_avantlink_paw_id', $paw_id);
  update_post_meta($post_id, '_avantlink_paw_url', $paw_url);

  return true;
}
?>