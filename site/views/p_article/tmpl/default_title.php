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

$params = $this -> item -> params;
?>

<?php if ($params->get('show_title',1)) : ?>
<h2 class="TzArticleTitle" itemprop="name">
    <?php if ($params->get('link_titles',1) AND !empty($this->item->readmore_link)) : ?>
        <?php
        if($params -> get('tz_use_lightbox') == 1):
            $titleLink = $this -> item ->link;
        else:
            $titleLink  = $this->item->readmore_link;
        endif;
        ?>
        <a href="<?php echo $titleLink; ?>" itemprop="url">
            <?php echo $this->escape($this->item->title); ?>
        </a>
    <?php else : ?>
        <?php echo $this->escape($this->item->title); ?>
    <?php endif; ?>
</h2>
<?php endif; ?>

<?php if (!$params->get('show_intro',1)) : ?>
    <?php
    //Call event onContentAfterTitle and TZPluginDisplayTitle on plugin
    echo $this->item->event->afterDisplayTitle;
    echo $this->item->event->TZafterDisplayTitle;
    ?>
<?php endif; ?>