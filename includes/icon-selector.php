<?php

$icons = json_decode(file_get_contents(FL_BUILDER_DIR . 'json/font-awesome.json'));
$categories = array();

foreach($icons->icons as $icon) {
    
    $name = $icon->categories[0];
    $id   = strtolower(str_replace(' ', '-', $name));
    
    if(!isset($categories[$id])) {
        $categories[$id] = new stdClass();
        $categories[$id]->id = $id;
        $categories[$id]->name = $name;
        $categories[$id]->icons = array();
    }
    
    $categories[$id]->icons[] = $icon->id;
}

?>
<div class="fl-lightbox-header">
    <h1><?php _e('Select Icon', 'fl-builder'); ?></h1>
    <div class="fl-icons-filter">
        <?php _e('Filter: ', 'fl-builder'); ?>
        <select>
            <option value="all">All</option>
            <?php foreach($categories as $cat) : ?>
            <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="fl-icons-list">
    <?php foreach($categories as $cat) : ?>
    <div class="fl-icons-section fl-<?php echo $cat->id; ?>">
    
		<h2><?php echo $cat->name; ?></h2>
		
        <?php foreach($cat->icons as $icon) : ?>
        <i class="fa fa-<?php echo $icon; ?>"></i>
        <?php endforeach; ?>
        
    </div>
    <?php endforeach; ?>
</div>
<div class="fl-lightbox-footer fl-icon-selector-footer">
    <a class="fl-icon-selector-cancel fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;"><?php _e('Cancel', 'fl-builder'); ?></a>
</div>