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
    ?>

    <script type="text/javascript">
        function tz_init(defaultwidth){
            var contentWidth    = jQuery('#TzContent').width();
            var columnWidth     = defaultwidth;
            var curColCount     = 0;

            var maxColCount     = 0;
            var newColCount     = 0;
            var newColWidth     = 0;
            var featureColWidth = 0;

            curColCount = Math.floor(contentWidth / columnWidth);

            maxColCount = curColCount + 1;
            if((maxColCount - (contentWidth / columnWidth)) > ((contentWidth / columnWidth) - curColCount)){
                newColCount     = curColCount;
            }
            else{
                newColCount = maxColCount;
            }

            newColWidth = contentWidth;
            featureColWidth = contentWidth;


            if(newColCount > 1){
                newColWidth = Math.floor(contentWidth / newColCount);
                featureColWidth = newColWidth * 2;
            }

            jQuery('.element').width(newColWidth);

            jQuery('.tz_feature_item').width(featureColWidth);
            var $container = jQuery('#portfolio');
            $container.imagesLoaded(function(){
                $container.isotope({
                    masonry:{
                        columnWidth: newColWidth
                    }
                });

            });
        }
    </script>

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

            <?php if($params -> get('show_sort',1) AND $sortfields = $params -> get('sort_fields',array('date','hits','title'))):?>
            <div class="option-combo">
                <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_SORT')?></div>

                <div id="sort" class="option-set clearfix" data-option-key="sortBy">
                <?php
                foreach($sortfields as $sortfield):
                    switch($sortfield):
                        case 'title':
                ?>
                    <a class="btn btn-small" href="#title" data-option-value="name"><?php echo JText::_('COM_TZ_PORTFOLIO_TITLE');?></a>
                    <?php
                            break;
                        case 'date':
                    ?>
                    <a class="btn btn-small selected" href="#date" data-option-value="date"><?php echo JText::_('COM_TZ_PORTFOLIO_DATE');?></a>
                    <?php
                            break;
                        case 'hits':
                    ?>
                    <a class="btn btn-small" href="#hits" data-option-value="hits"><?php echo JText::_('JGLOBAL_HITS');?></a>
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

<?php $layout = $params -> get('layout_type',array('masonry'));?>
<script type="text/javascript">
     var tz_resizeTimer = null;
    jQuery(window).bind('load resize', function() {
        if (tz_resizeTimer) clearTimeout(tz_resizeTimer);
        tz_resizeTimer = setTimeout("tz_init("+"<?php echo $params -> get('tz_column_width',233);?>)", 100);
    });

    var $container = jQuery('#portfolio');
     $container.find('.element').css({opacity: 0});
    $container.imagesLoaded( function(){
        $container.find('.element').css({opacity: 1});
        $container.isotope({
            itemSelector : '.element',
            layoutMode: '<?php echo $layout[0];?>',
            sortBy: 'original-order',
            getSortData: {
                date: function($elem){
                   var number = ($elem.hasClass('element') && $elem.attr('data-date').length) ?
                       $elem.attr('data-date'):$elem.find('.create').text();
                   return parseInt(number);
                }
                <?php
                 if($params -> get('show_sort',1) AND $params -> get('sort_fields',array('date','hits','title'))):
                    if(in_array('hits',$params -> get('sort_fields',array('date','hits','title')))):
                ?>
                ,hits: function($elem){
                   var number = ($elem.hasClass('element') && $elem.attr('data-hits').length) ?
                       $elem.attr('data-hits'):$elem.find('.hits').text();
                   return parseInt(number);
                }
                <?php
                    endif;
                    if(in_array('title',$params -> get('sort_fields',array('date','hits','title')))):
                ?>
                ,name: function( $elem ) {
                   var name = $elem.find('.name'),
                       itemText = name.length ? name : $elem;
                   return itemText.text();
                }
                <?php
                    endif;
                endif;
                ?>
            }
        },function(){
            <?php if($params -> get('tz_show_filter',1) AND $filter = $params -> get('filter_tags_categories_order',null)):?>
                //Sort tags or categories filter
                tzSortFilter(jQuery('#filter').find('a'),jQuery('#filter'),'<?php echo $filter?>');
            <?php endif;?>
        });
        tz_init('<?php echo $params -> get('tz_column_width',233);?>');
    });

    function loadPortfolio(){

        var $optionSets = jQuery('#tz_options .option-set'),
         $optionLinks = $optionSets.find('a');
        var $r_options    = null;
        $optionLinks.click(function(event){
            event.preventDefault();
            var $this = jQuery(this);
            // don't proceed if already selected
            if ( $this.hasClass('selected') ) {
                return false;
            }
            var $optionSet = $this.parents('.option-set');
            $optionSet.find('.selected').removeClass('selected');
            $this.addClass('selected');

            // make option object dynamically, i.e. { filter: '.my-filter-class' }
            var options = {},
                key = $optionSet.attr('data-option-key'),
                value = $this.attr('data-option-value');

            // parse 'false' as false boolean
            value = value === 'false' ? false : value;
            options[ key ] = value;

            if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
                // changes in layout modes need extra logic
                changeLayoutMode( $this, options );
            } else {
                // otherwise, apply new options
                if(value == 'name'){
                    options['sortAscending']    = true;
                }
                else{
                    options['sortAscending']    = false;
                    if( key != 'sortBy'){
                        if($r_options){
                            if($r_options['sortBy'] == 'name'){
                                options['sortAscending']    = true;
                            }
                        }
                    }
                }
                options = jQuery.extend($r_options,options);
                $container.isotope( options );
                $r_options  = options;
            }
            return false;
        });
    }
//    isotopeinit();
    loadPortfolio();



      </script>
      </div> <!-- #content -->
<?php endif;?>