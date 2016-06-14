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
?>
<?php if (!empty($list)) :?>
	<ul class="archive-module<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) : ?>
	<li>
		<a href="<?php echo $item->link; ?>">
			<?php echo $item->text; ?>
            <?php if($params -> get('show_article_count',1)):?>
                <span class="muted count"><?php echo JText::sprintf('MOD_TZ_PORTFOLIO_ARTICLES_ARCHIVE_ARTICLE_COUNT',$item -> total);?></span>
            <?php endif;?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
