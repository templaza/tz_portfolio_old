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
defined('_JEXEC') or die('Restricted access');
?>

<?php
$list   = $this -> listsArticle;
$params = $this -> params;

$pagination = $this -> pagination -> getData();
?>
<?php if($list):?>
<div id="TzTimline" itemscope itemtype="http://schema.org/Blog">
    <div class="TzTimlineWrap">

        <div class="ss-links" id="ss-links">
            <?php if($this -> pagination -> pagesCurrent > 1): ?>
            <a href="<?php echo $pagination -> start ->link?>">
                <?php echo $pagination -> start -> text;?>
            </a>
            <a href="<?php echo $pagination -> previous ->link?>">
                <?php echo $pagination -> previous -> text;?>
            </a>
            <?php endif;?>

            <?php if($params -> get('class_show_time_links',1)):?>
            <?php echo $this -> loadTemplate('date');?>
            <?php endif;?>

            <?php if($this -> pagination -> pagesCurrent < $this -> pagination -> pagesTotal): ?>
            <a href="<?php echo $pagination -> next ->link?>">
                <?php echo $pagination -> next -> text;?>
            </a>
            <a href="<?php echo $pagination -> end ->link?>">
                <?php echo $pagination -> end -> text;?>
            </a>
            <?php endif;?>
        </div><!-- end main-menu -->
        <div id="ss-container" class="ss-container">
            <?php echo $this -> loadTemplate('item');?>

        </div>
    </div>
</div><!-- end contact -->
<?php endif;?>