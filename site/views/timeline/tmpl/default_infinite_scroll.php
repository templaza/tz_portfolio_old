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

$doc    = &JFactory::getDocument();
?>

<?php if($this -> listsArticle):?>

    <?php
    $params = &$this -> params;
    ?>

    <link rel="stylesheet/less" type="text/css" href="components/com_tz_portfolio/css/tz_lib_style.less">
    <script src="components/com_tz_portfolio/js/less-1.0.21.min.js" type="text/javascript"></script>

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
            jQuery('.tz_item').each(function(){
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
                      <a class="btn btn-small" href="#title" data-option-value="name"><?php echo JText::_('Title');?></a>
                      <a class="btn btn-small" href="#date" data-option-value="date"><?php echo JText::_('Date');?></a>
                  </div>
                </div>
            <?php endif;?>

            <?php if($params -> get('show_layout',1)):?>
                <div class="option-combo">
                    <h2><?php echo JText::_('COM_TZ_PORTFOLIO_LAYOUT');?></h2>
                    <div id="layouts" class="option-set clearfix" data-option-key="layoutMode">
                    <?php
                        if(count($params -> get('layout_type',array('masonry','fitRows','straightDown')))>0):
                            foreach($params -> get('layout_type',array('masonry','fitRows','straightDown')) as $param):
                    ?>
                            <a class="btn btn-small" href="#<?php echo $param?>" data-option-value="<?php echo $param?>">
                                <?php echo $param?>
                            </a>
                        <?php endforeach;?>
                    <?php endif;?>
                    </div>
                </div>
            <?php endif;?>

            <?php if($params -> get('tz_portfolio_layout') == 'default'):?>
                <div class="TzShow">
                  <span class="title"><?php echo strtoupper(JText::_('JSHOW'));?></span>
                    <form name="adminForm" method="post" id="TzShowItems"
                          action="index.php?option=com_tz_portfolio&view=portfolio&Itemid=<?php echo $this -> Itemid?>">
                          <?php echo $this -> pagination -> getLimitBox();?>
                    </form>
                </div>
            <?php endif;?>
        </div>

        <div id="timeline" class="super-list variable-sizes clearfix">
            <?php echo $this -> loadTemplate('item');?>
        </div>

        <div id="tz_append">
            <?php if($params -> get('tz_timeline_layout') == 'ajaxButton'):?>
            <a href="#tz_append" class="btn btn-large btn-block"><?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ITEM_MORE');?></a>
            <?php endif;?>
        </div>

        <div id="loadaj" style="display: none;">
            <a href="<?php echo JURI::root().'index.php?option=com_tz_portfolio&amp;view=timeline&amp;task=timeline.ajax&amp;layout=item&amp;Itemid='.$this -> Itemid.'&amp;page=2'; ?>">
            </a>
        </div>

       

    <script type="text/javascript">
        jQuery(function(){
            var tzpage    =   1;
            <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                function getTags() {
                    var tags    =   [];
                    jQuery('#filter a').each(function (index) {
                       tags.push(jQuery(this).attr('data-option-value').replace(".",""));
                    });
                    return JSON.encode(tags);
                }
            <?php endif;?>

            <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                function getCategories() {
                    var tags    =   [];
                    jQuery('#filter a').each(function (index) {
                       tags.push(jQuery(this).attr('data-option-value').replace(".category",""));
                    });
                    return JSON.encode(tags);
                }
            <?php endif;?>

            var LastDate = jQuery('div.TzDate:last').attr('data-category');

            var $container = jQuery('#timeline');

            $container.imagesLoaded(function(){
                $container.isotope({
                    itemSelector : '.element',
                    getSortData: {
                        name: function( $elem ) {
                            var name = $elem.find('.name'),
                                itemText = name.length ? name : $elem;
                            return itemText.text();
                        },
                        date: function($elem){
                            var number = $elem.hasClass('element') ?
                              $elem.find('.create').text() :
                              $elem.attr('data-date');
                            return number;

                        }
                    }
                });
            });
            <?php if($params -> get('tz_timeline_layout') == 'ajaxInfiScroll'):?>
                jQuery('#tz_append').css({'border':0,'background':'none'});
            <?php endif;?>

            $container.infinitescroll({
                navSelector  : '#loadaj a',    // selector for the paged navigation
                nextSelector : '#loadaj a:first',  // selector for the NEXT link (to page 2)
                itemSelector : '.element',     // selector for all items you'll retrieve
                bufferPx:   200,
                errorCallback: function(){
                    <?php if($params -> get('tz_timeline_layout') == 'ajaxButton'):?>
                        jQuery('#tz_append a').unbind('click').html('<?php echo JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?>').show();
                    <?php endif;?>
                    <?php if($params -> get('tz_timeline_layout') == 'ajaxInfiScroll'):?>
                        jQuery('#tz_append').removeAttr('style').html('<a class="tzNomore"><?php echo JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?></a>');
                    <?php endif;?>
                    jQuery('#tz_append a').addClass('tzNomore');
                },
                loading: {
                    msgText:'<?php echo JText::_('COM_TZ_PORTFOLIO_LOADING_TEXT');?>',
                    finishedMsg: '',
                    img:'<?php echo JURI::root();?>components/com_tz_portfolio/assets/ajax-loader.gif',
                    selector: '#tz_append'
                  }
                },
                // call Isotope as a callback
                function( newElements ) {

                    var $newElems =   jQuery( newElements ).css({ opacity: 0 });

                    // ensure that images load before adding to masonry layout
                    $newElems.imagesLoaded(function(){
                        // show elems now they're ready
                        $newElems.animate({ opacity: 1 });
                        var LastDate2 = null;

                        tz_init(<?php echo $params -> get('tz_column_width',233);?>);

                        // trigger scroll again
                        $container.isotope( 'insert', $newElems);

                        // Delete date haved
                        $newElems.each(function(){
                            var tzClass = jQuery(this).attr('class');
                            if(tzClass.match(/.*?TzDate.*?/i)){
                                var LastDate2 = jQuery(this).attr('data-category');
                                if(LastDate == LastDate2){
                                    jQuery(this).remove();
                                    $container.isotope('reloadItems');
                                }
                                else
                                    LastDate    = LastDate2;
                            }
                        });

                        tzpage++;
                        <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                            jQuery.ajax({
                                url:'index.php?option=com_tz_portfolio&amp;task=timeline.ajaxtags',
                                data:{
                                    'tags':getTags(),
                                    'Itemid':'<?php echo $this -> Itemid;?>',
                                    'page': tzpage
                                }
                            }).success(function(data){
                                if (data.length) {
                                    tztag   = jQuery(data);
                                    jQuery('#filter').append(tztag);
                                    loadTimeline();

                                }
                            });
                        <?php endif;?>

                        <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                            jQuery.ajax({
                                url:'index.php?option=com_tz_portfolio&amp;task=timeline.ajaxcategories',
                                data:{
                                    'catIds':getCategories(),
                                    'Itemid':'<?php echo $this -> Itemid;?>',
                                    'page': tzpage
                                }
                            }).success(function(data){
                                if (data.length) {
                                    tzCategories   = jQuery(data);
                                    jQuery('#filter').append(tzCategories);
                                    loadTimeline();
                                }
                            });
                        <?php endif;?>

                        //if there still more item
                        if($newElems.length){

                            //move item-more to the end
                            jQuery('div#tz_append').find('a:first').show();
                        }
                    });

                }
            );

            <?php if($params -> get('tz_timeline_layout') == 'ajaxButton'):?>
                jQuery(window).unbind('.infscr');

                jQuery('div#tz_append a').click(function(){
                    jQuery(this).stop();
                    jQuery('div#tz_append').find('a:first').hide();
                    $container.infinitescroll('retrieve');
                });

            <?php endif;?>
        });
    </script>

    <?php $layout = $params -> get('layout_type',array('masonry'));?>
    <script type="text/javascript">
         var resizeTimer = null;
        jQuery(window).bind('load resize', function() {
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout("tz_init("+"<?php echo $params -> get('tz_column_width',233);?>)", 100);
        });

        var $container = jQuery('#timeline');
        $container.imagesLoaded( function(){
            $container.isotope({
                itemSelector : '.element',
                layoutMode: '<?php echo $layout[0];?>',
                getSortData: {
                    date: function($elem){
                        var number = $elem.hasClass('element') ?
                          $elem.find('.create').text() :
                          $elem.attr('data-date');
                        return number;

                    },
                    category : function( $elem ) {
                        var number = $elem.hasClass('element') ?
                          $elem.find('.create').text() :
                          $elem.attr('data-category');
                      return number;
                    }
                },
                sortBy: 'category'
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
    </div> <!-- #content -->
<?php endif;?>