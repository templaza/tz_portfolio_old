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

$doc    = JFactory::getDocument();

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>

<?php if($this -> listsArticle):?>

<?php
$params = &$this -> params;
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
            jQuery('.tz_item .TzTimeLineMedia').each(function(){
                jQuery(this).find('img').first().attr('width','100%');
            });

            jQuery('.tz_feature_item').width(featureColWidth);
            jQuery('.TzDate').each(function(){
               jQuery(this).width(contentWidth);
            });
            var $container = jQuery('#timeline');
            $container.imagesLoaded(function(){
                $container.isotope({
                    masonry:{
                        columnWidth: newColWidth
                    }
                });
            });
        }
    </script>

    <div id="TzContent">

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
                <h2 class="TzFilter"><?php echo JText::_('COM_TZ_PORTFOLIO_FILTER');?></h2>


                <div id="filter" class="option-set clearfix" data-option-key="filter">

                    <a href="#show-all" data-option-value="*" class="btn btn-small selected">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_SHOW_ALL');?>
                    </a>

                    <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                        <?php echo $this -> loadTemplate('tags');?>
                    <?php endif;?>

                    <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                        <?php echo $this -> loadTemplate('categories');?>
                    <?php endif;?>
                </div>
            </div>
            <?php endif;?>

            <?php if($params -> get('show_sort',1)):?>
                <div class="option-combo">
                  <h2><?php echo JText::_('COM_TZ_PORTFOLIO_SORT')?></h2>
                  <div id="sort" class="option-set clearfix" data-option-key="sortBy">
                      <a class="btn btn-small" href="#title" data-option-value="name"><?php echo JText::_('COM_TZ_PORTFOLIO_TITLE');?></a>
                      <a class="btn btn-small selected" href="#date" data-option-value="date"><?php echo JText::_('COM_TZ_PORTFOLIO_DATE');?></a>
                  </div>
                </div>
            <?php endif;?>

            <?php if($params -> get('show_layout',1)):?>
                <div class="option-combo">
                    <h2><?php echo JText::_('COM_TZ_PORTFOLIO_LAYOUT');?></h2>
                    <div id="layouts" class="option-set clearfix" data-option-key="layoutMode">
                    <?php
                        if(count($params -> get('layout_type',array('masonry','fitRows','straightDown')))>0):
                            foreach($params -> get('layout_type',array('masonry','fitRows','straightDown')) as $i => $param):
                    ?>
                            <a class="btn btn-small<?php if($i == 0) echo ' selected'?>" href="#<?php echo $param?>" data-option-value="<?php echo $param?>">
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
                              action="index.php?option=com_tz_portfolio&view=portfolio&Itemid=<?php echo $this -> Itemid?>">
                              <?php echo $this -> pagination -> getLimitBox();?>
                        </form>
                    </div>
                <?php endif;?>
            <?php endif;?>
        </div>

        <div id="timeline" class="super-list variable-sizes clearfix">
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
             var resizeTimer = null;
            jQuery(window).bind('load resize', function() {
                if (resizeTimer) clearTimeout(resizeTimer);
                resizeTimer = setTimeout("tz_init("+"<?php echo $params -> get('tz_column_width',233);?>)", 100);
            });

            var $container = jQuery('#timeline');
            $container.find('.element').css({opacity: 0});
            $container.imagesLoaded( function(){
                $container.find('.element').css({opacity: 1});
                $container.isotope({
                    itemSelector    : '.element',
                    layoutMode      : '<?php echo $layout[0];?>',
                    sortBy          : 'date',
                    getSortData     : {
                        name: function( $elem ) {
                            var name = $elem.find('.name'),
                                _date   = $elem.attr('data-category'),
                                itemText = name.length ? name : $elem.text();
                            if(_date.length){
                                itemText = _date + itemText;
                            }

                            return itemText;
                        },
                        date: function($elem){
                            var number = $elem.hasClass('element') ?
                              $elem.find('.create').text() :
                              $elem.attr('data-date');
                            return number;

                        }
                    }
                },function(){
    
                   <?php if($filter = $params -> get('filter_tags_categories_order',null)):?>
                        //Sort tags or categories filter
                        tzSortFilter(jQuery('#filter').find('a'),jQuery('#filter'),'<?php echo $filter?>');
                    <?php endif;?>
                });
                tz_init(<?php echo $params -> get('tz_column_width',233);?>);
            });

            function loadTimeline(){
                  var $optionSets = jQuery('#tz_options .option-set'),
                     $optionLinks = $optionSets.find('a');
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
                      changeLayoutMode( $this, options )
                    } else {
                      // otherwise, apply new options
                      $container.isotope( options );
                    }

                    return false;
                  });
            }
            loadTimeline();

          </script>

    </div>
<?php endif;?>