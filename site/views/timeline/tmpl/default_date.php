<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SunlandNo1
 * Date: 6/18/12
 * Time: 9:09 AM
 * To change this template use File | Settings | File Templates.
 */
defined('_JEXEC') or die();

//if($this -> params -> get('tz_timeline_layout') == 'ajaxInfiScroll'):
//    $listDates   = $this -> listsDate;
//elseif($this -> params -> get('tz_timeline_layout') == 'default'):
    $list   = $this -> listsArticle;
//endif;

//if($this -> params -> get('tz_timeline_layout') == 'ajaxInfiScroll'):
//    if($listDates):
?>
<!--        --><?php //foreach($listDates as $row):?>
<!--            --><?php
//                $strMonth   = date('M',strtotime($row -> created));
//                $strMonth   = strtolower($strMonth);
//            ?>
<!--            <a href="#--><?php //echo $strMonth?><!----><?php //echo $row -> year;?><!--">-->
<!--               --><?php //echo $strMonth;?>
<!--            </a>-->
<!--        --><?php //endforeach;?>
<!--    --><?php //endif;?>
<?php //elseif($this -> params -> get('tz_timeline_layout') == 'default'):?>
    <?php $i=0;?>
    <?php foreach($list as $row):?>
        <?php
            $strMonth   = date('M',strtotime($row -> created));
            $strMonth   = strtolower($strMonth);
        ?>
        <?php if($i == 0):?>
            <a href="#<?php echo $strMonth?><?php echo $row -> year;?>">
               <?php echo JText::_($strMonth);?>
            </a>
        <?php endif;?>
        <?php
            if($i != 0 && $list[$i-1] -> tz_date != $list[$i] -> tz_date):
        ?>
           <a href="#<?php echo $strMonth?><?php echo $row -> year;?>">
               <?php echo JText::_($strMonth);?>
           </a>
        <?php endif;?>
        <?php $i++;?>
    <?php endforeach;?>
<?php //endif;?>