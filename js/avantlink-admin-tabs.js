jQuery(function(){

	// Tabs
	jQuery("#tabs").tabs();

	//hover states on the static widgets
	jQuery("#dialog_link, ul#icons li").hover(
		function() { $(this).addClass("ui-state-hover"); },
		function() { $(this).removeClass("ui-state-hover"); }
	);

});