<?php
/*
Plugin Name: AvantLink WP
Plugin URI: http://www.avantlink.com/affiliates/affiliate-tool-center/
Description: AvantLink affiliate plugin for WordPress.  Features include easy-to-use integration with Ad Campaign, Affiliate Link Encoder, Custom Link, Product Ad Widget, and Product Search API tools.  Requires active affiliate account with AvantLink.
Version: 1.0.9
Author: AvantLink.com
Author URI: http://www.avantlink.com/
License: GPLv2
*/

/*
	Copyright 2011 AvantLink.com (http://www.avantlink.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Add admin menus
 */
add_action('admin_menu', 'avantlink_add_pages');

/**
 * Action function for adding admin menus
 */
function avantlink_add_pages() {

	// add a new submenu under Settings:
	add_options_page(__('AvantLink WP','avantlink-wp'), __('AvantLink WP','avantlink-wp'), 'manage_options', 'avantlink-wp', 'avantlink_settings_page');

}

/**
 * Handle admin settings page display
 */
if (is_admin() && $_GET['page'] == 'avantlink-wp') {

	// file that constructs settings page
	include 'settings.php';

	wp_register_style('jquery-ui', plugins_url('/css/smoothness/jquery-ui-1.8.7.custom.css', __FILE__));
	wp_enqueue_style('jquery-ui');

	// Remove "offending" version of jquery-ui scripts, if present
	wp_deregister_script('jquery-ui-core');
	wp_deregister_script('jquery-ui-tabs');

	wp_enqueue_script('jquery');
	wp_enqueue_script('avantlink-jquery-ui', plugins_url('/js/jquery-ui-1.8.7.custom.min.js', __FILE__));
	wp_enqueue_script('avantlink-admin-tabs', plugins_url('/js/avantlink-admin-tabs.js', __FILE__));

}

/**
 * Is the plugin fully configured/enabled?
 */
$avantlink_is_enabled = get_option('avantlink_is_enabled');

/**
 * If improperly configured, display error message in admin interface
 */
if (is_admin() && !($avantlink_is_enabled) && $_GET['page'] != 'avantlink-wp') {

	add_action( 'admin_notices', 'avantlink_activation_notice');

}

/**
 * Action function to display error notice about incomplete configuration
 */
function avantlink_activation_notice() {

	echo '<div class="error"><p><strong>The <a href="options-general.php?page=avantlink-wp">AvantLink WP</a> plugin is not fully configured.</strong></p></div>';

}

/**
 * Only enable functionality if the plugin is properly configured
 */
if ($avantlink_is_enabled) {

	// file that constructs/controls page/post meta box
	include 'meta-box-rp.php';
	include 'meta-box-ad-widget.php';

	// files that construct plugin's widgets
	include 'widget-search-form.php';
	// DGC - Disable "Ad Campaign" widget for now: include 'widget-ad-tool.php';
	include 'widget-product-ad-widget.php';
	include 'widget-related-products.php';

	// function that outputs related products
	include 'display_related_products.php';

	// function that outputs product search results
	include 'display_product_search_results.php';

	// hook for including "related products" style sheet
	add_action('wp_enqueue_scripts', 'avantlink_rp_style');

	// hook for including "product search" style sheet
	add_action('wp_enqueue_scripts', 'avantlink_ps_style');

	// hook for ALE inclusion, only display if plugin is properly configured
	add_action('wp_enqueue_scripts', 'avantlink_add_ale');

	// hook for product search form, only display if plugin is configured to do so
	if (get_option('avantlink_search_url') != '') {
		if (function_exists('avantlink_display_product_search_results')) {
			if (avantlink_is_product_search_page()) {
				add_filter('the_content', 'avantlink_do_product_search');
				add_filter('the_title','avantlink_add_product_search_term_page_title');
				add_filter('wp_title','avantlink_add_product_search_term_site_title');
			}
		}
	}

}

/**
 * Action function to output related products style sheet
 */
function avantlink_rp_style() {
	wp_enqueue_style('avantlink_rp_style', plugins_url('/css/rp_style.css', __FILE__));
}

/**
 * Action function to output product search style sheet
 */
function avantlink_ps_style() {
	wp_enqueue_style('avantlink_ps_style', plugins_url('/css/ps_style.css', __FILE__));
}

/**
 * Action function to output ALE code
 */
function avantlink_add_ale() {

	$deactivate_ALE = intval(get_option('avantlink_ale_deactivate'));
	if ($deactivate_ALE != 1) {
		$ale_subscription_id = intval(get_option('avantlink_ale_id'));
		if ($ale_subscription_id > 0) {
			$strSubscriptionUrl = 'https://www.avantlink.com/ale/ale.php?ti=' . $ale_subscription_id;
		}
		else {
			$affiliate_id = intval(get_option('avantlink_affiliate_id'));
			$website_id = intval(get_option('avantlink_website_id'));

			// Use default/pseudo subscription
			$strSubscriptionUrl = 'https://www.avantlink.com/ale/ale.php?p=' . $affiliate_id . '&pw=' . $website_id;
		}

		wp_enqueue_script('avantlink-ale', $strSubscriptionUrl, null, null, true);
	}

}

/**
 * Test function to see if we're on the product search results page
 */
function avantlink_is_product_search_page() {
	$strUrl = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'];
	$strUrl .= preg_replace('/[&?]ps=(.*)/', '', $_SERVER['REQUEST_URI']);
	if (rtrim(trim(get_option('avantlink_search_url')), '/') == rtrim($strUrl, '/')) {
		return true;
	}
	return false;
}

/**
 * Filter to show product search results
 */
function avantlink_do_product_search($content) {
	$content .= avantlink_display_product_search_results(true);
	return $content;
}

/**
 * Filter to add the product search term to the page title
 */
function avantlink_add_product_search_term_page_title($strTitle) {
	$display_search_term_opt_name = 'avantlink_search_display_term';
	$display_search_term_opt_val = get_option ($display_search_term_opt_name);
	if ($display_search_term_opt_val !== '1' && $strTitle == 'Search Results' && isset($_GET['ps']) && trim($_GET['ps']) != '') {
		$strChangeTitle = $strTitle . " for " . "'" . htmlspecialchars(strip_tags($_GET['ps'])) . "'";
		return $strChangeTitle;
	}
	else {
		return $strTitle;
	}
}

/**
 * Filter to add the product search term to the <title> element
 */
function avantlink_add_product_search_term_site_title($strTitle) {
	$display_search_term_opt_name = 'avantlink_search_display_term';
	$display_search_term_opt_val = get_option ($display_search_term_opt_name);
	if ($display_search_term_opt_val !== '1' && isset($_GET['ps']) && trim($_GET['ps']) != '') {
		$strChangeTitle = "Search '" . htmlspecialchars(strip_tags($_GET['ps'])) . "' | " . get_bloginfo( 'name' );
		return $strChangeTitle;
	}
	else {
		return $strTitle;
	}
}

// register the custom plugin
function avantlink_button_register($button){

	array_push($button, "separator", "avlbutton");

	return $button;

}

// load the the custom button plugin
function avantlink_attach($plugin_array){

	$url = trim(plugins_url('/editor_plugin.js', __FILE__));

	$plugin_array['avlbutton'] = $url;

	return $plugin_array;

}

/**
 * Fetch contents from a URL.
 * Uses cURL if available, or file_get_contents, or fopen if nothing else.
 */
function avantlink_get_url_contents($strRequestUrl)
{
	$strResponse = '';

	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $strRequestUrl);
		if (defined('CURLOPT_ENCODING')) {
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = @curl_exec($ch);
		if ($result !== false && curl_errno($ch) <= 0) {
			$strResponse = $result;
		}
		@curl_close($ch);
	}
	else {
		if (function_exists('file_get_contents')) {
			$strResponse = file_get_contents($strRequestUrl);
		}
		else {
			$fp = fopen($strRequestUrl, 'rb');
			if ($fp !== false) {
				while (!(feof($fp))) {
					$strResponse .= fread($fp, 4096);
				}
				fclose($fp);
			}
		}
	}

	return($strResponse);
}

/**
 * Parse a tab-delimited result set into an associative array.
 * Useful for processing API responses.
 */
function avantlink_parse_tab_delim_response($strResponse)
{
	$arrFinal = array();

	// Split the response into discrete lines (records)
	$strResponse = str_replace("\r", '', $strResponse);
	$arrLines = explode("\n", $strResponse);
	$intLineCount = count($arrLines);
	if ($intLineCount < 2) {
		return($arrFinal);
	}

	// Determine field order by the header line
	$arrFields = explode("\t", $arrLines[0]);
	$intFieldCount = count($arrFields);
	if ($intFieldCount < 1) {
		return($arrFinal);
	}
	for ($i = 0; $i < $intFieldCount; $i++) {
		$arrFields[$i] = str_replace(' ', '_', $arrFields[$i]);
	}

	// Parse the remaining data into associative arrays
	for ($i = 1; $i < $intLineCount; $i++) {
		if (trim($arrLines[$i]) != '') {
			$arrRecord = explode("\t", $arrLines[$i]);
			$arrCurrent = array();

			$intFieldPos = 0;
			foreach ($arrFields as $strFieldName) {
				$arrCurrent[$strFieldName] = $arrRecord[$intFieldPos];
				$intFieldPos++;
			}

			$arrFinal[] = $arrCurrent;
		}
	}

	return($arrFinal);
}

/**
 * Get the list of websites for an affiliate.
 */
function avantlink_api_get_websites($affiliate_id, $auth_key)
{
	$strUrl = 'https://www.avantlink.com/api.php?module=WebsiteFeed&output=tab&affiliate_id=' . $affiliate_id . '&auth_key=' . $auth_key;
	$strResponse = avantlink_get_url_contents($strUrl);
	if ($strResponse == '' || strpos($strResponse, 'Website Id') === false || strpos($strResponse, 'Website Name') === false) {
		return(array());
	}

	return(avantlink_parse_tab_delim_response($strResponse));
}

/**
 * Get the list of merchants that a given affiliate is active with.
 */
function avantlink_api_get_merchants($affiliate_id, $auth_key)
{
	$strUrl = 'https://www.avantlink.com/api.php?module=AssociationFeed&output=tab&affiliate_id=' . $affiliate_id . '&auth_key=' . $auth_key;
	$strResponse = avantlink_get_url_contents($strUrl);
	if ($strResponse == '' || strpos($strResponse, 'Merchant Id') === false || strpos($strResponse, 'Merchant Name') === false) {
		return(array());
	}

	$arrResults = array();
	$arrMerchants = avantlink_parse_tab_delim_response($strResponse);
	foreach ($arrMerchants as $arrMerchant) {
		if ($arrMerchant['Association_Status'] == 'active') {
			$arrResults[] = $arrMerchant;
		}
	}

	return($arrResults);
}

/**
 * Get the list of subscriptions for a given tool type, for an affiliate.
 */
function avantlink_api_get_subscriptions($affiliate_id, $auth_key, $subscription_type)
{
	$strUrl = 'https://www.avantlink.com/api.php?module=SubscriptionFeed&output=tab&affiliate_id=' . $affiliate_id . '&auth_key=' . $auth_key . '&subscription_type=' . $subscription_type;
	$strResponse = avantlink_get_url_contents($strUrl);
	if ($strResponse == '' || strpos($strResponse, 'Subscription Id') === false || strpos($strResponse, 'Subscription Name') === false) {
		return(array());
	}

	return(avantlink_parse_tab_delim_response($strResponse));
}

/**
 * Get the list of ad campaigns for a given affiliate/merchant/ad type combination.
 */
function avantlink_api_get_ad_campaigns($affiliate_id, $website_id, $merchant_id, $ad_type)
{
	$strUrl = 'https://www.avantlink.com/api.php?module=AdSearch&output=tab&affiliate_id=' . $affiliate_id . '&website_id=' . $website_id;
	$strUrl .= '&merchant_id=' . urlencode($merchant_id);
	$strUrl .= '&ad_type=' . urlencode($ad_type);
	$strUrl .= '&integration_type=dynamic';
	$strResponse = avantlink_get_url_contents($strUrl);
	if ($strResponse == '' || strpos($strResponse, 'Ad Id') === false || strpos($strResponse, 'Ad Content') === false) {
		return(array());
	}

	return(avantlink_parse_tab_delim_response($strResponse));
}

/**
 * Perform a product search for an affiliate.
 */
function avantlink_api_get_product_search($affiliate_id, $website_id, $search_term, $search_result_count = null, $sort_order = null, $adv_syntax = null, $result_options = null)
{
	$strUrl = 'https://www.avantlink.com/api.php?module=ProductSearch&output=tab&affiliate_id=' . $affiliate_id . '&website_id=' . $website_id;
	$strUrl .= '&search_term=' . urlencode($search_term);
	if (isset($search_result_count) && $search_result_count != null) {
		$strUrl .= '&search_results_count=' . intval($search_result_count);
	}
	if (isset($sort_order) && $sort_order != null) {
		$strUrl .= '&search_results_sort_order=' . urlencode($sort_order);
	}
	if (isset($adv_syntax) && $adv_syntax != null) {
		$strUrl .= '&search_advanced_syntax=' . urlencode($adv_syntax);
	}
	if (isset($result_options) && $result_options != null) {
		$strUrl .= '&search_results_options=' . urlencode($result_options);
	}

	$strResponse = avantlink_get_url_contents($strUrl);
	if ($strResponse == '' || strpos($strResponse, 'Product Id') === false || strpos($strResponse, 'Product Name') === false) {
		return(array());
	}

	return(avantlink_parse_tab_delim_response($strResponse));
}

class Avantlink {

	function Avantlink() {
		global $wp_version;

		// Check for WP2.6 installation
		if (!defined ('IS_WP26')) {
			define('IS_WP26', version_compare($wp_version, '2.6', '>=') );
		}

		//This works only in WP2.6 or higher
		if ( IS_WP26 == FALSE) {
			add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, the AvantLink WP plugin works only under WordPress 2.6 or higher',"avantlinkHW") . '</strong></p></div>\';'));
			return;
		}

		// define URL
		define('avantlink_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );

		include_once (dirname (__FILE__)."/lib/shortcodes.php");
		include_once (dirname (__FILE__)."/tinymce/tinymce.php");
	}

}

// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', create_function( '', 'global $avantlink; $avantlink = new Avantlink();' ) );
?>
