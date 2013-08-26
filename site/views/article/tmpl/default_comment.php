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
?>
<?php if($params -> get('show_comment',1)):?>
    <div class="clr"></div>
    <div class="tz_portfolio_comment">
        <div id="fb-root"></div>
        <fb:comments href="<?php echo $this -> linkCurrent;?>" num_posts="2" ></fb:comments>
    </div>
<?php endif;?>
