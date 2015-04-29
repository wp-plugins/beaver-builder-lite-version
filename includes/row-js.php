<?php 

if($settings->bg_type == 'slideshow') : 

	$source = FLBuilderModel::get_row_slideshow_source($row);
	
	if(!empty($source)) :

?>
YUI({'logExclude': { 'yui': true } }).use('fl-slideshow', function(Y) {
	
	if( null === Y.one('.fl-node-<?php echo $id; ?> .fl-bg-slideshow') ) {
		return;
	}

	var oldSlideshow = Y.one('.fl-node-<?php echo $id; ?> .fl-bg-slideshow .fl-slideshow'),
		newSlideshow = new Y.FL.Slideshow({
			autoPlay            : true,
			crop                : true,
			loadingImageEnabled : false,
			randomize           : <?php echo $settings->ss_randomize; ?>,
			responsiveThreshold : 0,
			source              : [{<?php echo $source; ?>}],
			speed               : <?php echo $settings->ss_speed * 1000; ?>,
			stretchy            : true,
			stretchyType        : 'contain',
			transition          : '<?php echo $settings->ss_transition; ?>',
			transitionDuration  : <?php echo $settings->ss_transitionDuration; ?>
		});
	
	if(oldSlideshow) {
		oldSlideshow.remove(true);
	}

	newSlideshow.render('.fl-node-<?php echo $id; ?> .fl-bg-slideshow');
});
<?php 

	endif;

endif; 

?>