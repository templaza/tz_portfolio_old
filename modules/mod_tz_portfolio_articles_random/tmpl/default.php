<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/
 
// no direct access
defined('_JEXEC') or die;
//var_dump($list);
?>
<?php if (!empty($list)) :?>
	<ul class="random-module<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) : ?>

	<li>
        <?php if(isset($item -> tz_image) AND $item -> tz_image):?>
            <a href="<?php echo $item->link; ?>">
                <img src="<?php echo $item -> tz_image?>" alt="<?php echo $item -> tz_imagetitle?>">
            </a>
        <?php endif;?>
		<a class="title" href="<?php echo $item->link; ?>">
			<?php echo $item->title; ?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>