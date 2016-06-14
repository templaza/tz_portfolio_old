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
defined('_JEXEC') or die('Restricted access');

$lists  = $this -> listsUsers;
$params = &$this -> params;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');
?>

<?php if($lists):?>

    <div class="TzUser <?php echo $this -> pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
        <div class="TzUserInner">
            <?php if ($params->get('show_page_heading', 1)) : ?>
            <h1 class="page-heading">
                <?php echo $this->escape($params->get('page_heading')); ?>
            </h1>
            <?php endif; ?>

            <?php echo  $this -> loadTemplate('author');?>

            <?php if($params -> get('use_filter_first_letter',1)):?>
                <div class="TzLetters">
                    <div class="breadcrumb">
                        <?php echo $this -> loadTemplate('letters');?>
                    </div>
                </div>
            <?php endif;?>

            <?php if($params -> get('show_limit_box',1)):?>
            <form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.JRequest::getCmd('created_by').'&amp;Itemid='.JRequest::getInt('Itemid'));?>"
                  id="adminForm"
                  name="adminForm"
                  method="post">
                <div class="display-limit">
                    <fieldset class="filters">
                        <?php echo  JText::_('JGLOBAL_DISPLAY_NUM');?>
                        <?php echo $this -> pagination -> getLimitBox();?>
                    </fieldset>
                </div>
            </form>
            <?php endif;?>

            <?php foreach($lists as $i => &$row):
                $this -> item   = &$row;
            ?>
            <div class="<?php if($i == 0): echo 'TzItemsLeading'; else: echo 'TzItemsRow row-0'; endif;?>">
                <div class="TzLeading leading-0" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                <?php echo $this -> loadTemplate('item');?>
                </div>
            </div>
            <?php endforeach;?>

            <?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
            <div class="pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>

               <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="TzCounter">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif;?>
        </div>
    </div>
<?php endif;?>

