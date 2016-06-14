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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class TZ_PortfolioViewLegacy extends JViewLegacy{
    protected $generateLayout   = null;

    public function generateLayout(&$article,&$params,$dispatcher,$csscomporess=null){
        if($template   = TZ_PortfolioTemplate::getTemplate(true)){
            $tplparams  = $template -> params;
            if($tplparams -> get('use_single_layout_builder',1)){
                $this -> _generateLayout($article,$params,$dispatcher,$csscomporess);
                return $this -> generateLayout;
            }
        }
        return false;
    }

    protected function _generateLayout(&$article,&$params,$dispatcher,$csscompress=null){

        JPluginHelper::importPlugin('content');
        if($template   = TZ_PortfolioTemplate::getTemplate(true)){
            $theme  = $template;
            $html   = null;

            if($theme){
                if($tplParams  = $theme -> layout){
                    $this -> document -> addStyleSheet('components/com_tz_portfolio/css/tz.bootstrap.min.css');
                    foreach($tplParams as $tplItems){
                        $rows   = null;

                        $background = null;
                        $color      = null;
                        $margin     = null;
                        $padding    = null;

                        if($tplItems -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> backgroundcolor))){
                            $background  = 'background: '.$tplItems -> backgroundcolor.';';
                        }
                        if($tplItems -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> textcolor))){
                            $color      =  'color: '.$tplItems -> textcolor.';';
                        }
                        if(isset($tplItems -> margin) && !empty($tplItems -> margin)){
                            $margin = 'margin: '.$tplItems -> margin.';';
                        }
                        if(isset($tplItems -> padding) && !empty($tplItems -> padding)){
                            $padding = 'padding: '.$tplItems -> padding.';';
                        }
                        if($background || $color || $margin || $padding){
                            $this -> document -> addStyleDeclaration('
                            #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'{
                                '.$background.$color.$margin.$padding.'
                            }
                        ');
                        }
                        if($tplItems -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkcolor))){
                            $this -> document -> addStyleDeclaration('
                                #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a{
                                    color: '.$tplItems -> linkcolor.';
                                }
                            ');
                        }
                        if($tplItems -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkhovercolor))){
                            $this -> document -> addStyleDeclaration('
                                #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a:hover{
                                    color: '.$tplItems -> linkhovercolor.';
                                }
                            ');
                        }
                        $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'"'
                            .' class="tz-container-fluid'.($tplItems -> {"class"}?' '.$tplItems -> {"class"}:'')
                            .($tplItems -> responsive?' '.$tplItems -> responsive:'').'">';
                        if(isset($tplItems -> containertype) && $tplItems -> containertype){
                            $rows[] = '<div class="'.$tplItems -> containertype.'">';
                        }

                        $rows[] = '<div class="tz-row">';
                        foreach($tplItems -> children as $children){
                            $html   = null;

                            if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                $rows[] = '<div class="'
                                    .(!empty($children -> {"col-lg"})?'tz-col-lg-'.$children -> {"col-lg"}:'')
                                    .(!empty($children -> {"col-md"})?' tz-col-md-'.$children -> {"col-md"}:'')
                                    .(!empty($children -> {"col-sm"})?' tz-col-sm-'.$children -> {"col-sm"}:'')
                                    .(!empty($children -> {"col-xs"})?' tz-col-xs-'.$children -> {"col-xs"}:'')
                                    .(!empty($children -> {"col-lg-offset"})?' tz-col-lg-offset-'.$children -> {"col-lg-offset"}:'')
                                    .(!empty($children -> {"col-md-offset"})?' tz-col-md-offset-'.$children -> {"col-md-offset"}:'')
                                    .(!empty($children -> {"col-sm-offset"})?' tz-col-sm-offset-'.$children -> {"col-sm-offset"}:'')
                                    .(!empty($children -> {"col-xs-offset"})?' tz-col-xs-offset-'.$children -> {"col-xs-offset"}:'')
                                    .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                    .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                            }

                            if($children -> type && $children -> type !='none'){
                                $html   = $this -> loadTemplate($children -> type);
                                $html   = trim($html);
                            }

                            $rows[] = $html;

                            if( !empty($children -> children) and is_array($children -> children) ){
                                $this -> _childrenLayout($rows,$children,$article,$params,$dispatcher);
                            }

                            if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                $rows[] = '</div>'; // Close col tag
                            }
                        }

                        if(isset($tplItems -> containertype) && $tplItems -> containertype){
                            $rows[] = '</div>';
                        }
                        $rows[] = '</div>';
                        $rows[] = '</div>';
                        $this -> generateLayout .= implode("\n",$rows);
                    }
                }
            }
        }
    }

    protected function _childrenLayout(&$rows,$children,&$article,&$params,$dispatcher){
        foreach($children -> children as $children){
            $background = null;
            $color      = null;
            $margin     = null;
            $padding    = null;

            if($children -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> backgroundcolor))){
                $background  = 'background: '.$children -> backgroundcolor.';';
            }
            if($children -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> textcolor))){
                $color      =  'color: '.$children -> textcolor.';';
            }
            if(isset($children -> margin) && !empty($children -> margin)){
                $margin = 'margin: '.$children -> margin.';';
            }
            if(isset($children -> padding) && !empty($children -> padding)){
                $padding = 'padding: '.$children -> padding.';';
            }
            if($background || $color){
                $this -> document -> addStyleDeclaration('
                    #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner{
                        '.$background.$color.$margin.$padding.'
                    }
                ');
            }
            if($children -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkcolor))){
                $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a{
                            color: '.$children -> linkcolor.';
                        }
                    ');
            }
            if($children -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkhovercolor))){
                $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a:hover{
                            color: '.$children -> linkhovercolor.';
                        }
                    ');
            }
            $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner" class="tz-container-fluid '
                .$children -> {"class"}.($children -> responsive?' '.$children -> responsive:'').'">';
            $rows[] = '<div class="tz-row">';
            foreach($children -> children as $children){
                $html   = null;

                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                    $rows[] = '<div class="'
                        .(!empty($children -> {"col-lg"})?'tz-col-lg-'.$children -> {"col-lg"}:'')
                        .(!empty($children -> {"col-md"})?' tz-col-md-'.$children -> {"col-md"}:'')
                        .(!empty($children -> {"col-sm"})?' tz-col-sm-'.$children -> {"col-sm"}:'')
                        .(!empty($children -> {"col-xs"})?' tz-col-xs-'.$children -> {"col-xs"}:'')
                        .(!empty($children -> {"col-lg-offset"})?' tz-col-lg-offset-'.$children -> {"col-lg-offset"}:'')
                        .(!empty($children -> {"col-md-offset"})?' tz-col-md-offset-'.$children -> {"col-md-offset"}:'')
                        .(!empty($children -> {"col-sm-offset"})?' tz-col-sm-offset-'.$children -> {"col-sm-offset"}:'')
                        .(!empty($children -> {"col-xs-offset"})?' tz-col-xs-offset-'.$children -> {"col-xs-offset"}:'')
                        .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                        .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                }

                if($children -> type && $children -> type !='none'){
                    $html   = $this -> loadTemplate($children -> type);
                    $html   = trim($html);
                }
                $rows[] = $html;

                if( !empty($children -> children) and is_array($children -> children) ){
                    $this -> _childrenLayout($rows,$children,$article,$params,$dispatcher);
                }

                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                    $rows[] = '</div>'; // Close col tag
                }

            }
            $rows[] = '</div>';
            $rows[] = '</div>';
        }
        return;
    }
}