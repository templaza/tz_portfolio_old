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

$document   =   JFactory::getDocument();
if($library ==1){
    $document->addScript('modules/mod_tz_portfolio_categories/js/jquery-1.7.2.min.js');
}
$document->addStyleSheet('modules/mod_tz_portfolio_categories/css/mod_tz_portfolio_slider.css');
$document->addScript('modules/mod_tz_portfolio_categories/js/jquery.jcarousel.js');
$document->addStyleDeclaration('
  .jcarousel-skin-tango .jcarousel-container-horizontal{
    width: ' . $sl_width . 'px;
  }
  .jcarousel-skin-tango .jcarousel-clip-horizontal{
    width: ' . $sl_width . 'px;
    height: ' . $sl_height . 'px;
  }
  .jcarousel-skin-tango .jcarousel-item{
     width: ' . $width . 'px;
     height: ' . $sl_height . 'px;

  }
');

if($list):

?>
<ul id="mycarousel" class="jcarousel-skin-tango">
  <?php foreach($list as $item) : ?>

      <li>
        <img src="<?php echo $item->images; ?>"  alt="<?php echo $item -> title;?>" />
        <?php if($title == 1){ ?>
            <a class="catslidetitle" href="<?php echo $item->link; ?>"><?php echo $item->title; ?>
                <?php if(isset($item -> total)):?>
                    <span>(<?php echo $item -> total?>)</span>
                <?php endif;?>
            </a>
        <?php } ?>
        <?php if($des == 1){ ?>
            <div class="catslidedes"><?php echo $item->description; ?></div>
        <?php } ?>
        <?php if($read == 1){ ?>
            <a class="readmore" href="<?php echo $item->link; ?>"><?php echo $text; ?></a>
        <?php } ?>
      </li>
  <?php endforeach; ?>
</ul>

<?php endif;?>

<script type="text/javascript">
  var tz = jQuery.noConflict();
  tz('.jcarousel-skin-tango').jcarousel({
      wrap: 'circular'
  });
</script>