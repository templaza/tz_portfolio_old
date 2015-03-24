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

$media  = $this -> listMedia;
$params = $this -> item -> params;

$src    = '';
if($params -> get('detail_article_image_size','L')):
    if(!empty($media[0] -> images)):
        $src    = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),'_'
                                          .$params -> get('detail_article_image_size','L')
                                          .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);
    endif;
endif;
$href   = null;
$class  = null;
$rel    = null;
if($params -> get('useCloudZoom',1) == 1):
    $effect   = null;
    if($params -> get('zoomWidth'))
        $effect[]   = 'zoomWidth:'.$params -> get('zoomWidth');
    if($params -> get('zoomHeight'))
        $effect[]   = 'zoomHeight:'.$params -> get('zoomHeight');
    if($params -> get('position','inside'))
        $effect[]   = 'position:\''.$params -> get('position','inside').'\'';
    if($params -> get('adjustX'))
        $effect[]   = 'adjustX:'.$params -> get('adjustX');
    if($params -> get('adjustY'))
        $effect[]   = 'adjustY:'.$params -> get('adjustY');
    if($params -> get('tint'))
        $effect[]   = 'tint:\''.$params -> get('tint').'\'';
    if($params -> get('tintOpacity'))
        $effect[]   = 'tintOpacity:'.$params -> get('tintOpacity');
    if($params -> get('lensOpacity'))
        $effect[]   = 'lensOpacity:'.$params -> get('lensOpacity');
    if($params -> get('softFocus',0) == 1)
        $effect[]   = 'softFocus: true';
    else
        $effect[]   = 'softFocus: false';
    if($params -> get('smoothMove',3))
        $effect[]   = 'smoothMove:'.$params -> get('smoothMove',3);
    if($params -> get('showTitle',1) == 1)
        $effect[]   = 'showTitle:true';
    else
        $effect[]   = 'showTitle:false';
    if($params -> get('titleOpacity'))
        $effect[]   = 'titleOpacity:'.$params -> get('titleOpacity');

    $effect = implode(',',$effect);
    if(!empty($media[0] -> images)):
        $href       = JURI::root().$media[0]-> images;
    endif;

    if($params -> get('article_image_zoom_size','XL')):
        if(!empty($media[0] -> images)):
            $href    = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),
                                '_'.$params -> get('article_image_zoom_size','XL')
                              .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);
        endif;
    endif;
    
    $class    = 'cloud-zoom';
    $rel        = ' rel="'.$effect.'"';
endif;
?>
<?php if($media):?>
    <?php if(!empty($media[0] -> images) OR !empty($media[0] -> thumb)):?>
    <div class="TzArticleMedia">
        <?php if($params -> get('show_image',1) == 1):?>
            <?php if($media[0] -> type == 'image'):?>
                <div class="tz_portfolio_image" style="position: relative;">
                    <?php if($params -> get('useCloudZoom',1) == 1):?>
                    <a<?php if($class) echo ' class="'.$class.'"'?> href="<?php echo $href;?>"<?php if($rel) echo $rel?>>
                    <?php endif;?>

                        <img src="<?php echo $src;?>" alt="<?php if(isset($media[0] -> imagetitle)) echo $media[0] -> imagetitle;?>"
                                 title="<?php if(isset($media[0] -> imagetitle)) echo $media[0] -> imagetitle;?>"
                                 itemprop="thumbnailUrl">
                        <?php if($params -> get('tz_use_image_hover',1) == 1):?>
                            <?php if(isset($srcHover)):?>
                                <img class="tz_image_hover"
                                    src="<?php echo $srcHover;?>"
                                 alt="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                                 title="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>">
                            <?php endif;?>
                        <?php endif;?>
                    <?php if($params -> get('useCloudZoom',1) == 1):?>
                    </a>
                    <?php endif;?>
                </div>
            <?php endif;?>
        <?php endif;?>

        <?php if($params -> get('show_image_gallery',1) == 1):?>
            <?php
                if($media[0] -> type == 'imagegallery'):
                    if($params -> get('show_arrows_image_gallery',1) == 1)
                    $dirNav   = 'true';
                else
                    $dirNav   = 'false';
                if($params -> get('show_controlNav_image_gallery',1) == 1) {
                    $controlNav = 'true';
                    if ($params->get('controlnav_type', 'none') == 'thumbnails' )
                        $controlNav = $params->get('controlnav_type', 'none');
                }else {
                    $controlNav = 'false';
                }

                if($params -> get('image_gallery_pausePlay',1) == 1)
                    $pausePlay   = 'true';
                else
                    $pausePlay   = 'false';

                if($params -> get('image_gallery_pauseOnAction',1) == 1)
                    $pauseOnAction   = 'true';
                else
                    $pauseOnAction   = 'false';

                if($params -> get('image_gallery_pauseOnHover',1) == 1)
                    $pauseOnHover   = 'true';
                else
                    $pauseOnHover   = 'false';

                if($params -> get('image_gallery_useCSS',1) == 1)
                    $useCSS   = 'true';
                else
                    $useCSS   = 'false';

                $animation  = '\'fade\'';
                if($params -> get('image_gallery_animation')):
                    $animation  = '\''.$params -> get('image_gallery_animation').'\'';
                endif;

                if($params -> get('image_gallery_slideshow',1) == 1):
                    $slideshow  = 'true';
                else:
                    $slideshow  = 'false';
                endif;

                if($params -> get('image_gallery_animationLoop',1)):
                    $animationLoop  = 'true';
                else:
                    $animationLoop  = 'false';
                endif;
                if($params -> get('image_gallery_smoothHeight',1)):
                    $smoothHeight   = 'true';
                else:
                    $smoothHeight   = 'false';
                endif;

                if($params -> get('image_gallery_randomize',0)):
                    $randomize  = 'true';
                else:
                    $randomize  = 'false';
                endif;

                switch ($params -> get('detail_article_image_gallery_size')):
                    case 'XS':
                        $name   = 'xsmall';
                        break;
                    case 'S':
                        $name   = 'small';
                        break;
                    case 'M':
                        $name   = 'medium';
                        break;
                    case 'L':
                        $name   = 'large';
                        break;
                    case 'XL':
                        $name   = 'xlarge';
                        break;
                endswitch;
                $before = '';
                if($params -> get('image_gallery_slide_direction') == 'horizontal'){
                    $before = 'var sBrowser    = navigator.userAgent;
                        if(sBrowser.toLowerCase().indexOf("firefox") > -1){
                            slider.prop = "left";
                        }
                        ';
                }

                $doc    = JFactory::getDocument();
                $doc -> addScriptDeclaration('
                    jQuery(document).ready(function(){
                        jQuery(\'.flexslider\').flexslider({
                            animation: '.$animation.',
                            slideDirection: "'.$params -> get('image_gallery_slide_direction').'",
                            slideshow: '.$slideshow.',
                            slideshowSpeed: '.$params -> get('image_gallery_animSpeed').',
                            animationDuration: '.$params -> get('image_gallery_animation_duration').',
                            directionNav: '.$dirNav.',
                            controlNav: '.(($controlNav=='thumbnails')?'"'.$controlNav.'"':$controlNav).',
                            prevText: "'.JText::_('Previous').'",
                            nextText: "'.JText::_('Next').'",
                            pausePlay: '.$pausePlay.',
                            pauseText: "'.JText::_('Pause').'",
                            playText: "'.JText::_('Play').'",
                            pauseOnAction: '.$pauseOnAction.',
                            pauseOnHover: '.$pauseOnHover.',
                            useCSS: '.$useCSS.',
                            startAt: '.$params -> get('image_gallery_startAt',0).',
                            animationLoop: '.$animationLoop.',
                            smoothHeight: '.$smoothHeight.',
                            randomize: '.$randomize.',
                            itemWidth:'.$params -> get('image_gallery_itemWidth',0).',
                            itemMargin:'.$params -> get('image_gallery_itemMargin',0).',
                            minItems:'.$params -> get('image_gallery_minItems',0).',
                            maxItems:'.$params -> get('image_gallery_maxItems',0).',
                            start: function(){
                                jQuery(".flexslider").css("width","'.$params -> get('tz_image_gallery_'.$name).'px")
                            }
                        });
                    });
                ');
            ?>
                <div class="tz_portfolio_image_gallery">
                    <div class="flexslider">
                        <ul class="slides">
                            <?php foreach($media as $rowMedia):?>
                                <?php
                                    $src        = JURI::root().str_replace('.'.JFile::getExt($rowMedia -> images),
                                                '_'.$params -> get('detail_article_image_gallery_size','L')
                                              .'.'.JFile::getExt($rowMedia -> images),$rowMedia -> images);
                                    $thumb_src  = JURI::root().str_replace('.'.JFile::getExt($rowMedia -> images),
                                                '_S.'.JFile::getExt($rowMedia -> images),$rowMedia -> images);
                                ?>
                                <li<?php echo ($controlNav=='thumbnails')?' data-thumb="'.$thumb_src.'"':''?>>
                                    <img src="<?php echo $src;?>"
                                         alt="<?php echo ($rowMedia -> imagetitle)?($rowMedia -> imagetitle):($this -> item -> title);?>"
                                        <?php if(!empty($rowMedia -> imagetitle)):?>
                                            title="<?php echo $rowMedia -> imagetitle;?>"
                                        <?php endif; ?>
                                    />

                                    <?php
                                        if($rowMedia -> imagetitle):
                                    ?>
                                    <p class="flex-caption"><?php echo $rowMedia -> imagetitle?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>

        <?php if($params -> get('show_video',1) == 1):?>
            <?php
            if($media[0] -> type == 'video'):
            ?>
                <div class="tz_portfolio_video" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
                    <?php
                        switch ($media[0] -> from):
                            case 'default':
                                echo $media[0] -> images;
                            break;
                            case 'vimeo':
                    ?>
                        <iframe src="http://player.vimeo.com/video/<?php echo $media[0] -> images;?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent"
                            width="<?php echo ($params -> get('video_width'))?$params -> get('video_width'):'600';?>"
                            height="<?php echo ($params -> get('video_height'))?$params -> get('video_height'):'255';?>"
                            frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen itemprop="embedUrl">
                        </iframe>
                    <?php
                                break;
                            case 'youtube':
                    ?>
                            <iframe  width="<?php echo ($params -> get('video_width'))?$params -> get('video_width'):'600';?>"
                                    height="<?php echo ($params -> get('video_height'))?$params -> get('video_height'):'315';?>"
                                    src="http://www.youtube.com/embed/<?php echo $media[0] -> images;?><?php echo (!empty($media[0] -> imagetitle))?'?title='.$media[0] -> imagetitle:'';?>"
                                    frameborder="0" allowfullscreen wmode="transparent" itemprop="embedUrl">
                            </iframe>
                        <?php break;?>
                    <?php endswitch;?>
                </div>
            <?php endif;?>
        <?php endif;?>

        <?php echo $this -> loadTemplate('audio');?>
    </div>
    <?php endif;?>
<?php endif;?>