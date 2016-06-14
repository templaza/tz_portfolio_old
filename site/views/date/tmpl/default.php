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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>

<div class="TzBlog blog<?php echo $this->pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
    <div class="TzBlogInner">
        <div class="row-fluid">
            <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
            <?php endif; ?>

            <?php if ($this->params->get('page_subheading')) : ?>
            <h2 class="TzCategoryTitle">
                <?php echo $this->escape($this->params->get('page_subheading')); ?>
            </h2>
            <?php endif; ?>

            <?php if($this->params -> get('use_filter_first_letter',1)):?>
            <div class="TzLetters">
                <div class="breadcrumb">
                    <?php echo $this -> loadTemplate('letters');?>
                </div>
            </div>
            <?php endif;?>

            <?php $date = null;?>
            <?php $leadingcount=0 ; ?>
            <?php if (!empty($this->lead_items)) : ?>
            <div class="TzItemsLeading">
                <?php foreach ($this->lead_items as &$item) : ?>
                    <?php if(isset($item -> date_group) AND !empty($item -> date_group)
                        AND $date != strtotime(date(JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'),strtotime($item -> date_group)))):?>
                        <?php $date = strtotime(date(JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'),strtotime($item -> date_group)));?>
                    <div class="date-group">
                        <div class="clearfix"></div>
                        <h2 class="text-info date"><?php echo JHtml::_('date',$item -> date_group,JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'));?></h2>
                    </div>
                    <?php endif;?>
                    <div class="TzLeading leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
                         itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                        <?php
                            $this->item = &$item;
                            echo $this->loadTemplate('item');
                        ?>
                  <div class="clr"></div>
                    </div>
                    <?php
                        $leadingcount++;
                    ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php
                $introcount=(count($this->intro_items));
                $counter=0;

            ?>
            <?php if (!empty($this->intro_items)) : ?>

                <?php foreach ($this->intro_items as $key => &$item) : ?>

                    <?php if(isset($item -> date_group) AND !empty($item -> date_group)
                        AND $date != strtotime(date(JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'),strtotime($item -> date_group))) ):?>
                    <div class="date-group">
                        <div class="clearfix"></div>
                        <h2 class="text-info date"><?php echo JHtml::_('date',$item -> date_group,JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'));?></h2>
                    </div>
                    <?php endif;?>

                <?php
                    $key= ($key-$leadingcount)+1;
                    $rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
                    $row = $counter / $this->columns ;

                    if ($rowcount==1) : ?>
                    <div class="TzItemsRow cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?>">
                    <?php endif; ?>
                    <div class="span<?php echo round((12 / $this->columns));?>">
                        <div class="TzItem column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
                             itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                            <?php
                                $this->item = &$item;
                                echo $this->loadTemplate('item');
                            ?>
                        <div class="clr"></div>
                        </div>
                    </div>
                    <?php $counter++; ?>

                    <?php if (($rowcount == $this->columns) or ($counter ==$introcount)): ?>
                        <span class="row-separator"></span>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($this->link_items)) : ?>
                <?php echo $this->loadTemplate('links'); ?>
            <?php endif; ?>
            <div class="clearfix"></div>

            <?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
                <div class="pagination">
                    <?php echo $this->pagination->getPagesLinks(); ?>

                    <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
                            <p class="TzCounter">
                                    <?php echo $this->pagination->getPagesCounter(); ?>
                            </p>
                    <?php endif; ?>
                </div>
            <?php  endif; ?>
            <div class="clearfix"></div>

        </div>
    </div>
</div>
