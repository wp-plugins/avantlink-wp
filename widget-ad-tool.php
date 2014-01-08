<?php
/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'avantlink_at_widgets' );
/*
 * Register widget.
 */
function avantlink_at_widgets() {
	register_widget( 'AvantLink_AT_Widget' );
}
/*
 * Widget class.
 */
class AvantLink_AT_Widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	function AvantLink_AT_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'avantlink_at_widget', 'description' => __('Allows you to quickly insert an AvantLink ad campaign into your site.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'avantlink_at_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'avantlink_at_widget', __('AvantLink Ad Campaign', 'framework'), $widget_ops, $control_ops );
	}
	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */

	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$html = $instance['html'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		/* Display Widget */
		echo '<div class="ad_tool">';

		if($html) { echo $html; }

        echo '</div>';

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['html'] = $new_instance['html'];

		/* No need to strip tags for.. */

		return $instance;
	}

	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$affiliate_id = get_option('avantlink_affiliate_id');
		$website_id = get_option('avantlink_website_id');
		$auth_key = get_option('avantlink_auth_key');
		$arrMerchantList = avantlink_api_get_merchants( $affiliate_id, $auth_key );
		$merchant_id_req = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : 0;
		$ad_type_req = isset($_POST['ad_type']) ? $_POST['ad_type'] : '';


		$strForm =	'<p>' .
					'<label for="merchant_id">Merchant:</label>' .
					'<select class="widefit" id="merchant_id" name="merchant_id" />' .
					'<option value="0"></option>';
		foreach ($arrMerchantList as $arrMerchant) {
			$lngMerchantId = $arrMerchant['Merchant_Id'];
			$strMerchantName = $arrMerchant['Merchant_Name'];

			if ($merchant_id_req == $lngMerchantId) {
				$strSelected = ' selected="selected"';
			}
			else { $strSelected = ''; }

			$strForm .=	'<option value="' . $lngMerchantId . '"' . $strSelected . '>' . htmlspecialchars($strMerchantName) . '</option>';
		}
		$strForm .=	'</select>' .
					'<br />';

		$arrAdTypeList = array('text', 'image', 'flash', 'html', 'video', 'DOTD text', 'DOTD html');
		$strForm .=	'<label for="merchant_id">Ad Type:</label>' .
					'<select class="widefit" id="ad_type" name="ad_type" />' .
					'<option value=""></option>';
		foreach ($arrAdTypeList as $strAdType) {
			$strValue =	strtolower(str_replace(' ', '-', $strAdType));

			if ($ad_type_req == $strValue) {
				$strSelected = ' selected="selected"';
			}
			else { $strSelected = ''; }

			$strForm .=	'<option value="' . $strValue . '"' . $strSelected . '>' . $strAdType . '</option>';
		}
		$strForm .=	'</select>';

		$strForm .=	'<input type="submit" value="Search" name="cmdSearch" />' .
					'</p>';

		$strForm .=	'<p>' .
					'<label for="' . $this->get_field_id( 'title' ) . '">Title (optional):</label>' .
					'<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' . htmlspecialchars($instance['title']) . '" />' .
					'</p>';

		if (isset($_POST['cmdSearch'])) {
			$strForm .=	'<p>' .
						'<table>';
			$arrAdCampaigns = avantlink_api_get_ad_campaigns( $affiliate_id, $website_id, $merchant_id_req, $ad_type_req );
			foreach($arrAdCampaigns as $arrCampaign) {
				$strForm .=	'<tr valign="center">' .
							'<td valign="center"><input type="radio" name="' . $this->get_field_name( 'html' ) . '" value ="' . htmlspecialchars($arrCampaign['Ad_Content']) . '" /></td>' .
							'<td>' .
							'<p>' . htmlspecialchars($arrCampaign['Merchant_Name']) . ' : ' . $arrCampaign['Ad_Title'] . '</p>' .
							'<p>' . $arrCampaign['Ad_Content'] . '</p>' .
							'</td>' .
							'</tr>';

			}
			$strForm .=	'</table>' .
						'</p>';
		}

		if ($instance['html']) {
			$strForm .=	'<p>' .
						'Current Ad:<br />' .
						$instance['html'] .
						'</p>';
		}

		echo $strForm;
	}
}
?>
