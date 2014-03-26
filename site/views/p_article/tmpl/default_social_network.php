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
$params         = $this -> item -> params;
$doc            = JFactory::getDocument();
$socialInfos    = $this -> socialInfo;
$url = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($this -> item -> slug,$this -> item -> catid),true,-1);
//$url    = 'http://www.templaza.com';
?>
<?php if(($params -> get('show_twitter_button',1) == 1) OR ($params -> get('show_facebook_button',1) == 1)
         OR ($params -> get('show_google_button',1) == 1) OR $params -> get('show_pinterest_button',1)
        OR $params -> get('show_linkedin_button',1)):?>
    <div class="breadcrumb tz_portfolio_like_button">
        <span class="icon-plus-2 label-info TzAdd"></span>

        <div class="TzLikeButtonInner">
            <span class="TzLikeQuestion"><?php echo JText::_('COM_TZ_PORTFOLIO_SOCIAL_QUESTION');?></span>

            <?php if($params -> get('show_facebook_button',1) == 1): ?>
            <!-- Facebook Button -->
            <div class="FacebookButton">
                <?php if(!$params -> get('show_comment') OR ($params -> get('show_comment')
                    AND $params -> get('tz_comment_type') != 'facebook')):?>
                <div id="fb-root"></div>
                <script type="text/javascript">
                    (function(d, s, id) {
                      var js, fjs = d.getElementsByTagName(s)[0];
                      if (d.getElementById(id)) {return;}
                      js = d.createElement(s); js.id = id;
                      js.src = "//connect.facebook.net/en_US/all.js#appId=177111755694317&xfbml=1";
                      fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                </script>
                <?php endif;?>
                <div class="fb-like" data-send="false" data-width="200" data-show-faces="true"
                      data-layout="button_count" data-href="<?php echo $url;?>"></div>
            </div>
            <?php endif; ?>

            <?php if($params -> get('show_twitter_button',1) == 1): ?>
            <!-- Twitter Button -->
            <div class="TwitterButton">
                <a href="https://twitter.com/share" data-url="<?php echo $url;?>" class="twitter-share-button"
                   data-count="horizontal"<?php //if($this->item->params->get('twitterUsername')): ?>
                   data-via="<?php //echo $this->item->params->get('twitterUsername'); ?>"
                            data-size="small">
                </a>
                <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
            </div>
            <?php endif; ?>

            <?php if($params -> get('show_google_button',1) == 1): ?>
            <!-- Google +1 Button -->
            <div class="GooglePlusOneButton">
                <!-- Place this tag where you want the +1 button to render. -->
                <div class="g-plusone" data-size="medium" data-href="<?php echo $url?>"></div>

                <!-- Place this tag after the last +1 button tag. -->
                <script type="text/javascript">
                  (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/plusone.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>
            </div>
            <?php endif; ?>

            <?php if($params -> get('show_pinterest_button',1)):?>
            <!-- Pinterest Button -->
            <div class="PinterestButton">
                <a href="//www.pinterest.com/pin/create/button/?url=<?php echo urlencode($url);?>"
                        data-pin-do="buttonPin" data-pin-config="beside" data-pin-color="red">
                        <img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" alt=""/>
                </a>
                <script type="text/javascript">
                (function(d){
                    var f = d.getElementsByTagName('SCRIPT')[0], p = d.createElement('SCRIPT');
                    p.type = 'text/javascript';
                    p.async = true;
                    p.src = '//assets.pinterest.com/js/pinit.js';
                    f.parentNode.insertBefore(p, f);
                }(document));
                </script>
            </div>
            <?php endif;?>

            <?php if($params -> get('show_linkedin_button',1)):?>
            <!-- Linkedin Button -->
            <div class="LinkedinButton">
                <script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
                <script type="IN/Share" data-url="<?php echo $url;?>" data-counter="right"></script>
            </div>
            <?php endif;?>

            <div class="clearfix"></div>
        </div>
    </div>
<?php endif; ?>