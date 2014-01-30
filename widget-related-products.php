<?php

//Add function to widgets_init that'll load the widget.
add_action( 'widgets_init', 'avantlink_rp_widgets' );


//Register widget.
function avantlink_rp_widgets() {
	register_widget( 'AvantLink_RP_Widget' );
}

/*
 * Widget class.
 */
class AvantLink_RP_Widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */

	function AvantLink_RP_Widget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'avantlink_rp_widget', 'description' => __('Display related products from your AvantLink merchants on your site.  Driven by keyword searches that can be customized on a per-post basis, or specified at the site level.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'avantlink_rp_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'avantlink_rp_widget', __('AvantLink Related Products', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */

	function widget( $args, $instance ) {
		extract( $args );

		global $post;
		$id = $post->ID;

		// only display on single pages
		if (is_single()|is_page()) {

			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) { echo $before_title . $title . $after_title; }

			echo '<div class="avantlink_related_products_container">';

			if(function_exists('avantlink_display_related_products')) { avantlink_display_related_products(); }

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
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults );

		$strForm = '';
		$strForm .= '<p><small><em>This widget serves as a placeholder for where to show your Related Products results.</em></small></p>';
		echo $strForm;
	}
}
?>