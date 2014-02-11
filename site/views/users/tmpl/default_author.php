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

$authorParams   = $this -> authorParams;
$tmpl           = JRequest::getString('tmpl');
?>

<?php if($authorParams -> get('show_user',1)):?>
    <?php if($this -> listAuthor):?>
        <?php
        if($this -> listAuthor -> images){
            $images = JURI::root().$this -> listAuthor -> images;
        }
        else{
            $images = JURI::root().'components/com_tz_portfolio/assets/no_user.png';
        }


        ?>
        <?php
        $target = '';
        if(isset($tmpl) AND !empty($tmpl)):
            $target = ' target="_blank"';
        endif;
        ?>
        <div class="clr"></div>
        <div class="tz_portfolio_user">
            <h3 class="TzArticleAuthorTitle"><?php echo JText::_('ARTICLE_AUTHOR_TITLE'); ?></h3>
            <div class="media">
                <div class="AuthorAvatar pull-left">
                    <img src="<?php echo $images;?>" alt="<?php echo $this -> listAuthor -> name;?>"/>
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$this -> listAuthor -> id.'&amp;Itemid='.JRequest::getCmd('Itemid'));?>"<?php echo $target?>>
                            <?php echo $this -> listAuthor -> name;?>
                        </a>
                    </h4>

                    <?php if($authorParams -> get('show_gender_user')):?>
                        <?php if($this -> listAuthor -> gender):?>
                            <span class="muted AuthorGender">
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_GENDER');?>
                                <span><?php if($this -> listAuthor -> gender == 'm'): echo JText::_('Male');?>
                                    <?php elseif($this -> listAuthor -> gender == 'f'): echo JText::_('Female');?>
                                    <?php endif;?>
                                    </span>
                                </span>
                        <?php endif;?>
                    <?php endif;?>

                    <?php if($authorParams -> get('show_email_user')):?>
                        <?php if($this -> listAuthor -> email):?>
                            <span class="muted AuthorEmail">
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_EMAIL');?>
                                <span>
                                        <?php echo $this -> listAuthor -> email;?>
                                    </span>
                                </span>
                        <?php endif;?>
                    <?php endif;?>

                    <?php if($authorParams -> get('show_description_user')  AND !empty($this -> listAuthor -> description)):?>
                        <?php echo $this -> listAuthor -> description?>
                    <?php endif;?>

                    <?php if(($authorParams -> get('show_url_user',1) AND !empty($this -> listAuthor -> url))
                            OR (!empty($this -> listAuthor -> twitter) OR !empty($this -> listAuthor -> facebook)
                            OR !empty($this -> listAuthor -> google_one))):?>
                    <div class="AuthorSocial">
                        <span><?php echo JText::_('COM_TZ_PORTFOLIO_QUESTION');?></span>
                        <?php if($authorParams -> get('show_url_user',1) AND !empty($this -> listAuthor -> url)):?>
                            <span class="AuthorUrl">
                                <span class="TzLine">|</span>
                                <?php echo JText::_('COM_TZ_PORTFOLIO_WEBSITE');?>
                                <a href="<?php echo $this -> listAuthor -> url;?>" target="_blank">
                                    <?php echo $this -> listAuthor -> url;?>
                                </a>
                            </span>
                        <?php endif;?>

                        <?php if(!empty($this -> listAuthor -> twitter) OR !empty($this -> listAuthor -> facebook)
                            OR !empty($this -> listAuthor -> google_one)):?>
                            <span class="TzLine">|</span>
                        <?php endif;?>
                        <?php if(!empty($this -> listAuthor -> twitter)): ?>
                            <a class="TzSocialLink" href="<?php echo $this -> listAuthor -> twitter?>"<?php echo $target?>>
                                <img src="components/com_tz_portfolio/assets/twitter.png"
                                    alt=""/>

                            </a>
                        <?php endif;?>
                        <?php if(!empty($this -> listAuthor -> facebook)):?>
                            <a class="TzSocialLink" href="<?php echo $this -> listAuthor -> facebook;?>"<?php echo $target?>>
                                <img src="components/com_tz_portfolio/assets/facebook.png" alt=""/>
                            </a>
                        <?php endif;?>
                        <?php if($this -> listAuthor -> google_one AND !empty($this -> listAuthor -> google_one)):?>
                            <a class="TzSocialLink" href="<?php echo $this -> listAuthor -> google_one?>"<?php echo $target?>>
                                <img src="components/com_tz_portfolio/assets/google_one.png" alt=""/>
                            </a>
                        <?php endif;?>
                    </div>
                    <?php endif;?>
                    <div class="clr"></div>
                </div>
            </div>

        </div>

    <?php endif;?>
<?php endif; ?>