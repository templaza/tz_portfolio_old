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

$list   = $this -> lists;
$params = $this -> params;
$doc    = JFactory::getDocument();
$doc -> addCustomTag('
    <script id="previewTmpl" type="text/x-jquery-tmpl">
        <div id="ib-img-preview" class="ib-preview">
            <img src="${src}" alt="" class="ib-preview-img"/>
            <span class="ib-preview-descr" style="display:none;">${description}</span>
            <div class="ib-nav" style="display:none;">
                <span class="ib-nav-prev">Previous</span>
                <span class="ib-nav-next">Next</span>
            </div>
            <span class="ib-close" style="display:none;">Close Preview</span>
            <div class="ib-loading-large" style="display:none;">Loading...</div>
        </div>
    </script>

');
$doc -> addCustomTag('
    <script id="contentTmpl" type="text/x-jquery-tmpl">
        <div id="ib-content-preview" class="ib-content-preview">
            <div class="ib-teaser" style="display:none;">{{html teaser}}</div>
            <div class="ib-content-full" style="display:none;">{{html content}}</div>
            <span class="ib-close" style="display:none;">Close Preview</span>
        </div>
    </script>
');

$width  = null;
$height = null;
if($width  = strtolower($params -> get('tz_gallery_item_width','210px'))){
    if(preg_match('/^\dpx$/',$width) != 0 && !preg_match('/^\d%$/',$width) != 0){
        $width  += 'px';
    }
}
if($height  = strtolower($params -> get('tz_gallery_item_height','210px'))){
    if(preg_match('/^\dpx$/',$height) != 0){
        $height  += 'px';
    }
}

$columnSize = $params -> get('image_crop_type','w_h_input');
switch($columnSize):
    case 'h_min':
        $doc -> addStyleDeclaration('.ib-main a{
            width: auto;
            height: auto;
        }
        .ib-main a.ib-content{
            width: '.$width.';
        }
        ');
        $doc -> addScriptDeclaration('jQuery(window).load(function(){
            var totalWidth = 0;
            var totalHeight = 0;
            var minHeight   = jQuery(\'.ib-main .ib-image img:first\').height();
            jQuery(\'.ib-main .ib-image img\').each(function(){
                if(minHeight > jQuery(this).height()){
                    minHeight = jQuery(this).height();
                }
            });

            if(!minHeight){
                minHeight   = "'.$height.'";
                minHeight   = minHeight.replace(\'px\',\'\');
            }
            if(jQuery(\'.ib-main .ib-image\').length)
                jQuery(\'.ib-main .ib-image\').height(minHeight);

            if(minHeight && jQuery(\'.ib-main .ib-content\').length)
                jQuery(\'.ib-main .ib-content\').height(minHeight);

            jQuery(\'.ib-main a\').each(function(index){
                if(index < '.$params -> get('column_count',10).'){
                    totalWidth   += jQuery(this).outerWidth(true);
                }
            })
            if(totalWidth >0)
                jQuery(\'.ib-main\').width(totalWidth);

            totalHeight = '.$params -> get('row_count',10).' * minHeight;
            if('.$params -> get('use_row_count',0).' && totalHeight '
                                     .'&& (jQuery(\'.ib-main a\').length * minHeight) > totalHeight){
                jQuery(\'#ib-main-wrapper\').height(totalHeight);
            }
        })');
        break;
    case 'h_input':
        $doc -> addStyleDeclaration('.ib-main a{
                width: auto;
                height: '.$height.';
            }
            .ib-main a.ib-content{
                width: '.$width.';
            }
        ');
        $doc -> addScriptDeclaration('jQuery(window).load(function(){
            var totalWidth = 0;
            var totalHeight = 0;
            var minHeight   = \''.$height.'\';
            minHeight   = minHeight.replace(\'px\',\'\');

            jQuery(\'.ib-main a\').each(function(index){
                if(index < '.$params -> get('column_count',10).'){
                    totalWidth   += jQuery(this).outerWidth(true);
                }
            })
            if(totalWidth >0)
                jQuery(\'.ib-main\').width(totalWidth);

            totalHeight = '.$params -> get('row_count',10).' * minHeight;
            if('.$params -> get('use_row_count',0).' && totalHeight '
                                     .'&& (jQuery(\'.ib-main a\').length * minHeight) > totalHeight){
                jQuery(\'#ib-main-wrapper\').height(totalHeight);
            }
        })');
        break;
    case 'w_h_input':
        $doc -> addStyleDeclaration('.ib-main a{
                width: '.$width.';
                height: '.$height.';
            }
        ');
        $doc -> addScriptDeclaration('jQuery(window).load(function(){
            var totalWidth = 0;
            var totalHeight = 0;
            var minHeight   = \''.$height.'\';
            minHeight   = minHeight.replace(\'px\',\'\');

            jQuery(\'.ib-main a\').each(function(index){
                if(index < '.$params -> get('column_count',10).'){
                    totalWidth   += jQuery(this).outerWidth(true);
                }
            })
            if(totalWidth >0)
                jQuery(\'.ib-main\').width(totalWidth);

            totalHeight = '.$params -> get('row_count',10).' * minHeight;
            if('.$params -> get('use_row_count',0).' && totalHeight '
                                     .'&& (jQuery(\'.ib-main a\').length * minHeight) > totalHeight){
                jQuery(\'#ib-main-wrapper\').height(totalHeight);
            }
        })');
        break;
endswitch;
?>

<?php if($list):?>
    <div id="PortfolioGallery">
        <div id="ib-main-wrapper" class="ib-main-wrapper">
            <div class="ib-main">
                <?php foreach($list as $row):?>
                    <?php
                        $itemParams     = $params;
                        $itemParams -> merge(new JRegistry($row -> attribs));
                        $mediaParams    = null;
                        $media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
                        $mediaParams    = $params;

                        $mediaParams -> merge(new JRegistry($row -> attribs));

                        $media -> setParams($mediaParams);
                        $listMedia      = $media -> getMedia($row -> id);
                        $row ->link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row -> slug, $row -> catid));

                        $this -> assign('listMedia',$listMedia);
                        $this -> assign('mediaParams',$mediaParams);
                        $this -> assign('item',$row);

                        $html   = null;
                        if($listMedia):
                            $html   = trim($this -> loadTemplate('media'));
                        endif;

                        if($html AND !empty($html)):
                            echo $html;
                        else:
                    ?>
                        <?php if($params -> get('show_article_not_image',1)):?>
                            <a href="<?php echo $row ->link;?>" class="ib-content">
                                <div class="ib-teaser">
                                    <?php if($itemParams -> get('show_title',1) == 1):?>
                                    <h2><?php echo $row -> title;?></h2>
                                    <?php endif;?>

                                    <?php if(($itemParams->get('show_author',1))
                                             OR ($itemParams->get('show_category',1))
                                             OR ($itemParams->get('show_create_date',1))
                                             OR ($itemParams->get('show_modify_date',1))
                                             OR ($itemParams->get('show_publish_date',1))
                                            OR ($itemParams->get('show_hits',1))):?>

                                        <?php if($itemParams -> get('show_author',1)):?>
                                            <span class="TzAuthor">
                                                <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $row -> author); ?>
                                            </span>
                                        <?php endif;?>

                                        <?php if($itemParams -> get('show_category',1)):?>
                                            <span class="TzCategory">
                                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $row -> category_title); ?>
                                            </span>
                                        <?php endif;?>

                                        <?php if($itemParams -> get('show_create_date',1)):?>
                                            <span class="TzCreate">
                                                <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON',
                                                      JHtml::_('date', $row -> created, JText::_('DATE_FORMAT_LC2'))); ?>
                                            </span>
                                        <?php endif;?>

                                        <?php if($itemParams -> get('show_modify_date',1)):?>
                                            <span class="TzModified">
                                                <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED',
                                                      JHtml::_('date', $row -> modified, JText::_('DATE_FORMAT_LC2'))); ?>
                                            </span>
                                        <?php endif;?>

                                        <?php if($itemParams -> get('show_publish_date',1)):?>
                                            <span class="TZPublished">
                                            <?php echo JText::sprintf( JHtml::_('date', $row -> publish_up,
                                                                                JText::_('DATE_FORMAT_LC2'))); ?>
                                            </span>
                                        <?php endif;?>

                                        <?php if($itemParams -> get('show_hits',1)):?>
                                            <span class="TzHits">
                                                <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $row->hits); ?>
                                            </span>
                                        <?php endif;?>
                                    <?php endif;?>

                                </div>
                                <div class="ib-content-full">
                                    <?php if($itemParams -> get('show_intro',1) == 1):?>
                                    <div class="TzIntrotext"><?php echo strip_tags($row -> introtext); ?></div>
                                    <?php endif;?>
                                    <div class="TzFulltext"><?php echo strip_tags($row -> fulltext);?></div>
                                </div>
                            </a>
                        <?php endif;?>
                    <?php endif;?>
                <?php endforeach;?>
                <div class="clr"></div>
            </div><!-- ib-main -->
        </div><!-- ib-main-wrapper -->
    </div>

    <script type="text/javascript">
        jQuery(function() {
            
            var $ibWrapper	= jQuery('#ib-main-wrapper'),

                Template	= (function() {

                        // true if dragging the container
                    var kinetic_moving				= false,
                        // current index of the opened item
                        current						= -1,
                        // true if the item is being opened / closed
                        isAnimating					= false,
                        // items on the grid
                        $ibItems					= $ibWrapper.find('div.ib-main > a'),
                        // image items on the grid
                        $ibImgItems					= $ibItems.not('.ib-content'),
                        // total image items on the grid
                        imgItemsCount				= $ibImgItems.length,
                        init						= function() {

                            // add a class ib-image to the image items
                            $ibImgItems.addClass('ib-image');
                            // apply the kinetic plugin to the wrapper
                            loadKinetic();
                            // load some events
                            initEvents();

                        },
                        loadKinetic					= function() {

                            if(typeof setWrapperSize == 'function'){
                                setWrapperSize();
                            }

                            $ibWrapper.kinetic({
                                moved	: function() {

                                    kinetic_moving = true;

                                },
                                stopped	: function() {

                                    kinetic_moving = false;

                                }
                            });

                        }/*,
                        setWrapperSize				= function() {

                            var containerMargins	= jQuery('#ib-top').outerHeight(true) +
                                    jQuery('#header').outerHeight(true) + parseFloat( $ibItems.css('margin-top') );
                            if(containerMargins == 0){
                                containerMargins    = 'auto';
                            }
                            $ibWrapper.css( 'height', containerMargins);

                        }*/,
                        initEvents					= function() {

                            // open the item only if not dragging the container
                            $ibItems.bind('click.ibTemplate', function( event ) {

                                if( !kinetic_moving )
                                    openItem( jQuery(this) );

                                return false;

                            });

                            // on window resize, set the wrapper and preview size accordingly
                            jQuery(window).bind('resize.ibTemplate', function( event ) {

                                if(typeof setWrapperSize == 'function'){
                                    setWrapperSize();
                                }

                                jQuery('#ib-img-preview, #ib-content-preview').css({
                                    width	: jQuery(window).width(),
                                    height	: jQuery(window).height()
                                })

                            });

                        },
                        openItem					= function( $row ) {

                            if( isAnimating ) return false;

                            // if content item
                            if( $row.hasClass('ib-content') ) {

                                isAnimating	= true;
                                current	= $row.index('.ib-content');
                                loadContentItem( $row, function() { isAnimating = false; } );

                            }
                            // if image item
                            else {

                                isAnimating	= true;
                                current	= $row.index('.ib-image');
                                loadImgPreview( $row, function() { isAnimating = false; } );

                            }

                        },
                        // opens one image item (fullscreen)
                        loadImgPreview				= function( $row, callback ) {

                            var largeSrc		= $row.children('img').data('largesrc'),
                                description		= $row.children('span').text(),
                                largeImageData	= {
                                    src			: largeSrc,
                                    description	: description
                                };

                            // preload large image
                            $row.addClass('ib-loading');

                            preloadImage( largeSrc, function() {

                                $row.removeClass('ib-loading');

                                var hasImgPreview	= ( jQuery('#ib-img-preview').length > 0 );

                                if( !hasImgPreview ){
    //                                jQuery('#previewTmpl').tmpl( largeImageData ).insertAfter( $ibWrapper );

                                    jQuery('body').append(jQuery('#previewTmpl').tmpl( largeImageData ));


                                }
                                jQuery('#ib-img-preview').children('img.ib-preview-img')
                                                    .attr( 'src', largeSrc )
                                                    .end()
                                                    .find('span.ib-preview-descr')
                                                    .text( description );

                                //Disabled scrollbar
                                jQuery('body').css('overflow','hidden');
                                
                                jQuery('#ib-top').insertBefore(jQuery('#ib-img-preview')).css({position:'fixed',top:0});

                                //get dimentions for the image, based on the windows size
                                var	dim	= getImageDim( largeSrc );

                                $row.removeClass('ib-img-loading');

                                //set the returned values and show/animate preview
                                jQuery('#ib-img-preview').css({
                                    width	: $row.width(),
                                    height	: $row.height(),
                                    left	: $row.offset().left,
                                    top		: $row.offset().top - jQuery(window).scrollTop()
                                }).children('img.ib-preview-img').hide().css({
                                    width	: dim.width,
                                    height	: dim.height,
                                    left	: dim.left,
                                    top		: dim.top
                                }).fadeIn( 400 ).end().show().animate({
                                    width	: jQuery(window).width(),
                                    left	: 0
                                }, 500, 'easeOutExpo', function() {

                                    jQuery(this).animate({
                                        height	: jQuery(window).height(),
                                        top		: 0
                                    }, 400, function() {

                                        var $this	= jQuery(this);
                                        $this.find('span.ib-preview-descr, span.ib-close').show()
                                        if( imgItemsCount > 1 )
                                            $this.find('div.ib-nav').show();

                                        if( callback ) callback.call();

                                    });

                                });

                                if( !hasImgPreview )
                                    initImgPreviewEvents();

                            } );

                        },
                        // opens one content item (fullscreen)
                        loadContentItem				= function( $row, callback ) {

                            var hasContentPreview	= ( jQuery('#ib-content-preview').length > 0 ),
                                teaser				= $row.children('div.ib-teaser').html(),
                                content				= $row.children('div.ib-content-full').html(),
                                contentData			= {
                                    teaser		: teaser,
                                    content		: content
                                };

                            if( !hasContentPreview ){
    //                            jQuery('#contentTmpl').tmpl( contentData ).insertAfter( $ibWrapper );
                                jQuery('body').append(jQuery('#contentTmpl').tmpl( contentData ));
                            }

                            //Disabled scrollbar
                            jQuery('body').css('overflow','hidden');
                            
                            jQuery('#ib-top').insertBefore(jQuery('#ib-content-preview')).css({position:'fixed',top:0});

                            //set the returned values and show/animate preview
                            jQuery('#ib-content-preview').css({
                                width	: $row.width(),
                                height	: $row.height(),
                                left	: $row.offset().left,
                                top		: $row.offset().top - jQuery(window).scrollTop()
                            }).show().animate({
                                width	: jQuery(window).width(),
                                left	: 0
                            }, 500, 'easeOutExpo', function() {

                                jQuery(this).animate({
                                    height	: jQuery(window).height(),
                                    top		: 0
                                }, 400, function() {

                                    var $this	= jQuery(this),
                                        $teaser	= $this.find('div.ib-teaser'),
                                        $content= $this.find('div.ib-content-full'),
                                        $close	= $this.find('span.ib-close');

                                    if( hasContentPreview ) {
                                        $teaser.html( teaser )
                                        $content.html( content )
                                    }

                                    $teaser.show();
                                    $content.show();
                                    $close.show();

                                    if( callback ) callback.call();

                                });

                            });

                            if( !hasContentPreview )
                                initContentPreviewEvents();

                        },
                        // preloads an image
                        preloadImage				= function( src, callback ) {

                            jQuery('<img/>').load(function(){

                                if( callback ) callback.call();

                            }).attr( 'src', src );

                        },
                        // load the events for the image preview : navigation ,close button, and window resize
                        initImgPreviewEvents		= function() {

                            var $preview	= jQuery('#ib-img-preview');

                            $preview.find('span.ib-nav-prev').bind('click.ibTemplate', function( event ) {

                                navigate( 'prev' );

                            }).end().find('span.ib-nav-next').bind('click.ibTemplate', function( event ) {

                                navigate( 'next' );

                            }).end().find('span.ib-close').bind('click.ibTemplate', function( event ) {

                                closeImgPreview();

                            });

                            //resizing the window resizes the preview image
                            jQuery(window).bind('resize.ibTemplate', function( event ) {

                                var $largeImg	= $preview.children('img.ib-preview-img'),
                                    dim			= getImageDim( $largeImg.attr('src') );

                                $largeImg.css({
                                    width	: dim.width,
                                    height	: dim.height,
                                    left	: dim.left,
                                    top		: dim.top
                                })

                            });

                        },
                        // load the events for the content preview : close button
                        initContentPreviewEvents	= function() {

                            jQuery('#ib-content-preview').find('span.ib-close').bind('click.ibTemplate', function( event ) {

                                closeContentPreview();

                            });

                        },
                        // navigate the image items in fullscreen mode
                        navigate					= function( dir ) {

                            if( isAnimating ) return false;

                            isAnimating		= true;

                            var $preview	= jQuery('#ib-img-preview'),
                                $loading	= $preview.find('div.ib-loading-large');

                            $loading.show();

                            if( dir === 'next' ) {

                                ( current === imgItemsCount - 1 ) ? current	= 0 : ++current;

                            }
                            else if( dir === 'prev' ) {

                                ( current === 0 ) ? current	= imgItemsCount - 1 : --current;

                            }

                            var $row		= $ibImgItems.eq( current ),
                                largeSrc	= $row.children('img').data('largesrc'),
                                description	= $row.children('span').text();

                            preloadImage( largeSrc, function() {

                                $loading.hide();

                                //get dimentions for the image, based on the windows size
                                var	dim	= getImageDim( largeSrc );

                                $preview.children('img.ib-preview-img')
                                                .attr( 'src', largeSrc )
                                                .css({
                                    width	: dim.width,
                                    height	: dim.height,
                                    left	: dim.left,
                                    top		: dim.top
                                                })
                                                .end()
                                                .find('span.ib-preview-descr')
                                                .text( description );

                                $ibWrapper.scrollTop( $row.offset().top )
                                          .scrollLeft( $row.offset().left );

                                isAnimating	= false;

                            });

                        },
                        // closes the fullscreen image item
                        closeImgPreview				= function() {

                            jQuery('#ib-top').insertBefore(jQuery('#ib-main-wrapper')).removeAttr('style');
                            if( isAnimating ) return false;

                            isAnimating	= true;

                            var $row	= $ibImgItems.eq( current );

                            jQuery('#ib-img-preview').find('span.ib-preview-descr, div.ib-nav, span.ib-close')
                                .hide()
                                .end()
                                .animate({
                                    height	: $row.height(),
                                    top		: $row.offset().top-jQuery(window).scrollTop()
                                    }, 500, 'easeOutExpo', function() {

                                    jQuery(this).animate({
                                        width	: $row.width(),
                                        left	: $row.offset().left
                                        }, 400, function() {

                                            jQuery(this).fadeOut(function() {isAnimating	= false;});
                                            //Enabled scrollbar
                                            jQuery('body').css('overflow','');

                                    });
                            });



                        },
                        // closes the fullscreen content item
                        closeContentPreview			= function() {

                            jQuery('#ib-top').insertBefore(jQuery('#ib-main-wrapper')).removeAttr('style');
                            if( isAnimating ) return false;

                            isAnimating	= true;

                            var $row	= $ibItems.not('.ib-image').eq( current );

                            jQuery('#ib-content-preview').find('div.ib-teaser, div.ib-content-full, span.ib-close')
                                                    .hide()
                                                    .end()
                                                    .animate({
                                                        height	: $row.height(),
                                                        top		: $row.offset().top - jQuery(window).scrollTop()
                                                    }, 500, 'easeOutExpo', function() {

                                                        jQuery(this).animate({
                                                            width	: $row.width(),
                                                            left	: $row.offset().left
                                                        }, 400, function() {

                                                            jQuery(this).fadeOut(function() {isAnimating	= false;});
                                                            //Enabled scrollbar
                                                            jQuery('body').css('overflow','');

                                                        } );

                                                    });

                        },
                        // get the size of one image to make it full size and centered
                        getImageDim					= function( src ) {

                            var img     	= new Image();
                            img.src     	= src;

                            var w_w	= jQuery(window).width(),
                                w_h	= jQuery(window).height(),
                                r_w	= w_h / w_w,
                                i_w	= img.width,
                                i_h	= img.height,
                                r_i	= i_h / i_w,
                                new_w, new_h,
                                new_left, new_top;

                            if( r_w > r_i ) {

                                new_h	= w_h;
                                new_w	= w_h / r_i;

                            }
                            else {

                                new_h	= w_w * r_i;
                                new_w	= w_w;

                            }

                            return {
                                width	: new_w,
                                height	: new_h,
                                left	: (w_w - new_w) / 2,
                                top		: (w_h - new_h) / 2
                            };

                        };

                    return { init : init };

                })();

            Template.init();

        });
    </script>
<?php endif;?>