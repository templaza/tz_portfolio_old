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

// No direct access.
defined('_JEXEC') or die;

//Show plugin content tabs
if($pluginsTab = $this -> pluginsTab):
    $i  = 0;
    $p  = 0;
    //Get plugin is enabled in group tz_portfolio
     foreach($pluginsTab as $name => $pFields):
    ?>
    <div class="tab-pane" id="tztabsplugins<?php echo $name;?>">
        <?php echo JHtml::_('bootstrap.startAccordion', 'tzPluginMenuOptions'.$i, array('active' => 'tzplugincollapse'.$p));?>
            <?php
            //Get Fieldsets of this plugin
            if($pFieldSets   = $pFields -> getFieldsets()):
                foreach($pFieldSets as $name => $pFieldSet):
                    if(!$fieldsetLabel = $pFieldSet -> label):
                        $fieldsetLabel  = JText::_('COM_PLUGINS_'.strtoupper($pFieldSet -> name).'_FIELDSET_LABEL');
                    endif;
            ?>
                    <?php echo JHtml::_('bootstrap.addSlide', 'tzPluginMenuOptions'.$i
                        ,$fieldsetLabel, 'tzplugincollapse'.$p);
                    ?>
                        <?php
                        //Get Fields of this plugin
                        foreach ($pFields->getFieldset($name) as $field):
                        ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php echo JHtml::_('bootstrap.endSlide');?>

                    <?php $p++;?>
                <?php endforeach;?>
            <?php endif;?>

        <?php echo JHtml::_('bootstrap.endAccordion');?>
    </div>
    <?php $i ++;?>
    <?php endforeach;?>
<?php endif;?>