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
$showread = $params->get('readmore');
$readtext = $params->get('readmoretext');
$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_tz_portfolio_articles_features/css/mod_tz_portfolio_articles_features.css')
?>
<ul class="TzModFeature TzModFeature<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li>
        <?php if(isset($item -> tz_image) AND !empty($item -> tz_image)):?>
            <img src="<?php echo $item -> tz_image;?>"
                 title="<?php echo $item -> tz_image_title?>"
                 alt="<?php echo $item -> tz_image_title?>">
        <?php endif;?>

        <?php if(!empty($item -> title)):?>
            <a class="FeatureTitle" href="<?php echo $item->link; ?>">
                <?php echo $item->title; ?></a>
        <?php endif;?>
        
        <?php
            if(isset($item -> text)):
        ?>
            <p><?php echo $item -> text;?></p>
        <?php endif;?>

        <?php if($showread == 1){ ?>
        <a href="<?php echo $item->link; ?>" class="readmore"><?php echo $readtext; ?> ></a>
        <?php } ?>

	</li>
<?php endforeach; ?>
</ul>
