<?php
// related products output
function avantlink_display_related_products() {

	// get global post information
	global $post;
	$id = $post->ID;

	// get post specific settings
	$keyword = get_post_meta($id, '_avantlink_rp_keyword', true);
	$nkeyword = get_post_meta($id, '_avantlink_rp_nkeyword', true);
	$results = get_post_meta($id, '_avantlink_rp_num_results', true);

	// get plugin options
	$affiliate_id = get_option('avantlink_affiliate_id');
	$website_id = get_option('avantlink_website_id');
	$d_results = get_option('avantlink_related_num_results');
	$d_keyword = get_option('avantlink_related_keyword');
	$d_nkeyword = get_option('avantlink_related_nkeyword');
	$d_title = get_option('avantlink_related_title');
	$all_posts = get_option('avantlink_related_all_posts');

	// logic for global vs post specific settingd
	if($keyword != '') { $search_term = $keyword; } else { $search_term = $d_keyword; }
	if($nkeyword != '') { $nsearch_term = $nkeyword; } else { $nsearch_term = $d_nkeyword; }
	if($results != '') { $num_results = $results; } else { $num_results = $d_results; }
	if($d_title == '') { $title = "Related Products"; } else { $title = $d_title; }
	if($all_posts != 1 && $keyword == '') { $disabled = true; }
	$query = '';

	// format negative keywords for request
	$nsearch_query = '';
	$nsearch_terms = explode(',', $nsearch_term);
	$intSearchTermCount = count($nsearch_terms);
	for ($i = 0; $i < $intSearchTermCount; $i++) {
		if (trim($nsearch_terms[$i]) != '') {
			$nsearch_term = str_replace(' ', ' -', trim($nsearch_terms[$i]));
			$nsearch_query .= '-' . $nsearch_term . ' ';
		}
	}
	$nsearch_query = trim($nsearch_query);

	// format keyword for request
	$search_terms = explode(',', $search_term);
	$intSearchTermCount = count($search_terms);
	for($i = 0; $i < $intSearchTermCount; $i++) {
		if (trim($search_terms[$i]) != '') {
			$search_term = str_replace(' ', '+', $search_terms[$i]);
			if ($query == '') {
				$query = $search_term;
			}
			else { $query .= '|' . $search_term; }

			// If any were specified, append negative keywords to each keyword set
			if ($nsearch_query != '') {
				$query .= ' ' . $nsearch_query;
			}
		}
	}

	if($disabled == false) {
		$output = '';

		$arrProducts = avantlink_api_get_product_search( $affiliate_id, $website_id, $query, null, null, '1', 'nofollow' );
		$intProductCount = count($arrProducts);
		if ($intProductCount == 0) {
			$output = '<p>No related products found</p>';
		}
		else {

			// plugin container, title
			$output .= '<div id="avantlink_rp">';
			$output .= '<div id="rp_title"><h3 class="widget-title">'.$title.'</h3></div>';

			// max 10 results
			if($num_results > 10) { $num_results = 10; }
			if(!is_numeric($num_results)) { $num_results = 5; }
			if($intProductCount < $num_results) { $num_results = $intProductCount; }

			// for each item...
			for($i = 0; $i < $num_results; $i++) {

				// product variables
				$arrProduct = $arrProducts[$i];
				$merchant_name = $arrProduct['Merchant_Name'];
				$product_name = $arrProduct['Product_Name'];
				$retail_price = $arrProduct['Retail_Price'];
				$sale_price = $arrProduct['Sale_Price'];
				$thumbnail_image = $arrProduct['Large_Image'];
				$buy_url = $arrProduct['Buy_URL'];

				// trim prices
				$retail_price = substr($retail_price, 1);
				$sale_price = substr($sale_price, 1);

				// calculate percent off
				$percent_off = round(100 * ($retail_price - $sale_price) / $retail_price);
				if($percent_off != 0) { $percent_off_styled = ' '.$percent_off.'% Off'; }

				// build item
				$output .= '<div class="rp_item">';
				$output .= '<div class="rp_image"><a href="'.$buy_url.'" target="_blank"><img src='.$thumbnail_image.' /></a></div>';
				$output .= '<div class="rp_name"><span><a href="'.$buy_url.'" target="_blank">'.$product_name.'</a></span></div>';
				$output .= '<div class="prices"><a href="'.$buy_url.'" target="_blank"><span class="sale_price">$'.$sale_price.'</span><span class="percent_off">'.$percent_off_styled.'</span></a></div>';
				$output .= '</div>';

				unset($percent_off_styled);

			} // End, for each product record

			$output .= '<div class="clear"></div></div>';

		} // End, if any products were found

		echo $output;
	}

	return;

}
?>