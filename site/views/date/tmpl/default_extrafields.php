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
 
// no direct access
defined('_JEXEC') or die('Restricted access');
$params = $this -> item -> params;
$list   = $this -> listFields;
?>
<?php if($params -> get('show_extra_fields') != '0'):?>
    <?php if($this -> listFields):?>
        <div class="TzBlogExtraField">
            <span class="ExtraFieldTitle"><?php echo JText::_('COM_TZ_PORTFOLIO_ADDITIONAL_INFO');?></span>
            <ul class="TzExtra">
                <?php foreach($list as $i => $row):?>
                    <?php
                        $bool   = true;
                        $images = $row -> images;
                        if(isset($images)):
                            if($params -> get('field_show_type') == 'image'):
                                $bool2  = false;
                                foreach($images as $image){
                                    if(!empty($image)){
                                        $bool2 = true;
                                        break;
                                    }
                                }

                               if($bool2 == false)
                                   $bool = false;
                            endif;
                        endif;
                    ?>
                    <?php if($bool == true):?>
                        <li class="TzEx">

							<?php
							$show_title = true;
							if(count($row -> value) == 1):
								$value  = $row -> value;
								if(empty($value[0])):
									if($params -> get('field_show_type','textimage') == 'textimage' AND empty($images[0])):
										$show_title = false;
									endif;
								endif;
							endif;
                            ?>
                            <?php if($show_title == true):?>
                            <span class="name"><?php echo $row -> title;?>:</span>
                            <?php endif;?>
							
                            <?php
                                if(count($row -> value) > 0):
                            ?>
                                <span class="tzFieldText">
                                    <?php
                                        foreach($row -> value as $j => $item):
                                    ?>
                                        <?php
                                            if(isset($images) AND !empty($images)):
                                                if($params -> get('field_show_type','textimage') == 'image'
                                                     OR $params -> get('field_show_type','textimage') == 'textimage'):
                                                    $height = null;
                                                    $crop   = null;

                                                    $src    = JURI::root().$images[$j];
                                                    
                                                    $item   = trim($item);

                                                    if(preg_match('/^<a.*?>.*?<\/a>$/',htmlspecialchars_decode($item),$match)):
                                                        $link   = htmlspecialchars_decode($item);
                                                        preg_match('/^<a.*?>/',$link,$match2);
                                                        echo $match2[0];
                                                    endif;

                                        ?>
											<?php if($src AND !empty($images[$j])):?>
                                            <img src="<?php echo $src?>"
                                                alt="<?php echo $row -> title; ?>"/>
											<?php endif;?>
                                        <?php
                                                    if(preg_match('/^<a.*?>.*?<\/a>$/',htmlspecialchars_decode($item),$match)):
                                                        echo '</a>';
                                                    endif;
                                                endif;
                                            endif;
                                        ?>
                                        <?php
                                            if($params -> get('field_show_type','textimage') == 'text'
                                               OR $params -> get('field_show_type','textimage') == 'textimage'):
                                                echo htmlspecialchars_decode($item);
                                            endif;
                                            if($params -> get('field_show_type','textimage') != 'image'
                                                   AND $params -> get('field_show_type','textimage') != 'textimage'
                                                    AND $j<count($row -> value) -1):
                                                    echo ',';
                                            endif;
                                        ?>
                                    <?php
                                        endforeach;
                                    ?>
                                </span>
                            <?php endif;?>
                        </li>
                    <?php endif;?>
                <?php endforeach; ?>
            </ul>
            <div style="clear:both;"></div>
        </div>
    <?php endif;?>
<?php endif; ?>