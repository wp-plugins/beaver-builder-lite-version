<?php

$photo  = $module->get_data();
$src    = $module->get_src();
$link   = $module->get_link();
$alt    = $module->get_alt();

?>
<div class="fl-photo fl-photo-crop-<?php echo $settings->crop; ?> fl-photo-align-<?php echo $settings->align; ?>" itemscope itemtype="http://schema.org/ImageObject">
    <div class="fl-photo-content">
        <?php if(!empty($link)) : ?>
        <a href="<?php echo $link; ?>" target="<?php echo $settings->link_target; ?>" itemprop="url">
        <?php endif; ?>
        <img class="fl-photo-img" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" itemprop="image" />
        <?php if(!empty($link)) : ?>
        </a>
        <?php endif; ?>    
    </div>
    <?php if($photo && $settings->show_caption && !empty($photo->caption)) : ?>
    <div class="fl-photo-caption fl-photo-caption-<?php echo $settings->show_caption; ?>" itemprop="caption"><?php echo $photo->caption; ?></div>
    <?php endif; ?>
</div>