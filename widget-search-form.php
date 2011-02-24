<?php

//Add function to widgets_init that'll load the widget.
add_action( 'widgets_init', 'avantlink_psf_widgets' );


//Register widget.
function avantlink_psf_widgets() {
	register_widget( 'AvantLink_PSF_Widget' );
}

/*
 * Widget class.
 */
class AvantLink_PSF_Widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */

	function AvantLink_PSF_Widget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'avantlink_psf_widget', 'description' => __('Quickly enable a product search form on your site, using data from your AvantLink merchants.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'avantlink_psf_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'avantlink_psf_widget', __('AvantLink Product Search Form', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */

	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['ps_title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		/* Display Widget */

		echo '<div class="avantlink_psr_form">';

		$search_url = get_option('avantlink_search_url');
		$strSearchUrlParamHtml = '';

		$arrParsedUrl = parse_url($search_url);
		$strParams = $arrParsedUrl['query'];
		if ($strParams != '') {
			$arrParams = array();
			parse_str($strParams, $arrParams);
			foreach ($arrParams as $strParamName => $strParamValue) {
				$strSearchUrlParamHtml .= '<input type="hidden" name="' . htmlspecialchars($strParamName) . '" value="' . htmlspecialchars($strParamValue) . '" />';
			}
		}

		echo '<form role="search" method="get" action="'.$search_url.'">' . $strSearchUrlParamHtml . '<div><label class="screen-reader-text" for="ps">Search for:</label><input type="text" value="" name="ps" id="ps" class="psr_input" /> <input type="submit" id="searchsubmit" value="Search" class="psr_submit" /></div></form>';

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
		$instance['ps_title'] = strip_tags( $new_instance['ps_title'] );

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
		'ps_title' => 'Product Search',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$strForm =	'<p>' .
					'<label for="' . $this->get_field_id( 'ps_title' ) . '">Title:</label>' .
					'<input class="widefat" id="' . $this->get_field_id( 'ps_title' ) . '" name="' . $this->get_field_name( 'ps_title' ) . '" value="' . htmlspecialchars($instance['ps_title']) . '" />' .
					'</p>';
		echo $strForm;
	}
}
?>
