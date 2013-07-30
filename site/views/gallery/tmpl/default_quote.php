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

//no direct access
defined('_JEXEC') or die();

$item   = $this -> item;
$media      = $this -> listMedia;
$params     = $this -> mediaParams;

if($params -> get('show_quote_text',1) OR $params -> get('show_quote_author',1)):
    if(count($media)):
        if($media[0] -> type == 'quote'):
?>
    <a href="<?php echo $item ->link;?>" class="ib-content">
        <div class="ib-teaser">
            <?php if($params -> get('show_quote_text',1)):?>
            <h2 class="text"><i class="icon-quote"></i><?php echo $media[0] -> quote_text;?></h2>
            <?php endif;?>

            <?php if($params -> get('show_quote_author',1)):?>
            <span class="author"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_QUOTE_AUTHOR',$media[0] -> quote_author);?></span>
            <?php endif;?>
        </div>

    </a>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>