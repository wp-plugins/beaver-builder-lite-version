<div class="fl-col-group fl-node-<?php echo $group->node; ?>" data-node="<?php echo $group->node; ?>">
    <?php foreach($cols as $col) : ?>
    <div class="<?php echo FLBuilder::render_column_class($col); ?>" style="width: <?php echo $col->settings->size; ?>%;" data-node="<?php echo $col->node; ?>">
        <div class="fl-col-content fl-node-content">
        <?php FLBuilder::render_modules($col->node); ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>