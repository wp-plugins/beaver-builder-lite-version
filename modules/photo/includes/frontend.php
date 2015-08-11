<?php

$photo  = $module->get_data();
$src    = $module->get_src();
$link   = $module->get_link();
$alt    = $module->get_alt();
$attrs  = $module->get_attributes();

?>
<div class="fl-photo<?php if ( ! empty( $settings->crop ) ) echo ' fl-photo-crop-' . $settings->crop ; ?> fl-photo-align-<?php echo $settings->align; ?>" itemscope itemtype="http://schema.org/ImageObject">
	<div class="fl-photo-content">
		<?php if(!empty($link)) : ?>
		<a href="<?php echo $link; ?>" target="<?php echo $settings->link_target; ?>" itemprop="url">
		<?php endif; ?>
		<img class="fl-photo-img" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" itemprop="image" <?php echo $attrs; ?> />
		<?php if(!empty($link)) : ?>
		</a>
		<?php endif; ?>    
		<?php if($photo && !empty($photo->caption) && 'hover' == $settings->show_caption) : ?>
		<div class="fl-photo-caption fl-photo-caption-hover" itemprop="caption"><?php echo $photo->caption; ?></div>
		<?php endif; ?>
	</div>
	<?php if($photo && !empty($photo->caption) && 'below' == $settings->show_caption) : ?>
	<div class="fl-photo-caption fl-photo-caption-below" itemprop="caption"><?php echo $photo->caption; ?></div>
	<?php endif; ?>
</div>