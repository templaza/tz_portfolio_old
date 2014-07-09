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

?>
<!-- Column setting popbox -->
<div id="columnsettingbox" style="display: none;">
    <ul class="nav nav-tab" id="columnsettings">
        <li class="active"><a  href="#basic" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_BASIC');?></a></li>
        <li><a href="#responsive" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_RESPONSIVE');?></a></li>
<!--        <li><a href="#animation" data-toggle="tab">--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION');?><!--</a></li>-->
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="basic">
            <div id="includetypes">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_HEADING_TYPE');?>: </label>
                <select class="includetypes">
                    <option value="none"><?php echo JText::_('JNONE');?></option>
                    <option value="hits"><?php echo JText::_('JGLOBAL_HITS');?></option>
                    <option value="title"><?php echo JText::_('JGLOBAL_TITLE');?></option>
                    <option value="author"><?php echo JText::_('COM_TZ_PORTFOLIO_USER')?></option>
                    <option value="tag"><?php echo JText::_('COM_TZ_PORTFOLIO_TAGS');?></option>
                    <option value="vote"><?php echo JText::_('COM_TZ_PORTFOLIO_VOTE');?></option>
                    <option value="icons"><?php echo JText::_('COM_TZ_PORTFOLIO_ICONS');?></option>
                    <option value="media"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_MEDIA');?></option>
                    <option value="extra_fields"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_FIELDS');?></option>
                    <option value="author_name"><?php echo JText::_('JAUTHOR');?></option>
                    <option value="fulltext"><?php echo JText::_('COM_CONTENT_FIELD_FULLTEXT');?></option>
                    <option value="introtext"><?php echo JText::_('COM_CONTENT_FIELD_INTROTEXT');?></option>
                    <option value="category"><?php echo JText::_('JCATEGORY');?></option>
                    <option value="comment"><?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT')?></option>
                    <option value="navigation"><?php echo JText::_('COM_TZ_PORTFOLIO_NAVIGATION');?></option>
                    <option value="attachments"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_ATTACHMENTS');?></option>
                    <option value="created_date"><?php echo JText::_('COM_CONTENT_FIELD_CREATED_LABEL');?></option>
                    <option value="gmap"><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDSET_GOOGLE_MAP')?></option>
                    <option value="modified_date"><?php echo JText::_('COM_TZ_PORTFOLIO_MODIFIED_DATE');?></option>
                    <option value="related"><?php echo ucwords(mb_strtolower(JText::_('COM_TZ_PORTFOLIO_RELATED_ARTICLE')));?></option>
                    <option value="social_network"><?php echo JText::_('COM_TZ_PORTFOLIO_SOCIAL_NETWORK');?></option>
                    <option value="published_date"><?php echo JText::_('COM_TZ_PORTFOLIO_PUBLISHED_DATE');?></option>
                    <option value="parent_category"><?php echo JText::_('COM_TZ_PORTFOLIO_PARENT_CATEGORY');?></option>
                    <option value="comment_count"><?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');?></option>
                </select>
            </div>

            <div id="spanwidth">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_SET_WIDTH_LABEL');?>: </label>
                <select class="possiblewidths">
                    <option value=""><?php echo JText::_('JNONE')?></option>
                    <option value="1">span1</option>
                    <option value="2">span2</option>
                    <option value="3">span3</option>
                    <option value="4">span4</option>
                    <option value="5">span5</option>
                    <option value="6">span6</option>
                    <option value="7">span7</option>
                    <option value="8">span8</option>
                    <option value="9">span9</option>
                    <option value="10">span10</option>
                    <option value="11">span11</option>
                    <option value="12">span12</option>
                </select>
            </div>

            <div id="spanoffset">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_OFFSET');?> </label>
                <select class="possibleoffsets">
                    <option value="0">(<?php echo JText::_('JNONE');?>)</option>
                    <option value="1">offset1</option>
                    <option value="2">offset2</option>
                    <option value="3">offset3</option>
                    <option value="4">offset4</option>
                    <option value="5">offset5</option>
                    <option value="6">offset6</option>
                    <option value="7">offset7</option>
                    <option value="8">offset8</option>
                    <option value="9">offset9</option>
                    <option value="10">offset10</option>
                </select>
            </div>

            <div id="customclass">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_CUSTOM_CLASS');?> </label>
                <input type="text" class="customclass" id="inputcustomclass">
            </div>
        </div>

        <div class="tab-pane" id="responsive">
            <label class="checkbox"> <input type="checkbox" value="visible-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_LARGE');?></label>
            <label class="checkbox"> <input type="checkbox" value="visible-md"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_MEDIUM');?></label>
            <label class="checkbox"> <input type="checkbox" value="visible-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_SMALL');?></label>
            <label class="checkbox"> <input type="checkbox" value="visible-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_EXTRA_SMALL');?></label>
            <label class="checkbox"> <input type="checkbox" value="hidden-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_EXTRA_SMALL');?></label>
            <label class="checkbox"> <input type="checkbox" value="hidden-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_SMALL');?></label>
            <label class="checkbox"> <input type="checkbox" value="hidden-md"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_MEDIUM');?></label>
            <label class="checkbox"> <input type="checkbox" value="hidden-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_LARGE');?></label>
        </div>
<!--        <div class="tab-pane" id="animation">-->
<!--            <h4>--><?php //echo JText::_('COM_TZ_PORTFOLIO_CHOOSE_TYPE_OF_ANIMATION');?><!--:</h4>-->
<!--            <select class="animationType">-->
<!--                <option value="none" selected>--><?php //echo JText::_('JNONE');?><!--</option>-->
<!--                <option value="fade-in">Fade-In</option>-->
<!--                <option value="fade-out">Fade-Out</option>-->
<!--                <option value="slide-down-from-top">Slide-Down-From-Top</option>-->
<!--                <option value="slide-in-from-right">Slide-In-From-Right</option>-->
<!--                <option value="slide-up-from-bottom">Slide-Up-From-Bottom</option>-->
<!--                <option value="slide-in-from-left">Slide-In-From-Left</option>-->
<!--                <option value="scale-up">Scale Up</option>-->
<!--                <option value="scale-down">Scale Down</option>-->
<!--                <option value="rotate">Rotate</option>-->
<!--                <option value="flip-y-axis">Flip-Y-Axis</option>-->
<!--                <option value="flip-x-axis">Flip-X-Axis</option>-->
<!--            </select>-->
<!--            <br/>-->
<!--            <h4>--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION_SPEED');?><!--:</h4>-->
<!--            <input type="text" class="animationSpeed"> <br/><p>--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION_SPEED_DESC');?><!--</p>-->
<!--            <h4>--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION_DELAY');?><!--:</h4>-->
<!--            <input type="text" class="animationDelay">-->
<!--            <br/>-->
<!--            <h4>--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION_OFFSET_PERCENTAGE');?><!--:</h4>-->
<!--            <input type="text" class="animationOffset"> <br/><p>--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION_OFFSET_PERCENTAGE_DESC');?><!--</p>-->
<!--            <h4>--><?php //echo JText::_('COM_TZ_PORTFOLIO_ANIMATION_EASING');?><!--:</h4>-->
<!--            <select class="animationEasing">-->
<!--                <option value="ease" selected>ease</option>-->
<!--                <option value="in">in</option>-->
<!--                <option value="out">out</option>-->
<!--                <option value="in-out">in-out</option>-->
<!--                <option value="snap">snap</option>-->
<!--                <option value="easeOutCubic">easeOutCubic</option>-->
<!--                <option value="easeInOutCubic">easeInOutCubic</option>-->
<!--                <option value="easeOutCirc">easeOutCirc</option>-->
<!--                <option value="easeInOutCirc">easeInOutCirc</option>-->
<!--                <option value="easeInExpo">easeInExpo</option>-->
<!--                <option value="easeOutExpo">easeOutExpo</option>-->
<!--                <option value="easeInOutExpo">easeInOutExpo</option>-->
<!--                <option value="easeInQuad">easeInQuad</option>-->
<!--                <option value="easeOutQuad">easeOutQuad</option>-->
<!--                <option value="easeInOutQuad">easeInOutQuad</option>-->
<!--                <option value="easeInQuart">easeInQuart</option>-->
<!--                <option value="easeOutQuart">easeOutQuart</option>-->
<!--                <option value="easeInOutQuart">easeInOutQuart</option>-->
<!--                <option value="easeInQuint">easeInQuint</option>-->
<!--                <option value="easeOutQuint">easeOutQuint</option>-->
<!--                <option value="easeInOutQuint">easeInOutQuint</option>-->
<!--                <option value="easeInSine">easeInSine</option>-->
<!--                <option value="easeOutSine">easeOutSine</option>-->
<!--                <option value="easeInOutSine">easeInOutSine</option>-->
<!--                <option value="easeInBack">easeInBack</option>-->
<!--                <option value="easeOutBack">easeOutBack</option>-->
<!--                <option value="easeInOutBack">easeInOutBack</option>-->
<!--            </select>-->
<!--            <br/>-->
<!--        </div>-->
    </div>
</div>

<!-- Row setting popbox -->
<div id="rowsettingbox" style="display: none;">
    <h3 class="row-header"><?php echo JText::_('COM_TZ_PORTFOLIO_ROW_SETTINGS');?></h3>

    <div>
        <div class="row-fluid">


            <div class="span6 rownameOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_NAME');?>: </label>
                <input type="text" class="rowname" id="">
            </div>

            <div class="span6 rowclassOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_CUSTOM_CLASS');?> </label>
                <input type="text" class="rowcustomclass" id="">
            </div>

        </div>

        <div class="row-fluid">
            <div class="span6 rowcolorOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_BACKGROUND');?> </label>
                <input type="text" class="rowbackgroundcolor" id="">
            </div>

            <div class="span6 rowcolorOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_TEXT');?>: </label>
                <input type="text" class="rowtextcolor" id="">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 rowcolorOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_LINK');?>: </label>
                <input type="text" class="rowlinkcolor" id="">
            </div>

            <div class="span6 rowcolorOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_LINK_HOVER');?>: </label>
                <input type="text" class="rowlinkhovercolor" id="">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 rownameOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_MARGIN');?>: </label>
                <input type="text" class="rowmargin" id="">
            </div>

            <div class="span6 rowclassOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PADDING');?>: </label>
                <input type="text" class="rowpadding" id="">
            </div>
        </div>

        <div id="rowresponsiveinputs" class="row-fluid">
            <div class="span6">
                <label class="checkbox"> <input type="checkbox" value="visible-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_EXTRA_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-md"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_VISIBLE_LARGE');?></label>
            </div>
            <div class="span6">
                <label class="checkbox"> <input type="checkbox" value="hidden-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_EXTRA_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-md"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_HIDDEN_LARGE');?></label>
            </div>
        </div>

    </div>
</div>