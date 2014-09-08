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

$media      = $this -> listMedia;
$params     = $this -> mediaParams;
$audioOption= '';
if(count($media)):
    if($params -> get('show_soundcloud_artwork',1)){
        $audioOption    .= '&amp;show_artwork=true';
    }else{
        $audioOption    .= '&amp;show_artwork=false';
    }
    if($params -> get('soundcloud_auto_play',0)){
        $audioOption    .= '&amp;auto_play=true';
    }else{
        $audioOption    .= '&amp;auto_play=false';
    }
    if($params -> get('show_soundcloud_sharing',1)){
        $audioOption    .= '&amp;sharing=true';
    }else{
        $audioOption    .= '&amp;sharing=false';
    }
    if($params -> get('show_soundcloud_buying',1)){
        $audioOption    .= '&amp;buying=true';
    }else{
        $audioOption    .= '&amp;buying=false';
    }
    if($params -> get('show_soundcloud_download',1)){
        $audioOption    .= '&amp;download=true';
    }else{
        $audioOption    .= '&amp;download=false';
    }
    if($params -> get('show_soundcloud_user',1)){
        $audioOption    .= '&amp;show_user=true';
    }else{
        $audioOption    .= '&amp;show_user=false';
    }
    if($params -> get('show_soundcloud_playcount',1)){
        $audioOption    .= '&amp;show_playcount=true';
    }else{
        $audioOption    .= '&amp;show_playcount=false';
    }
    if($params -> get('show_soundcloud_comments',1)){
        $audioOption    .= '&amp;show_comments=true';
    }else{
        $audioOption    .= '&amp;show_comments=false';
    }

    if($color   = $params -> get('audio_soundcloud_color','transparent')){
        if($color != 'transparent'){
            $audioOption    .= '&amp;color='.str_replace('#','',$color);
        }
    }
    if($themeColor   = $params -> get('audio_soundcloud_theme_color','transparent')){
        if($themeColor != 'transparent'){
            $audioOption    .= '&amp;theme_color='.$themeColor;
        }
    }
    if($audioWidth   = $params -> get('audio_soundcloud_width','100%')){
        if(!preg_match('/[0-9]+(\%|px)/i',$audioWidth)){
            $audioWidth .= 'px';
        }
    }
    if($audioHeight   = $params -> get('audio_soundcloud_height','166')){
        if(!preg_match('/[0-9]+(\%|px)/i',$audioHeight)){
            $audioHeight .= 'px';
        }
    }
?>

    <?php if($params -> get('show_audio',1)):?>
        <?php
        if($media[0] -> type == 'audio'):

        ?>
            <div class="tz_audio" itemprop="audio" itemscope itemtype="http://schema.org/AudioObject">
                <iframe width="<?php echo $audioWidth;?>"
                        height="<?php echo $audioHeight;?>"
                        src="http://w.soundcloud.com/player/?url=http://api.soundcloud.com/tracks/<?php echo $media[0] -> audio_id.$audioOption;?>"
                        frameborder="0" allowfullscreen
                        itemprop="embedUrl">
                </iframe>
            </div>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>
