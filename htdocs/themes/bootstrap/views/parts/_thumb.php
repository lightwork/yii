<?php 
	$id = 'item_thumb_' . $index;
	$url = (isset($data) && !empty($data)) ? $data : 'http://placehold.it/280x180';
	$placeholder = (isset($data) && !empty($data)) ? '' : 'data-placeholder="true"';
	$class = 'thumbnail';
	if(isset($data) && ($data instanceof ItemImage) && $data->type == ItemImage::TYPE_PRIMARY) { $class .= ' primary'; }
?>
<li class="span3">
	<a href="#" id="<?= $id; ?>" class="<?= $class; ?>" data-title="Tooltip" <?= $placeholder; ?> >
		<?php if(isset($data) && ($data instanceof ItemImage)) : ?>
		<img src="<?= $this->createUrl('image/load', array('image_id'=>$data->id, 'image_size'=>'bootthumb')); ?>" alt="">
		<?php else: ?>
		<input type="hidden" name="ItemImage[]" value="" />
		<img src="<?= $url; ?>" alt="">
		<?php endif; ?>
	</a>
</li>