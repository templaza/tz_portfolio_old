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

?>
<div class="generator">
<?php
if($this -> tzlayout){
    //print_r($layout); die;
    foreach($this -> tzlayout as $items )
    {
        $containerType  = '';
        if(isset($items -> containertype)){
            $containerType  = $items -> containertype;
        }
        $parentId   = uniqid(rand());
        $id         = uniqid(rand());
        ?>
        <!-- Main Rows -->
        <div class="row-fluid layoutmainrow">
        <div class="span12">

        <div class="rowpropperties pull-left">
            <span class="rowname"><?php echo $items -> name; ?></span>
            <span class="rowdocs">
                <input type="hidden" class="rownameinput" name="" value="<?php echo $this -> get_value($items,"name"); ?>">
                <input type="hidden" class="rowcustomclassinput" name="" value="<?php echo $this -> get_value($items,"class") ?>">
                <input type="hidden" class="rowresponsiveinput" name="" value="<?php echo $this -> get_value($items,"responsive") ?>">

                <input type="hidden" class="rowbackgroundcolorinput" name="" value="<?php echo $this -> get_color($items,'backgroundcolor') ?>">
                <input type="hidden" class="rowtextcolorinput" name="" value="<?php echo $this -> get_color($items,'textcolor') ?>">
                <input type="hidden" class="rowlinkcolorinput" name="" value="<?php echo $this -> get_color($items,'linkcolor') ?>">
                <input type="hidden" class="rowlinkhovercolorinput" name="" value="<?php echo $this -> get_color($items,'linkhovercolor') ?>">
                <input type="hidden" class="rowmargininput" name="" value="<?php echo $this -> get_value($items,'margin') ?>">
                <input type="hidden" class="rowpaddinginput" name="" value="<?php echo $this -> get_value($items,'padding') ?>">
            </span>
        </div>
        <div id="<?php echo $parentId?>" class="pull-right row-tools row-container">
            <select class="containertype" name="" aria-invalid="false">
                <option<?php echo ($containerType == '')?' selected=""':''?> value=""><?php echo JText::_('JNONE');?></option>
                <option<?php echo ($containerType == 'container')?' selected=""':''?> value="container"><?php echo JText::_('COM_TZ_PORTFOLIO_FIXED_WIDTH');?></option>
                <option<?php echo ($containerType == 'container-fluid')?' selected=""':''?> value="container-fluid"><?php echo JText::_('COM_TZ_PORTFOLIO_FULL_WIDTH');?></option>
            </select>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_MOVE_THIS_ROW');?>" class="fa fa-arrows rowmove"></a>
            <a href="javascript:" class="accordion-toggle"
               title="<?php echo JText::_('COM_TZ_PORTFOLIO_TOGGLE_THIS_ROW');?>"
               data-toggle="collapse" data-parent="#<?php echo $parentId;?>"
               data-target="#<?php echo $id;?>">
                <span class="fa fa-chevron-up"></span><span class="fa fa-chevron-down"></span>
            </a>
            <a href="#rowsettingbox" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ROW_SETTINGS');?>" class="fa fa-cog rowsetting" rel="rowpopover"></a>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fa fa-bars add-row"></a>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_COLUMN');?>" class="fa fa-columns add-column"></a>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_ROW');?>" class="fa fa-times rowdelete"></a>
        </div>

        <div class="hr clr"></div>

        <div id="<?php echo $id;?>" class="row-fluid show-grid collapse in">

        <!-- Columns -->
        <?php
        foreach( $items -> children as $item )
        {
            ?>
            <div class="<?php echo ($this -> get_value($item,"type")=='component' or $this -> get_value($item,"type")=='message') ? 'type-'.$this -> get_value($item,"type"):'' ?>  span<?php echo $this -> get_value($item,"col-lg"); ?> column <?php echo ( empty($item->{"col-lg-offset"})?'':'offset'.$item ->{"col-lg-offset"} )?>">

                                    <span class="position-name"><?php
                                        echo $this -> get_value($item,"type");
                                        //                                        if($this -> get_value($item,"type")=='component' || $this -> get_value($item,"type")=='message' || $this -> get_value($item,"type")=='megamenu' || $this -> get_value($item,"type")=='logo'){ echo strtoupper($this -> get_value($item,"type"));}
                                        //                                        elseif(empty($item -> position)) echo '(none)';
                                        //                                        else echo $this -> get_value($item,"position");

                                        ?></span>
                                    <div class="columntools">
                                        <a href="#columnsettingbox" rel="popover" data-placement="bottom"
                                           title="<?php echo JText::_('COM_TZ_PORTFOLIO_COLUMN_SETTINGS');?>" class="fa fa-cog rowcolumnspop"></a>
                                        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fa fa-bars add-rowin-column"></a>
                                        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE_COLUMN');?>" class="fa fa-times columndelete"></a>
                                        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_MOVE_COLUMN');?>" class="fa fa-arrows columnmove"></a>
                                    </div>

                                    <input type="hidden" class="widthinput-xs" name="" value="<?php echo $this -> get_value($item,"col-xs") ?>">
                                    <input type="hidden" class="widthinput-sm" name="" value="<?php echo $this -> get_value($item,"col-sm") ?>">
                                    <input type="hidden" class="widthinput-md" name="" value="<?php echo $this -> get_value($item,"col-md") ?>">
                                    <input type="hidden" class="widthinput-lg" name="" value="<?php echo $this -> get_value($item,"col-lg") ?>">
                                    <input type="hidden" class="offsetinput-xs" name="" value="<?php echo $this -> get_value($item,"col-xs-offset") ?>">
                                    <input type="hidden" class="offsetinput-sm" name="" value="<?php echo $this -> get_value($item,"col-sm-offset") ?>">
                                    <input type="hidden" class="offsetinput-md" name="" value="<?php echo $this -> get_value($item,"col-md-offset") ?>">
                                    <input type="hidden" class="offsetinput-lg" name="" value="<?php echo $this -> get_value($item,"col-lg-offset") ?>">
                                    <input type="hidden" class="typeinput" name="" value="<?php echo $this -> get_value($item,"type") ?>">
<!--                                    <input type="hidden" class="positioninput" name="" value="--><?php //echo $this -> get_value($item,"position") ?><!--">-->
<!--                                    <input type="hidden" class="styleinput" name="" value="--><?php //echo $this -> get_value($item,"style") ?><!--">-->
                                    <input type="hidden" class="customclassinput" name="" value="<?php echo $this -> get_value($item,"customclass") ?>">
                                    <input type="hidden" class="responsiveclassinput" name="" value="<?php echo $this -> get_value($item,"responsiveclass") ?>">
<!--                                    <input type="hidden" class="animationType" name="" value="--><?php //echo $this -> get_value($item,"animationType") ?><!--">-->
<!--                                    <input type="hidden" class="animationSpeed" name="" value="--><?php //echo $this -> get_value($item,"animationSpeed") ?><!--">-->
<!--                                    <input type="hidden" class="animationDelay" name="" value="--><?php //echo $this -> get_value($item,"animationDelay") ?><!--">-->
<!--                                    <input type="hidden" class="animationOffset" name="" value="--><?php //echo $this -> get_value($item,"animationOffset") ?><!--">-->
<!--                                    <input type="hidden" class="animationEasing" name="" value="--><?php //echo $this -> get_value($item,"animationEasing") ?><!--">-->
                                    <!-- Row in Columns -->
                                    <?php
                                    if( !empty($item -> children) and is_array($item -> children) )
                                    {
                                        foreach( $item -> children as $children )
                                        {
                                            $parentId2  = uniqid(rand());
                                            $id2        = uniqid(rand());
                                            ?>
                                            <div class="row-fluid child-row">
                                            <div class="span12">

                                            <div class="rowpropperties pull-left">
                                                <span class="rowname"><?php echo $children -> name ?></span>
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

                                            <div id="<?php echo $parentId2;?>" class="pull-right row-tools">
                                                <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_MOVE_THIS_ROW');?>" class="fa fa-arrows row-move-in-column"></a>
                                                <a href="javascript:" class="accordion-toggle"
                                                   data-toggle="collapse" data-parent="#<?php echo $parentId2;?>"
                                                   data-target="#<?php echo $id2;?>">
                                                    <span class="fa fa-chevron-up"></span><span class="fa fa-chevron-down"></span>
                                                </a>
                                                <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fa fa-bars add-row"></a>
                                                <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_COLUMN');?>" class="fa fa-columns add-column"></a>
                                                <a href="#rowsettingbox" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ROW_SETTINGS');?>" class="fa fa-cog rowsetting" rel="rowpopover"></a>
                                                <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_ROW');?>" class="fa fa-times rowdelete"></a>
                                            </div>

                                            <div class="clearfix"></div>

                                            <div id="<?php echo $id2;?>" class="row-fluid show-grid collapse in">

                                            <?php
                                            foreach($children -> children as $children)
                                            {
                                                ?>

                                                <div class="<?php echo ($this -> get_value($children,"type")=='component' or $this -> get_value($children,"type")=='message') ? 'type-'.$this -> get_value($children,"type"):'' ?>  span<?php echo $this -> get_value($children,"col-lg"); ?> column <?php echo ( empty($children->{"col-lg-offset"})?'':'offset'.$children->{"col-lg-offset"} )?>">

                                                                <span class="position-name"><?php

                                                                    echo $children -> type;

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
<!--                                                                <input type="hidden" class="animationType" name="" value="--><?php //echo $this -> get_value($children,"animationType") ?><!--">-->
<!--                                                                <input type="hidden" class="animationSpeed" name="" value="--><?php //echo $this -> get_value($children,"animationSpeed") ?><!--">-->
<!--                                                                <input type="hidden" class="animationDelay" name="" value="--><?php //echo $this -> get_value($children,"animationDelay") ?><!--">-->
<!--                                                                <input type="hidden" class="animationOffset" name="" value="--><?php //echo $this -> get_value($children,"animationOffset") ?><!--">-->
<!--                                                                <input type="hidden" class="animationEasing" name="" value="--><?php //echo $this -> get_value($children,"animationEasing") ?><!--">-->

                                                                <!--3-->



                                                                <?php

                                                                $this -> childrens   = $children;
                                                                if( !empty($children -> children) and is_array($children -> children) )
                                                                {
                                                                    echo $this -> loadTemplate('generator_children');
                                                                }
                                                                ?>
                                                                <!--3-->
                                                </div>
                                            <?php
                                            } ?>

                                            </div>
                                            </div>
                                            </div>
                                        <?php
                                        }
                                    }
                                    ?>
                                    <!--  End Row in Columns -->
            </div>

        <?php
        }
        ?>
        <!-- Columns -->

        </div>

        </div>
        </div>
        <!-- End Main Rows -->
    <?php
    }
}
?>

</div>