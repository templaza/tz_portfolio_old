<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SunlandNo1
 * Date: 6/28/12
 * Time: 9:06 AM
 * To change this template use File | Settings | File Templates.
 */
$fieldsId   = $this -> fieldsId;
?>
<ul class="adminformlist">
    <li>
        <label title="<?php echo JText::_('COM_TZ_PORTFOLIO_TZ_FIELDS_DESC');?>" class="hasTip"
               for="jform_params_tz_fieldsid" id="jform_params_tz_fieldsid-lbl">
            <?php echo JText::_('COM_TZ_PORTFOLIO_TZ_FIELDS_CHOOSE')?>
        </label>
        <select id="jform_params_tz_fieldsid" multiple="multiple"
                name="jform[params][tz_fieldsid][]" class="" aria-invalid="false"
                style="min-width: 130px; min-height: 80px;">
            <option value=""<?php if(in_array(-1,$fieldsId)) echo ' selected="selected"';?>><?php echo JText::_('COM_TZ_PORTFOLIO_ALL_FIELDS');?></option>
            <?php if($this -> listFields):?>
                <?php foreach($this -> listFields as $item):?>
                    <option value="<?php echo $item -> id;?>"<?php if(in_array($item -> id,$fieldsId)) echo ' selected="selected"';?>><?php echo $item -> title;?></option>
                <?php endforeach;?>
            <?php endif;?>
        </select>
    </li>
</ul>