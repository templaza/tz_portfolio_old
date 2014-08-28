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

defined('_JEXEC') or die();
$params = $this -> params;
?>
<div id="tz_append" class="text-center">
    <?php if($params -> get('tz_portfolio_layout') == 'ajaxButton'):?>
    <a href="javascript:" class="btn btn-large btn-block"><?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ITEM_MORE');?></a>
    <?php endif;?>
</div>


<div id="loadaj" style="display: none;">
    <a href="<?php echo JURI::root().'index.php?option=com_tz_portfolio&amp;view=portfolio&amp;task=portfolio.ajax'
        .'&amp;layout=item'.(($this -> char)?'&amp;char='.$this -> char:'').'&amp;Itemid='
        .$this -> Itemid.'&amp;page=2'; ?>">
    </a>
</div>

    <script type="text/javascript">

        jQuery(function(){
            var tzpage    = 1;
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


            var $container = jQuery('#portfolio'),
                $scroll = true;
            
            <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'):?>
                jQuery('#tz_append').css({'border':0,'background':'none'});
            <?php endif;?>

            $container.infinitescroll({
                navSelector  : '#loadaj a',    // selector for the paged navigation
                nextSelector : '#loadaj a:first',  // selector for the NEXT link (to page 2)
                itemSelector : '.element',     // selector for all items you'll retrieve
                errorCallback: function(){
                    <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxButton'):?>
                        jQuery('#tz_append a').unbind('click').html('<?php echo JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?>').show();
                    <?php endif;?>
                    <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'):?>
                        jQuery('#tz_append').removeAttr('style').html('<a class="tzNomore"><?php echo JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?></a>');
                    <?php endif;?>
                    jQuery('#tz_append a').addClass('tzNomore');
                    jQuery('#infscr-loading').css('display','none');
                },
                loading: {
                    msgText:'<i class="tz-icon-spinner tz-spin"><\/i><?php echo JText::_('COM_TZ_PORTFOLIO_LOADING_TEXT');?>',
                    finishedMsg: '',
                    img:'<?php echo JURI::root();?>components/com_tz_portfolio/assets/ajax-loader.gif',
                    selector: '#tz_append'
                  }
                },
                // call Isotope as a callback
                function( newElements ) {
                    jQuery('#infscr-loading').css('display','none');

                    var $newElems =   jQuery( newElements ).css({ opacity: 0 }),
                        $bool = true;

                    <?php
                    if($params -> get('comment_function_type','default') == 'js'):
                        // Ajax show comment count.
                        if($params -> get('tz_show_count_comment',1)):
                            if($params -> get('tz_comment_type') == 'facebook' OR
                             $params -> get('tz_comment_type') == 'disqus'):
                    ?>
                    ajaxComments($newElems,<?php echo $this -> Itemid;?>,'<?php echo $this -> commentText;?>');
                    <?php
                            endif;
                        endif;
                    endif;
                    ?>

                    // ensure that images load before adding to masonry layout
                    $newElems.imagesLoaded(function(){

                        // show elems now they're ready
                        $newElems.animate({ opacity: 1 });



                        tz_init('<?php echo $this -> params -> get('tz_column_width',233);?>');

                        // trigger scroll again
                        $container.isotope( 'appended', $newElems);

                        tzpage++;

                        <?php if($params -> get('tz_show_filter',1)):?>
                            <?php if(!$params -> get('show_all_filter',0)):?>
                                <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                                    jQuery.ajax({
                                        url:'index.php?option=com_tz_portfolio&task=portfolio.ajaxtags',
                                        data:{
                                            'tags':getTags(),
                                            'Itemid':'<?php echo $this -> Itemid;?>',
                                            'page': tzpage
                                        }
                                    }).success(function(data){
                                        if (data.length) {
                                            tztag   = jQuery(data);
                                            jQuery('#filter').append(tztag);
                                            loadPortfolio();

                                            <?php if($filter = $params -> get('filter_tags_categories_order',null)):?>
                                            //Sort tags or categories filter
                                            tzSortFilter(jQuery('#filter').find('a'),jQuery('#filter'),'<?php echo $filter?>');
                                            <?php endif;?>
                                        }
                                    });
                                <?php endif;?>

                                <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                                    jQuery.ajax({
                                        url:'index.php?option=com_tz_portfolio&task=portfolio.ajaxcategories',
                                        data:{
                                            'catIds':getCategories(),
                                            'Itemid':'<?php echo $this -> Itemid;?>',
                                            'page': tzpage
                                        }
                                    }).success(function(data){
                                        if (data.length) {
                                            tzCategories   = jQuery(data);
                                            jQuery('#filter').append(tzCategories);
                                            loadPortfolio();

                                            <?php if($filter = $params -> get('filter_tags_categories_order',null)):?>
                                            //Sort tags or categories filter
                                            tzSortFilter(jQuery('#filter').find('a'),jQuery('#filter'),'<?php echo $filter?>');
                                            <?php endif;?>
                                        }
                                    });
                                <?php endif;?>
                            <?php endif;?>

                        <?php endif;?>

                        //if there still more item
                        if($newElems.length){

                            //move item-more to the end
                            jQuery('div#tz_append').find('a:first').show();
                        }
                    });
                    $scroll = true;
                }
            );

            <?php if($params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'):?>
            jQuery(window).scroll(function(){
                jQuery(window).unbind('.infscr');
                if($scroll){
                    if((jQuery(window).scrollTop() + jQuery(window).height()) >= (jQuery(document).height() - 50)){
                        $scroll	= false;
                       $container.infinitescroll('retrieve');
                    }
                }
            });
            <?php endif;?>

            <?php if($params -> get('tz_portfolio_layout') == 'ajaxButton'):?>
                jQuery(window).unbind('.infscr');

                jQuery('div#tz_append a').click(function(){
                    jQuery(this).stop();
                    jQuery('div#tz_append').find('a:first').hide();
                    $container.infinitescroll('retrieve');
                });

            <?php endif;?>
        });

  </script>
