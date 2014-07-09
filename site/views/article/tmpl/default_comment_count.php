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

<?php if($params -> get('tz_show_count_comment',1) == 1):?>
    <span class="TZCommentCount">
                    <?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');?>

        <?php if($params -> get('comment_function_type','js') == 'js'):?>
            <?php if($params -> get('tz_comment_type') == 'disqus'): ?>
                <a href="<?php echo $this -> item ->link;?>#disqus_thread"><?php echo $this -> item -> commentCount;?></a>
            <?php elseif($params -> get('tz_comment_type') == 'facebook'):?>
                <span class="fb-comments-count" data-href="<?php echo $this -> item ->link;?>"></span>
            <?php endif;?>
        <?php else:?>
            <?php if($params -> get('tz_comment_type') == 'facebook'):?>
                <?php if(isset($this -> item -> commentCount)):?>
                    <span><?php echo $this -> item -> commentCount;?></span>
                <?php endif;?>
            <?php endif;?>
                        <?php if($params -> get('tz_comment_type') == 'disqus'):?>
                <?php if(isset($this -> item -> commentCount)):?>
                    <span><?php echo $this -> item -> commentCount;?></span>
                <?php endif;?>
            <?php endif;?>
        <?php endif;?>


        <?php if($params -> get('tz_comment_type') == 'jcomment'): ?>
            <?php
            $comments = JPATH_SITE.'/components/com_jcomments/jcomments.php';
            if (file_exists($comments)){
                require_once($comments);
                if(class_exists('JComments')){
                    ?>
                    <span><?php echo JComments::getCommentsCount((int) $this -> item -> id,'com_tz_portfolio');?></span>
                <?php
                }
            }
            ?>
        <?php endif;?>
    </span>
<?php endif;?>