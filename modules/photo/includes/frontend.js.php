<?php if($settings->link_type == 'lightbox') : ?>
jQuery(function() {
	jQuery('.fl-node-<?php echo $id; ?> a').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		closeBtnInside: false
	});
});
<?php endif; ?>