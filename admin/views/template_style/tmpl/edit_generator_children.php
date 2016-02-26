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

// No direct access
defined('_JEXEC') or die('Restricted access');

if($children   = $this -> childrens){
?>
<?php
//if( !empty($children -> children) and is_array($children -> children) ) {
    foreach( $children ->children as $i => $children )
    {
        ?>
        <div class="row-fluid child-row">
            <div class="span12">
                <div class="rowpropperties pull-left">
                    <span class="rowname"><?php echo $children -> name; ?></span>
                                                                                                                <span class="rowdocs">
                                                                                                                    <input type="hidden" class="rownameinput" name="" value="<?php echo $this -> get_value($children,"name") ?>">
                                                                                                                    <input type="hidden" class="rowcustomclassinput" name="" value="<?php echo $this -> get_value($children,"class") ?>">
                                                                                                                    <input type="hidden" class="rowresponsiveinput" name="" value="<?php echo $this -> get_value($children,"responsive") ?>">
                                                                                                                    <input type="hidden" class="rowbackgroundcolorinput" name="" value="<?php echo $this -> get_color($children,'backgroundcolor') ?>">
                                                            <input type="hidden" class="rowtextcolorinput" name="" value="<?php echo $this -> get_color($children,'textcolor') ?>">
                                                            <input type="hidden" class="rowlinkcolorinput" name="" value="<?php echo $this -> get_color($children,'linkcolor') ?>">
                                                            <input type="hidden" class="rowlinkhovercolorinput" name="" value="<?php echo $this -> get_color($children,'linkhovercolor') ?>">
                                                            <input type="hidden" class="rowmargininput" name="" value="<?php echo $this -> get_value($children,'margin') ?>">
                                                            <input type="hidden" class="rowpaddinginput" name="" value="<?php echo $this -> get_value($children,'padding') ?>">
                                                                                                                </span>
                </div>
                <div class="pull-right row-tools">
                    <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_MOVE_THIS_ROW');?>" class="fa fa-arrows rowmove"></a>
                    <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fa fa-bars add-row"></a>
                    <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_COLUMN');?>" class="fa fa-columns add-column"></a>
                    <a href="#rowsettingbox" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ROW_SETTINGS');?>" class="fa fa-cog rowsetting" rel="rowpopover"></a>
                    <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_ROW');?>" class="fa fa-times rowdelete"></a>
                </div>

                <div class="clearfix"></div>

                <div class="row-fluid show-grid">

                    <?php
                    foreach($children -> children as $children)
                    {
                        ?>

                        <div class="<?php echo ($this -> get_value($children,"type")=='component' or $this -> get_value($children,"type")=='message') ? 'type-'.$this -> get_value($children,"type"):'' ?>  span<?php echo $this -> get_value($children,"col-lg"); ?> column <?php echo ( empty($children -> {"col-lg-offset"})?'':'offset'.$children->{"col-lg-offset"})?>">

                                                                                                                        <span class="position-name"><?php echo $children ->type;
                                                                                                                            ?></span>
                                                                                                                        <span class="columntools">
                                                                                                                            <a href="#columnsettingbox" rel="popover" data-placement="bottom" title="<?php echo JText::_('COM_TZ_PORTFOLIO_COLUMN_SETTINGS');?>" class="fa fa-cog rowcolumnspop"></a>
                                                                                                                            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fa fa-bars add-rowin-column"></a>
                                                                                                                            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE_COLUMN');?>" class="fa fa-times columndelete"></a>
                                                                                                                            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_MOVE_COLUMN');?>" class="fa fa-arrows columnmove"></a>
                                                                                                                        </span>

                            <input type="hidden" class="widthinput-xs" name="" value="<?php echo $this -> get_value($children,"col-xs") ?>">
                            <input type="hidden" class="widthinput-sm" name="" value="<?php echo $this -> get_value($children,"col-sm") ?>">
                            <input type="hidden" class="widthinput-md" name="" value="<?php echo $this -> get_value($children,"col-md") ?>">
                            <input type="hidden" class="widthinput-lg" name="" value="<?php echo $this -> get_value($children,"col-lg") ?>">
                            <input type="hidden" class="offsetinput-xs" name="" value="<?php echo $this -> get_value($children,"col-xs-offset") ?>">
                            <input type="hidden" class="offsetinput-sm" name="" value="<?php echo $this -> get_value($children,"col-sm-offset") ?>">
                            <input type="hidden" class="offsetinput-md" name="" value="<?php echo $this -> get_value($children,"col-md-offset") ?>">
                            <input type="hidden" class="offsetinput-lg" name="" value="<?php echo $this -> get_value($children,"col-lg-offset") ?>">
                            <input type="hidden" class="typeinput" name="" value="<?php echo $this -> get_value($children,"type") ?>">
                            <input type="hidden" class="positioninput" name="" value="<?php echo $this -> get_value($children,"position") ?>">
                            <input type="hidden" class="styleinput" name="" value="<?php echo $this -> get_value($children,"style") ?>">
                            <input type="hidden" class="customclassinput" name="" value="<?php echo $this -> get_value($children,"customclass") ?>">
                            <input type="hidden" class="responsiveclassinput" name="" value="<?php echo $this -> get_value($children,"responsiveclass") ?>">
<!--                            <input type="hidden" class="animationType" name="" value="--><?php //echo $this -> get_value($children,"animationType") ?><!--">-->
<!--                            <input type="hidden" class="animationSpeed" name="" value="--><?php //echo $this -> get_value($children,"animationSpeed") ?><!--">-->
<!--                            <input type="hidden" class="animationDelay" name="" value="--><?php //echo $this -> get_value($children,"animationDelay") ?><!--">-->
<!--                            <input type="hidden" class="animationOffset" name="" value="--><?php //echo $this -> get_value($children,"animationOffset") ?><!--">-->
<!--                            <input type="hidden" class="animationEasing" name="" value="--><?php //echo $this -> get_value($children,"animationEasing") ?><!--">-->
                            <?php
                            if( !empty($children -> children) and is_array($children -> children) ){
                                $this -> childrens   = $children;
                                echo $this -> loadTemplate('generator_children');
                            }
                            ?>
                        </div>



                    <?php

                    } ?>

                </div>
            </div>
        </div>


    <?php

//        echo $this -> loadTemplate('generator_children');
    }
    }
//}
?>