<?php
// avantlink_settings_page() displays the page content for the Test settings submenu
function avantlink_settings_page() {
	global $wp_version;

    //must check that the user has the required capability
    if (!current_user_can('manage_options')) {

		wp_die( __('You do not have sufficient permissions to access this page.') );

	}

    // variables for the field and option names

    $affiliate_id_opt_name = 'avantlink_affiliate_id';
    $affiliate_id_data_field_name = 'avantlink_affiliate_id';

    $auth_key_opt_name = 'avantlink_auth_key';
    $auth_key_data_field_name = 'avantlink_auth_key';

	$website_id_opt_name = 'avantlink_website_id';
    $website_id_data_field_name = 'avantlink_website_id';

	$deactivate_ALE_opt_name = 'avantlink_ale_deactivate';
    $deactivate_ALE_data_field_name = 'avantlink_ale_deactivate';

    $display_search_term_opt_name = 'avantlink_search_display_term';
    $display_search_term_field_name = 'avantlink_search_display_term';

	$ale_subscription_id_opt_name = 'avantlink_ale_id';
    $ale_subscription_id_data_field_name = 'avantlink_ale_id';

	$search_url_opt_name = 'avantlink_search_url';
    $search_url_data_field_name = 'avantlink_search_url';

	$search_results_count_opt_name = 'avantlink_search_results_count';
    $search_results_count_data_field_name = 'avantlink_search_results_count';

    $title_opt_name = 'avantlink_related_title';
    $title_data_field_name = 'avantlink_related_title';

    $num_results_opt_name = 'avantlink_related_num_results';
    $num_results_data_field_name = 'avantlink_related_num_results';

    $keyword_opt_name = 'avantlink_related_keyword';
    $keyword_data_field_name = 'avantlink_related_keyword';

    $nkeyword_opt_name = 'avantlink_related_nkeyword';
    $nkeyword_data_field_name = 'avantlink_related_nkeyword';

    $all_posts_opt_name = 'avantlink_related_all_posts';
    $all_posts_data_field_name = 'avantlink_related_all_posts';

    $hidden_field_name = 'avantlink_submit_mode';

    // read in existing option value from database
    $affiliate_id_opt_val = get_option( $affiliate_id_opt_name );
    $auth_key_opt_val = get_option( $auth_key_opt_name );
	$website_id_opt_val = get_option( $website_id_opt_name );
	$deactivate_ALE_opt_val = get_option( $deactivate_ALE_opt_name );
	$display_search_term_opt_val = get_option( $display_search_term_opt_name );
	$ale_subscription_id_opt_val = get_option( $ale_subscription_id_opt_name );
	$search_url_opt_val = get_option( $search_url_opt_name );
	$search_results_count_opt_val = get_option( $search_results_count_opt_name );
    $title_opt_val = get_option( $title_opt_name );
    $num_results_opt_val = get_option( $num_results_opt_name );
    $keyword_opt_val = get_option( $keyword_opt_name );
    $nkeyword_opt_val = get_option( $nkeyword_opt_name );
    $all_posts_opt_val = get_option( $all_posts_opt_name );

    // Main affiliate identification configuration
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'S' ) {

		// read their posted value
        $affiliate_id_opt_val = $_POST[ $affiliate_id_data_field_name ];
        $auth_key_opt_val = $_POST[ $auth_key_data_field_name ];
		$website_id_opt_val = $_POST[ $website_id_data_field_name ];

        // save the posted value in the database
        update_option( $affiliate_id_opt_name, $affiliate_id_opt_val );
        update_option( $auth_key_opt_name, $auth_key_opt_val );
		update_option( $website_id_opt_name, $website_id_opt_val );

        // put an settings updated message on the screen
		echo '<div class="updated"><p><strong>Settings Saved</strong></p></div>';

    }

	// account validation
	$arrWebsiteList = avantlink_api_get_websites( $affiliate_id_opt_val, $auth_key_opt_val );

	$avantlink_is_enabled = false;
	$account_validation = "False";
	if (count($arrWebsiteList) > 0) {
		$account_validation = "True";
		if ($website_id_opt_val > 0) {
			$avantlink_is_enabled = true;
		}
	}

	update_option('avantlink_is_enabled', $avantlink_is_enabled);

	//
	// Only save settings from other tabs if the plugin is fully enabled
	//
	if ($avantlink_is_enabled) {

		// ALE configuration
	    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'A' ) {

			// read their posted value
			$deactivate_ALE_opt_val = $_POST[ $deactivate_ALE_data_field_name ];
			$ale_subscription_id_opt_val = $_POST[ $ale_subscription_id_data_field_name ];

	        // save the posted value in the database
			update_option( $deactivate_ALE_opt_name, $deactivate_ALE_opt_val );
			update_option( $ale_subscription_id_opt_name, $ale_subscription_id_opt_val );

	        // put an settings updated message on the screen
			echo '<div class="updated"><p><strong>Settings Saved</strong></p></div>';

	    }

		// Related Products configuration
		if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'R' ) {

			// read their posted value
	        $title_opt_val = $_POST[ $title_data_field_name ];
	        $num_results_opt_val = $_POST[ $num_results_data_field_name ];
	        $keyword_opt_val = $_POST[ $keyword_data_field_name ];
	        $nkeyword_opt_val = $_POST[ $nkeyword_data_field_name ];
	        $all_posts_opt_val = $_POST[ $all_posts_data_field_name ];

	        if (get_magic_quotes_gpc()) {
		        $title_opt_val = stripslashes($title_opt_val);
		        $keyword_opt_val = stripslashes($keyword_opt_val);
		        $nkeyword_opt_val = stripslashes($nkeyword_opt_val);
	        }

	        // save the posted value in the database
	        update_option( $title_opt_name, $title_opt_val );
	        update_option( $num_results_opt_name, $num_results_opt_val );
	        update_option( $keyword_opt_name, $keyword_opt_val );
	        update_option( $nkeyword_opt_name, $nkeyword_opt_val );
	        update_option( $all_posts_opt_name, $all_posts_opt_val );

	        // put an settings updated message on the screen
			echo '<div class="updated"><p><strong>Settings Saved</strong></p></div>';

	    }

		// Product Search configuration
	    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'P' ) {

			// read their posted value
			$search_url_opt_val = $_POST[ $search_url_data_field_name ];
			$search_results_count_opt_val = $_POST[ $search_results_count_data_field_name ];
			$display_search_term_opt_val = $_POST[ $display_search_term_field_name ];

	        if (get_magic_quotes_gpc()) {
				$search_url_opt_val = stripslashes($search_url_opt_val);
	        }

	        // save the posted value in the database
			update_option( $search_url_opt_name, $search_url_opt_val );
			update_option( $search_results_count_opt_name, $search_results_count_opt_val );
			update_option( $display_search_term_opt_name, $display_search_term_opt_val );

	        // put an settings updated message on the screen
			echo '<div class="updated"><p><strong>Settings Saved</strong></p></div>';

	    }

	}

	if (!($avantlink_is_enabled)) {
		echo '<div class="error"><p><strong>The <a href="options-general.php?page=avantlink-wp">AvantLink WP</a> plugin is not fully configured.</strong></p></div>';
	}

    // Now display the settings editing screen
    echo '<div class="wrap">';

    // Header
    echo "<h2>" . __( 'AvantLink WP Settings', '' ) . "</h2>";

	//
	// Main Affiliate Id / Authentication Form
	//
	$strFormAuthentication = '
<form action="options-general.php?page=avantlink-wp#tabs-1" method="post">
<input type="hidden" name="' . $hidden_field_name . '" value="S" />
<table class="form-table">
' . ($account_validation == "False" || $website_id_opt_val == '' ? '<tr valign="top"><th scope="row" colspan="3"><label for="blogname"><em><span style="color:red;">Please verify the highlighted fields<span></em></label></th></tr>' : '')
. '
<tr valign="top">
	<th scope="row" colspan="3"><strong><small>To utilize the AvantLink WP plugin you must identify your AvantLink.com affiliate account.</small></strong></th>
</tr>
<tr valign="top">
	<th scope="row" colspan="3"><em>Obtain your Affiliate Id and API Authorization Key <a target="_blank" href="https://www.avantlink.com/affiliate/view_edit_auth_key.php">here</a>.</em></th>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $affiliate_id_data_field_name . '"' . ($account_validation == "False" ? ' style="color:red;"' : '') . '>Affiliate Id:</label></th>
	<td>
		<input type="text" id="' . $affiliate_id_data_field_name . '" name="' . $affiliate_id_data_field_name . '" value="' . htmlspecialchars($affiliate_id_opt_val) . '" size="10" />
		(Integer numeric value.)
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $auth_key_data_field_name . '"' . ($account_validation == "False" ? ' style="color:red;"' : '') . '>API Authorization Key:</label></th>
	<td>
		<input type="text" id="' . $auth_key_data_field_name . '" name="' . $auth_key_data_field_name . '" value="' . htmlspecialchars($auth_key_opt_val) . '" size="40" />
		(Alphanumeric text value; a.k.a. auth_key.)
	</td>
</tr>
';
	if ($account_validation == "True") {
		$strFormAuthentication .= '
<tr valign="top">
	<th scope="row" colspan="3"><em>Review your Configured Websites <a target="_blank" href="https://www.avantlink.com/affiliate/edit_websites.php">here</a>.</em></th>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $website_id_data_field_name . '"' . ($website_id_opt_val == '' ? ' style="color: red;"' : '') . '>Website:</label></th>
	<td>
		<select id="' . $website_id_data_field_name . '" name="' . $website_id_data_field_name . '">
		<option value="0"></option>
';
		foreach ($arrWebsiteList as $arrWebsite) {
			$intWebsiteId = $arrWebsite['Website_Id'];
			$strWebsiteName = $arrWebsite['Website_Name'];
			if ($website_id_opt_val != '' && $website_id_opt_val == $intWebsiteId) {
				$strSelected = ' selected="selected"';
			}
			else { $strSelected = ''; }
			$strFormAuthentication .= '<option value="'. $intWebsiteId . '"' . $strSelected . '>' . htmlspecialchars($intWebsiteId . ' - ' . $strWebsiteName) . '</option>';
		}

		$strFormAuthentication .= '
		</select>
	</td>
</tr>
';
}

	$strFormAuthentication .= '
</table>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
</p>
</form>
';

	//
   	// If the plugin not fully configured, just show the
   	// main id/authentication form and leave now
   	//
    if (!($avantlink_is_enabled)) {
		echo $strFormAuthentication;
		return;
    }

	//
   	// If execution reaches here, then the plugin is fully configured
   	// So, show the entire set of configuration tabs
   	//

	//
	// ALE Configuration Form
	//
	$strFormAle = '
<form action="options-general.php?page=avantlink-wp#tabs-2" method="post">
<input type="hidden" name="' . $hidden_field_name . '" value="A" />
<table class="form-table">
<tr valign="top">
	<th scope="row" colspan="3">
		<strong><small>
		The Affiliate Link Encoder (ALE) uses JavaScript to convert direct-to-merchants links to affiliate-tracking links.<br />
		This functionality is enabled by default.
		</small></strong>
	</th>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $deactivate_ALE_data_field_name . '">Deactivate ALE?</label></th>
	<td>
		<input type="checkbox" id="' . $deactivate_ALE_data_field_name . '" name="' . $deactivate_ALE_data_field_name . '" value="1"' . ($deactivate_ALE_opt_val == 1 ? ' checked="checked"' : '') . ' />
		(The Affiliate Link Encoder is enabled by default with activation of this plugin.)

	</td>
</tr>
<tr valign="top">
	<th scope="row" colspan="3"><em>Review/manage your Affiliate Link Encoder Subscriptions <a target="_blank" href="https://www.avantlink.com/affiliate/affiliate_link_encoder_list.php">here</a>.</em></th>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $ale_subscription_id_data_field_name . '">ALE Subscription:</label></th>
	<td>
		<select id="' . $ale_subscription_id_data_field_name . '" name="'.$ale_subscription_id_data_field_name.'">
		<option></option>
';

	$arrSubscriptionList = avantlink_api_get_subscriptions( $affiliate_id_opt_val, $auth_key_opt_val, 'ale' );
	foreach ($arrSubscriptionList as $arrSubscription) {
		$intSubscriptionId = $arrSubscription['Subscription_Id'];
		$strSubscriptionName = $arrSubscription['Subscription_Name'];

		if ($ale_subscription_id_opt_val != '' && $ale_subscription_id_opt_val == $intSubscriptionId) {
			$strSelected = ' selected="selected"';
		}
		else { $strSelected = ''; }

		$strFormAle .= '<option value="'. $intSubscriptionId . '"' . $strSelected . '>' . htmlspecialchars($intSubscriptionId . ' - ' . $strSubscriptionName) . '</option>';
	}

	$strFormAle .= '
		</select>
		(If no ALE subscription is selected, then default options for your affiliate account will be used.)
	</td>
</tr>
</table>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
</p>
</form>
';

	//
	// Related Products Configuration Form
	//
	$strFormRelatedProducts = '
<form action="options-general.php?page=avantlink-wp#tabs-3" method="post">
<input type="hidden" name="' . $hidden_field_name . '" value="R" />
<table class="form-table">
<tr valign="top">
	<th scope="row" colspan="3">
		<strong><small>
		To output related products add "AvantLink Related Products" widget to your theme\'s sidebar.<br />
		Unless you check the option below to "Apply to All Posts", related products will only be shown on those posts for which you specifically define related products keywords.
		</small></strong>
	</th>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $all_posts_data_field_name . '">Apply To All Posts:</label></th>
	<td>
		<input type="checkbox" id="' . $all_posts_data_field_name . '" name="' . $all_posts_data_field_name . '" value="1"' . ($all_posts_opt_val == 1 ? ' checked="checked"' : '') . ' />
		(Display related products in all posts using the default keywords defined below.)
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $title_data_field_name . '">Title Text:</label></th>
	<td>
		<input type="text" id="' . $title_data_field_name . '" name="' . $title_data_field_name . '" value="' . htmlspecialchars($title_opt_val) . '" size="20" />
		(Defaults to &quot;Related Products&quot; if blank.)
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $keyword_data_field_name . '">Keywords/Phrases:</label></th>
	<td>
		<input type="text" id="' . $keyword_data_field_name . '" name="' . $keyword_data_field_name . '" value="' . htmlspecialchars($keyword_opt_val) . '" size="20" />
		(Comma separated values allowed. Default keywords/phrases to query. Keywords defined within the &quot;AvantLink Related Products&quot; meta box of specific posts will override these.)
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $nkeyword_data_field_name . '">Negative Keywords:</label></th>
	<td>
		<input type="text" id="' . $nkeyword_data_field_name . '" name="' . $nkeyword_data_field_name . '" value="' . htmlspecialchars($nkeyword_opt_val) . '" size="20" />
		(Comma separated values allowed. Default keywords to exclude from search results. Negative keywords defined within the &quot;AvantLink Related Products&quot; meta box of specific posts will override these.)
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $num_results_data_field_name . '">Number of Products:</label></th>
	<td>
		<input type="text" id="' . $num_results_data_field_name . '" name="' . $num_results_data_field_name . '" value="' . htmlspecialchars($num_results_opt_val) . '" size="20" />
		(The number of products to display, 10 maximum. Defaults to 5 if blank.)
	</td>
</tr>
<tr valign="top">
	<th scope="row" colspan="3"><em>To further style the related products widget, edit <code>wp-content/plugins/avantlink-wp/css/rp_style.css</code>.</em></th>
</tr>
</table>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
</p>
</form>
 ';

	//
	// Product Search Configuration Form
	//
	$strFormProductSearch = '
<form action="options-general.php?page=avantlink-wp#tabs-4" method="post">
<input type="hidden" name="' . $hidden_field_name . '" value="P" />
<table class="form-table">
<tr valign="top">
	<th scope="row" colspan="3"><strong><small>To display the product search form add "AvantLink Product Search Form" widget to your theme\'s sidebar.<br />
	Then
	' . (version_compare($wp_version, '3.0.0', '<') ? 'create a blank page' : '<a href="post-new.php?post_type=page">create a blank page</a>') . '
	titled "Search Results" and paste the url of the newly created page in the "Search Page URL" field below.</small></strong>
	</th>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $display_search_term_field_name . '">Do not display search terms?</label></th>
	<td>
		<input type="checkbox" id="' . $display_search_term_field_name . '" name="' . $display_search_term_field_name . '" value="1"' . ($display_search_term_opt_val == 1 ? ' checked="checked"' : '') . ' />
		(Display of search terms enabled by default. Search terms are displayed in title of search.)

	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $search_url_data_field_name . '">Search Page URL:</label></th>
	<td><input type="text" id="' . $search_url_data_field_name . '" name="' . $search_url_data_field_name . '" value="' . htmlspecialchars($search_url_opt_val) . '" size="80" /></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="' . $search_results_count_data_field_name . '">Number of Results:</label></th>
	<td>
		<input type="text" id="' . $search_results_count_data_field_name . '" name="' . $search_results_count_data_field_name . '" value="' . htmlspecialchars($search_results_count_opt_val) . '" size="5" />
		(The number of search results to display. Defaults to 10 if blank.)
	</td>
</tr>
<tr valign="top">
	<th scope="row" colspan="3"><em>To further style search results, edit <code>wp-content/plugins/avantlink-wp/css/ps_style.css</code>.</em></th>
</tr>
</table>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
</p>
</form>
';

	//
	// Full tab-view
	//
	$strOutputHtml = '
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Settings</a></li>
		<li><a href="#tabs-2">Affiliate Link Encoder</a></li>
		<li><a href="#tabs-3">Related Products</a></li>
		<li><a href="#tabs-4">Product Search</a></li>
	</ul>
	<div id="tabs-1">
	' . $strFormAuthentication . '
	</div>
	<div id="tabs-2">
	' . $strFormAle . '
	</div>
	<div id="tabs-3">
	' . $strFormRelatedProducts . '
	</div>
	<div id="tabs-4">
	' . $strFormProductSearch . '
	</div>
</div>
';

	echo $strOutputHtml;
	return;
}
?>