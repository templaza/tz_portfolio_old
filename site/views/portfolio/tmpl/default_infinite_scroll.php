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
<div id="tz_append">
    <?php if($params -> get('tz_portfolio_layout') == 'ajaxButton'):?>
    <a href="#tz_append" class="btn btn-large btn-block"><?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ITEM_MORE');?></a>
    <?php endif;?>
</div>


<div id="loadaj" style="display: none;">
    <a href="<?php echo JURI::root().'index.php?option=com_tz_portfolio&view=portfolio&task=portfolio.ajax&layout=item&Itemid='.$this -> Itemid.'&page=2'; ?>">
    </a>
</div>

  <script type="text/javascript">
        var tz=jQuery.noConflict();
        tz(function(){
            var tzpage    =   1;
            <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                function getTags() {
                    var tags    =   [];
                    tz('#filter a').each(function (index) {
                       tags.push(tz(this).attr('data-option-value').replace(".",""));
                    });
                    return JSON.encode(tags);
                }
            <?php endif;?>

            <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                function getCategories() {
                    var tags    =   [];
                    tz('#filter a').each(function (index) {
                       tags.push(tz(this).attr('data-option-value').replace(".category",""));
                    });
                    return JSON.encode(tags);
                }
            <?php endif;?>


            var $container = tz('#portfolio');

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
            <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'):?>
                tz('#tz_append').css({'border':0,'background':'none'});
            <?php endif;?>

            $container.infinitescroll({
                navSelector  : '#loadaj a',    // selector for the paged navigation
                nextSelector : '#loadaj a:first',  // selector for the NEXT link (to page 2)
                itemSelector : '.element',     // selector for all items you'll retrieve
                errorCallback: function(){
                    <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxButton'):?>
                        tz('#tz_append a').unbind('click').html('<?php echo JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?>').show();
                    <?php endif;?>
                    <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'):?>
                        tz('#tz_append').removeAttr('style').html('<a class="tzNomore"><?php echo JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?></a>');
                    <?php endif;?>
                    tz('#tz_append a').addClass('tzNomore');
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

                    var $newElems =   tz( newElements ).css({ opacity: 0 });

                    // ensure that images load before adding to masonry layout
                    $newElems.imagesLoaded(function(){
                        // show elems now they're ready
                        $newElems.animate({ opacity: 1 });

                        tz_init('<?php echo $this -> params -> get('tz_column_width',233);?>');

                        // trigger scroll again
                        $container.isotope( 'appended', $newElems);

                        tzpage++;
                        <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                            tz.ajax({
                                url:'index.php?option=com_tz_portfolio&task=portfolio.ajaxtags',
                                data:{
                                    'tags':getTags(),
                                    'Itemid':'<?php echo $this -> Itemid;?>',
                                    'page': tzpage
                                }
                            }).success(function(data){
                                if (data.length) {
                                    tztag   = tz(data);
                                    tz('#filter').append(tztag);
                                    loadPortfolio();

                                }
                            });
                        <?php endif;?>

                        <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                            tz.ajax({
                                url:'index.php?option=com_tz_portfolio&task=portfolio.ajaxcategories',
                                data:{
                                    'catIds':getCategories(),
                                    'Itemid':'<?php echo $this -> Itemid;?>',
                                    'page': tzpage
                                }
                            }).success(function(data){
                                if (data.length) {
                                    tzCategories   = tz(data);
                                    tz('#filter').append(tzCategories);
                                    loadPortfolio();
                                }
                            });
                        <?php endif;?>

                        //if there still more item
                        if($newElems.length){

                            //move item-more to the end
                            tz('div#tz_append').find('a:first').show();
                        }
                    });

                }
            );

            <?php if($this -> params -> get('tz_portfolio_layout') == 'ajaxButton'):?>
                tz(window).unbind('.infscr');

                tz('div#tz_append a').click(function(){
                    tz(this).stop();
                    tz('div#tz_append').find('a:first').hide();
                    $container.infinitescroll('retrieve');
                });

            <?php endif;?>
        });


  </script>