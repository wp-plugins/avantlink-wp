<?php

/**
 * @author Deanna Schneider
 * @copyright 2008
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class Avantlink_shortcodes {
	var $count = 1;
	// register the new shortcodes
	function avantlink_shortcodes() {
		add_shortcode( 'avantlinkHW', array(&$this, 'show_RSS') );
	}

	function show_RSS( $atts ) {
			global $avantlink;

		extract(shortcode_atts(array('id'=> false), $atts ));

		//$out = __('[TEST THIS]','avantlinkHW');
		$out = '<span style="color: red">Hello ' . $id . '</span>';
		return $out;
	}
}

// let's use it
$avantlinkShortcodes = new Avantlink_Shortcodes;

// ### Need to add a call for the pw from the plugin settings page

function merch_link_func($atts,$content) {
	extract(shortcode_atts(array(
		'link' => 'link url',
		'merchid' => 'merch id',
		'ctc' => 'ctc id'
	), $atts));

	$affiliate_id = get_option('avantlink_affiliate_id');

	return '<a href="http://www.avantlink.com/click.php?tt=cl&amp;mi='.$merchid.'&amp;pw='.$affiliate_id.'&amp;ctc='.$ctc.'&amp;url='. urlencode($link).'">'.$content.'</a>';
	}
add_shortcode('av_merch_link', 'merch_link_func');

// Output script tag in shortcode from visual editor
function avantlink_avantlink_ad_func($atts,$content) {
		$output = html_entity_decode($content);

	return $output;
}
add_shortcode('avantlink_ad', 'avantlink_avantlink_ad_func');

?>
