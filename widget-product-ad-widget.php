<?php

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'avantlink_paw_widgets' );

/*
 * Register widget.
 */
function avantlink_paw_widgets() {
	register_widget( 'AvantLink_PAW_Widget' );
}

/*
 * Widget class.
 */
class AvantLink_PAW_Widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */

	function AvantLink_PAW_Widget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'avantlink_paw_widget', 'description' => __('Integrate AvantLink Product Ad Widget (PAW) tools into your site.  You can choose which PAW subscription to display for each post or use an overall default for your entire site. ', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'avantlink_paw_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'avantlink_paw_widget', __('AvantLink Product Ad Widget', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */

	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$id = $post->ID;

		$paw_id = $instance['paw_id'];
		$paw_url = $instance['paw_url'];

		if ($paw_url == '') {
			// Looks like there's no particular PAW that this instance
			// of the widget is configured to display

			// See if we're on a post that has a specific PAW defined
			if (is_single()|is_page()) {
				$paw_url = trim(get_post_meta($id, '_avantlink_paw_url', true));
			}
		}

		if ($paw_url != '') {
			// Display this now

			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) { echo $before_title . $title . $after_title; }

			echo '<div class="avl_ad_widget">';
			echo '<script type="text/javascript" src="' . htmlspecialchars($paw_url) . '"></script>';
			echo '</div>';

			/* After widget (defined by themes). */
			echo $after_widget;
		}
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$paw_id = intval($new_instance['paw_id']);
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

		$instance['paw_id'] = $paw_id;
		$instance['paw_url'] = $paw_url;

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
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults );

		$paw_id = $instance['paw_id'];

		$strForm = '';

		$strForm .=	'<p>' .
					'<label for="' . $this->get_field_id('paw_id') . '">PAW Subscription:</label>' .
					'<select size="1" id="' . $this->get_field_id('paw_id') . '" name="' . $this->get_field_name('paw_id') . '">' .
					'<option value="0"></option>';

	    $affiliate_id = get_option('avantlink_affiliate_id');
		$auth_key = get_option('avantlink_auth_key');
		$arrSubscriptionList = avantlink_api_get_subscriptions( $affiliate_id, $auth_key, 'paw' );
		foreach ($arrSubscriptionList as $arrSubscription) {
			$intSubscriptionId = $arrSubscription['Subscription_Id'];
			$strSubscriptionName = $arrSubscription['Subscription_Name'];

			if ($paw_id == $intSubscriptionId) {
				$strSelected = ' selected="selected"';
			}
			else { $strSelected = ''; }

			$strForm .= '<option value="'. $intSubscriptionId . '"' . $strSelected . '>' . htmlspecialchars($intSubscriptionId . ' - ' . $strSubscriptionName) . '</option>';
		}

		$strForm .=	'</select>' .
					'</p>';

		$strForm .= '<p><small><em>If no Product Ad Widget (PAW) Subscription is selected here, then only those posts that specify a particular PAW Subscription themselves will display the Product Ad Widget.</em></small></p>';

		$strForm .= '<p><small>Review/manage your Product Ad Widget Subscriptions <a target="_blank" href="https://www.avantlink.com/affiliate/product_ad_widget.php">here</a>.</small></p>';

		echo $strForm;
	}
}
?>