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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$doc    = JFactory::getDocument();
$doc -> addScriptDeclaration('
jQuery(document).ready(function(){
    jQuery("#portfolio").tzPortfolioIsotope({
        "params": '.$this -> params .'
    });
});
');
?>

<?php if($this -> listsArticle):?>

    <?php
    $params = &$this -> params;

    if($params -> get('comment_function_type','default') == 'js'):
        // Ajax show comment count.
        if($params -> get('tz_show_count_comment',1)):
            if($params -> get('tz_comment_type') == 'facebook' OR
                $params -> get('tz_comment_type') == 'disqus'):

                $commentText    = JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');
                $this -> assign('commentText',$commentText);
                $doc -> addScriptDeclaration('
                    jQuery(document).ready(function(){
                        ajaxComments(jQuery(\'#portfolio\').find(\'.element\'),'.$this -> Itemid.',\''.$commentText.'\');
                    });
                ');
            endif;
        endif;
    endif;

    $device = '4;2-768;3-980;5-1280';
    $device = explode(';',$device);
    if(count($device)){
        foreach($device as $i => $value){
            if(strpos($value,'-')){
                $col_w    = explode('-',$value);
                $device[$col_w[1]]  = $col_w[0];
                unset($device[$i]);
            }
        }
    }
    ?>

    <div id="TzContent" class="<?php echo $this->pageclass_sfx;?>">
        <?php if ($params->get('show_page_heading', 1)) : ?>
        <h1 class="page-heading">
            <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
        <?php endif; ?>

        <?php if($params -> get('use_filter_first_letter',1)):?>
        <div class="TzLetters">
            <div class="breadcrumb">
                <?php echo $this -> loadTemplate('letters');?>
            </div>
        </div>
        <?php endif;?>

        <div id="tz_options" class="clearfix">
            <?php if($params -> get('tz_show_filter',1)):?>
            <div class="option-combo">
                <div class="filter-title TzFilter"><?php echo JText::_('COM_TZ_PORTFOLIO_FILTER');?></div>

                <div id="filter" class="option-set clearfix" data-option-key="filter">
                    <a href="#show-all" data-option-value="*" class="btn btn-small selected"><?php echo JText::_('COM_TZ_PORTFOLIO_SHOW_ALL');?></a>
                    <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                        <?php echo $this -> loadTemplate('tags');?>
                    <?php endif;?>
                    <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                        <?php echo $this -> loadTemplate('categories');?>
                    <?php endif;?>
                </div>
            </div>
            <?php endif;?>

            <?php if($params -> get('show_sort',1) AND $sortfields = $params -> get('sort_fields',array('date','hits','title'))):
                $sort   = $params -> get('orderby_sec','rdate');
            ?>
            <div class="option-combo">
                <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_SORT')?></div>

                <div id="sort" class="option-set clearfix" data-option-key="sortBy">
                <?php
                foreach($sortfields as $sortfield):
                    switch($sortfield):
                        case 'title':
                ?>
                    <a class="btn btn-small<?php echo ($sort == 'alpha' || $sort == 'ralpha')?' selected':''?>"
                       href="#title" data-option-value="name"><?php echo JText::_('COM_TZ_PORTFOLIO_TITLE');?></a>
                    <?php
                            break;
                        case 'date':
                    ?>
                    <a class="btn btn-small<?php echo ($sort == 'date' || $sort == 'rdate')?' selected':''?>"
                       href="#date" data-option-value="date"><?php echo JText::_('COM_TZ_PORTFOLIO_DATE');?></a>
                    <?php
                            break;
                        case 'hits':
                    ?>
                    <a class="btn btn-small<?php echo ($sort == 'hits' || $sort == 'rhits')?' selected':''?>"
                       href="#hits" data-option-value="hits"><?php echo JText::_('JGLOBAL_HITS');?></a>
                <?php
                            break;
                    endswitch;
                endforeach;
                ?>
                </div>
            </div>
            <?php endif;?>

            <?php if($params -> get('show_layout',1)):?>
            <div class="option-combo">
                <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_LAYOUT');?></div>
                <div id="layouts" class="option-set clearfix" data-option-key="layoutMode">
                <?php
                    if(count($params -> get('layout_type',array('masonry','fitRows','straightDown')))>0):
                        foreach($params -> get('layout_type',array('masonry','fitRows','straightDown')) as $i => $param):
                ?>
                        <a class="btn btn-small<?php if($i == 0) echo ' selected';?>" href="#<?php echo $param?>" data-option-value="<?php echo $param?>">
                            <?php echo $param?>
                        </a>
                    <?php endforeach;?>
                <?php endif;?>
                </div>
            </div>
            <?php endif;?>

            <?php if($params -> get('tz_portfolio_layout') == 'default'):?>
                <?php if($params -> get('show_limit_box',1)):?>
                    <div class="TzShow">
                      <span class="title"><?php echo strtoupper(JText::_('JSHOW'));?></span>
                        <form name="adminForm" method="post" id="TzShowItems"
                              action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&view=portfolio&Itemid='.$this -> Itemid);?>">
                              <?php echo $this -> pagination -> getLimitBox();?>
                        </form>
                    </div>
                <?php endif;?>
            <?php endif;?>
        </div>

        <div id="portfolio" class="super-list variable-sizes clearfix"
             itemscope itemtype="http://schema.org/Blog">
            <?php echo $this -> loadTemplate('item');?>
        </div>

        <?php if($params -> get('tz_portfolio_layout') == 'default'):?>
            <?php if (($params->def('show_pagination', 1) == 1  || ($params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
                <div class="pagination">
                    <?php  if ($params->def('show_pagination_results', 1)) : ?>
                    <p class="counter">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                    <?php endif; ?>

                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
                <div class="clearfix"></div>
            <?php endif;?>
        <?php endif;?>

        <?php if($params -> get('tz_portfolio_layout') == 'ajaxButton' || $params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'):?>
            <?php echo $this -> loadTemplate('infinite_scroll');?>
        <?php endif;?>
    </div> <!-- #content -->
<?php endif;?>