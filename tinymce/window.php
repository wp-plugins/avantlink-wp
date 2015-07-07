<?php

// look up for the path
require_once( dirname( dirname(__FILE__) ) .'/Avantlink_editorbtn-config.php');

global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
	wp_die(__("You are not allowed to be here"));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Avantlink Ad Selection Tool</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<style>
p {margin : 5px 0;}

a img {border : none;}
#adsearch_main {
	width : 600px;
	margin : 0 auto;
	font-size : 12px;
	font-family : arial;
	overflow : hidden;
}

#ad_search_form {
	background-color : #f5f5f5;
	border : 1px solid #ccc;
	padding : 10px;
}

.ad_results_table td {
	padding : 10px 0;
	border-bottom : 1px dashed #ccc;
}


.ad_results_table tr:hover {
background : #eee;
}

.ad_results_table td input {
	padding : 0 10px !important;
	margin : 0 10px !important;
	display : block;
	float : left;
}

#merchant_sort, #search_ad_type {padding : 0 10px 0 0; float : left;}
#search_submit { float : right;}
#insert_submit { float : left; margin : 10px 0;}

.clear {clear : both;}
.error { background-color : #fbc1c1; border : 1px solid #e10202; text-align : center; padding : 5px 0;}

#link .panel_wrapper, #link div.current {
    height: auto !important;
    overflow : hidden;
}

.white_bg { background-color : #fff; padding : 2px; width : 500px; }

</style>


	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

	<!-- Get value of selected radio btn -->
	<script language="JavaScript">

		function getSelectValue() {
			for (index=0; index < document.link_form.merchant_id.length; index++) {
				if (document.link_form.merchant_id[index].selected) {
					var radioValue = document.link_form.merchant_id[index].value;
					return radioValue;
					break;
				}
			}
		}

<?php
		$strWebsitesJs = '';
		$affiliate_id = get_option('avantlink_affiliate_id');
		$auth_key = get_option('avantlink_auth_key');
		$arrMerchantList = avantlink_api_get_merchants( $affiliate_id, $auth_key );
		foreach ($arrMerchantList as $arrMerchant) {
			$lngMerchantId = $arrMerchant['Merchant_Id'];
			$strMerchantURL = $arrMerchant['Merchant_URL'];

			$strWebsitesJs .= ' '.$lngMerchantId.':"'.$strMerchantURL.'",';
		}
		$strWebsitesJs = rtrim($strWebsitesJs, ",");
		echo 'var arrWebsites = {'.$strWebsitesJs.'};';
?>

		function updateLandingPageURL() {
			var merchantId = getSelectValue();
			if (arrWebsites[merchantId] != "") {
				document.link_form.prod_link.value = arrWebsites[merchantId];
			}
		}

	</script>


	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}

	function insertavantlinkHWLink(value) {

		var tagtext;

		var rss = document.getElementById('adpicker_panel');

		// who is active ?
		if (rss.className.indexOf('current') != -1) {
			if (value != '' )
				tagtext = value;
			else
				tinyMCEPopup.close();
		}
 
// New tinyMCE code 9-10-2014
	if(window.tinyMCE) {

    /* get the TinyMCE version to account for API diffs */
    var tmce_ver=window.tinyMCE.majorVersion;

    if (tmce_ver>="4") {
        window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
    } else {
        window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
    }

    tinyMCEPopup.editor.execCommand('mceRepaint');
    tinyMCEPopup.close();
    }
    return;
}
// End tinyMCE code

// Function for editor link - value for link text needs to get encoded

	function insertCustomLink() {

		var tagtext;
		var rss = document.getElementById('link_panel');

		// who is active ?
		if (rss.className.indexOf('current') != -1) {
			var linkurl = document.getElementById('prod_link').value;
			var linktext = document.getElementById('link_text').value;
			var ctctext = document.getElementById('ctc_text').value;
			var merchid = getSelectValue();

			if (linkurl != '' ) {
				tagtext = '<a href="http://www.avantlink.com/click.php?tt=cl&mi=' + merchid + '&pw=<?php echo get_option('avantlink_website_id'); ?>&ctc=' + encodeURIComponent(ctctext) + '&url=' + encodeURIComponent(linkurl) + '">' + linktext + '</a>';
			}
			else {
				tinyMCEPopup.close();
				return;
			}
		}
		if(window.tinyMCE) {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
			//Peforms a clean up of the current editor HTML.
			//tinyMCEPopup.editor.execCommand('mceCleanup');
			//Repaints the editor. Sometimes the browser has graphic glitches.
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}

		return;
	}
	</script>

	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">

<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<div class="tabs">
		<ul>
			<li id="adpicker_tab" class="current"><span><a href="javascript:mcTabs.displayTab('adpicker_tab','adpicker_panel');" onmousedown="return false;"><?php _e("Ad Campaigns", 'avantlinkHW'); ?></a></span></li>
			<li id="link_tab"><span><a href="javascript:mcTabs.displayTab('link_tab','link_panel');" onmousedown="return false;"><?php _e("Custom Links", 'avantlinkHW'); ?></a></span></li>

		</ul>
	</div>

	<div class="panel_wrapper">
		<!-- ad picker panel -->
		<div id="adpicker_panel" class="panel current">

			<div id="ad_search_form">
    	<form role="search" method="get" id="product_searchform" action="" style="text-align:left;">

	<div id="merchant_sort">Merchant :
		<select name="merchant_id">
		<option value="0"></option>
		<?php
			if (isset($_GET['merchant_id'])) {
				$merchant_id = intval($_GET['merchant_id']);
			}
			else { $merchant_id = 0; }

			$affiliate_id = get_option('avantlink_affiliate_id');
			$auth_key = get_option('avantlink_auth_key');
			foreach ($arrMerchantList as $arrMerchant) {
				$lngMerchantId = $arrMerchant['Merchant_Id'];
				$strMerchantName = $arrMerchant['Merchant_Name'];

				if ($merchant_id == $lngMerchantId) {
					$strSelected = ' selected="selected"';
				}
				else { $strSelected = ''; }

				echo '<option value="' . $lngMerchantId . '"' . $strSelected . '>' . htmlspecialchars($strMerchantName) . '</option>';
			}

		?>
		</select>
	</div> <!-- END Merchant Sort -->

	<div id="search_ad_type">Ad Type :
		<select name="ad_type">
			<?php if(isset($_GET['ad_type'])) { echo '<option value="'.$_GET['ad_type'].'">'.urldecode($_GET['ad_type']).'</option>'; } ?>
			<option value="text">text</option>
			<option value="image">image</option>
			<option value="flash">flash</option>
			<option value="html">html</option>
			<option value="video">video</option>
			<option value="dotd-text">DOTD text</option>
			<option value="dotd-html">DOTD html</option>
		</select>
	</div> <!-- END Ad type -->

    <div id="search_submit"><input type="submit" id="searchsubmit" value="Search" /></div>

    </form>
    <div class="clear"> </div>
   	</div> <!-- End Ad Search form -->

		<form name="adpicker_form" action="#">
		<br />
		<table class="ad_results_table"border="0" cellpadding="4" cellspacing="0">


    <!-- *** adpicker.php *** -->
	<p><b> Ad Search Results</b> - Find the ad you want to insert. Then click insert next to that ad. </p>
	<?php
	$merch_req_id = $_GET['merchant_id'];
	$ad_type_req = $_GET['ad_type'];

	if ($merch_req_id > 0 && $ad_type_req != '') {
		$website_id = get_option('avantlink_website_id');
		$arrAdCampaigns = avantlink_api_get_ad_campaigns( $affiliate_id, $website_id, $merch_req_id, $ad_type_req );
	}
	else {
		$arrAdCampaigns = array();
	}
	if (count($arrAdCampaigns) == 0) {
		echo '<h3 class="error">No Ad Search Results </h3>';
	}
	else {
		foreach ($arrAdCampaigns as $arrCampaign) {

			$strAdValue = '[avantlink_ad merchant=&quot;' . htmlspecialchars(str_replace('"', '', $arrCampaign['Merchant_Name'])) . '&quot; title=&quot;' . htmlspecialchars(str_replace('"', '', $arrCampaign['Ad_Title'])) . '&quot;]'. htmlspecialchars($arrCampaign['Ad_Content']) .'[/avantlink_ad]';
			echo	'<tr valign="center">' .
					'<td><input type="submit" id="insert" name="insert" value="Insert" onclick="insertavantlinkHWLink(\'' . str_replace("'", "\'", $strAdValue) . '\');" /></td>' .
					'<td>' .
						'<p>' . htmlspecialchars($arrCampaign['Merchant_Name']) . ' : ' . $arrCampaign['Ad_Title'] . '</p>' .
						'<p>' . $arrCampaign['Ad_Content'] . '</p>' .
					'</td>' .
					'</tr>';
		}
	}
	?>
	<!-- ***  END adpicker.php *** -->

</table>

 <div class="clear"> </div>


<div class="mceActionPanel">
	<div style="float: right">
	<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'avantlinkHW'); ?>" onclick="tinyMCEPopup.close();" />
	</div>

	<div class="clear"> </div>
	</div>
</form>
</div><!-- end adpicker panel -->


<div id="link_panel" class="panel">
<form name="link_form" action="#">
<br />
	<table border="0" cellpadding="4" cellspacing="0">
		<tr>
		<td nowrap="nowrap"> Merchant :
		</td>
		<td>
			<select name="merchant_id" onchange="updateLandingPageURL()">
			<option value="0"></option>
			<?php
			if (isset($_GET['merchant_id'])) {
				$merchant_id = intval($_GET['merchant_id']);
			}
			else { $merchant_id = 0; }

			foreach ($arrMerchantList as $arrMerchant) {
				$lngMerchantId = $arrMerchant['Merchant_Id'];
				$strMerchantName = $arrMerchant['Merchant_Name'];

				if ($merchant_id == $lngMerchantId) {
					$strSelected = ' selected="selected"';
				}
				else { $strSelected = ''; }

				echo '<option value="' . $lngMerchantId . '"' . $strSelected . '>' . htmlspecialchars($strMerchantName) . '</option>';
			}
			?>
		</select>
		</td>
		</tr>

          <tr>
            <td nowrap="nowrap"><label for="rsstag">Merchant Landing Page URL :</label></td>
            <td><input type="text" id="prod_link" name="prod_link" style="width: 350px" />
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="link_text">Link Display Text :</label></td>
            <td><input type="text" id="link_text" name="link_text" style="width: 350px" />
            </td>
          </tr>
		  <tr>
            <td nowrap="nowrap"><label for="ctc_text">Custom Tracking Code (optional):</label></td>
            <td><input type="text" id="ctc_text" name="ctc_text" style="width: 350px" />
            </td>
          </tr>

        </table>

        <div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'avantlinkHW'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="Insert" onclick="insertCustomLink();" />
		</div>
		</div>
            <div class="clear"> </div>


		</div><!-- end link panel -->


	</div>

</body>
</html>
