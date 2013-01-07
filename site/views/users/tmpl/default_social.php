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
$params = $this -> params;
?>
<?php if($params -> get('show_twitter_button',1) OR $params -> get('show_twitter_button',1) OR $params -> get('show_google_button',1)):?>
    <div class="tz_portfolio_like_button">
        <?php if($params -> get('show_twitter_button',1)): ?>
            <!-- Twitter Button -->
            <div class="UserTwitterButton">
                <a href="<?php echo $this -> listsUsers -> twitter;?>" class="twitter-share-button" data-count="horizontal"<?php //if($this->item->params->get('twitterUsername')): ?> data-via="<?php //echo $this->item->params->get('twitterUsername'); ?>"<?php //endif; ?>>
                    <?php //echo JText::_('K2_TWEET'); ?>
                </a>
                <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
            </div>
        <?php endif; ?>

        <?php if($params -> get('show_facebook_button',1)): ?>
            <!-- Facebook Button -->
            <div class="UserFacebookButton">
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
                <div class="fb-like" data-send="false" data-width="20" data-show-faces="true"></div>
            </div>
        <?php endif; ?>

        <?php if($params -> get('show_google_button',1)): ?>
            <!-- Google +1 Button -->
            <div class="UserGooglePlusOneButton" style="width: 20px;">
                <g:plusone annotation="inline" width="10"  href="<?php echo $this -> listsUsers -> google_one?>"></g:plusone>
                <script type="text/javascript">
                  (function() {
                    window.___gcfg = {lang: 'en'}; // Define button default language here
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/plusone.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>
            </div>
            <?php endif; ?>
    </div>
<?php endif; ?>