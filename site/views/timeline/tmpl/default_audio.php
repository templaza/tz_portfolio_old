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
        $audioOption    .= '&show_artwork=true';
    }else{
        $audioOption    .= '&show_artwork=false';
    }
    if($params -> get('soundcloud_auto_play',0)){
        $audioOption    .= '&auto_play=true';
    }else{
        $audioOption    .= '&auto_play=false';
    }
    if($params -> get('show_soundcloud_sharing',1)){
        $audioOption    .= '&sharing=true';
    }else{
        $audioOption    .= '&sharing=false';
    }
    if($params -> get('show_soundcloud_buying',1)){
        $audioOption    .= '&buying=true';
    }else{
        $audioOption    .= '&buying=false';
    }
    if($params -> get('show_soundcloud_download',1)){
        $audioOption    .= '&download=true';
    }else{
        $audioOption    .= '&download=false';
    }
    if($params -> get('show_soundcloud_user',1)){
        $audioOption    .= '&show_user=true';
    }else{
        $audioOption    .= '&show_user=false';
    }
    if($params -> get('show_soundcloud_playcount',1)){
        $audioOption    .= '&show_playcount=true';
    }else{
        $audioOption    .= '&show_playcount=false';
    }
    if($params -> get('show_soundcloud_comments',1)){
        $audioOption    .= '&show_comments=true';
    }else{
        $audioOption    .= '&show_comments=false';
    }

    if($color   = $params -> get('audio_soundcloud_color','transparent')){
        if($color != 'transparent'){
            $audioOption    .= '&color='.str_replace('#','',$color);
        }
    }
    if($themeColor   = $params -> get('audio_soundcloud_theme_color','transparent')){
        if($themeColor != 'transparent'){
            $audioOption    .= '&theme_color='.$themeColor;
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
            <div class="tz_audio">
                <iframe width="<?php echo $audioWidth;?>"
                        height="<?php echo $audioHeight;?>"
                        src="http://w.soundcloud.com/player/?url=http://api.soundcloud.com/tracks/<?php echo $media[0] -> audio_id.$audioOption;?>"
                        frameborder="0" allowfullscreen>
                </iframe>
            </div>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>
