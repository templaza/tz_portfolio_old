<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

tzportfolioimport ('lessphp/lessc.inc') ;
tzportfolioimport ('minify/csscompressor');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * TZLess class compile less
 *
 * @package TZ
 */
class TZLess extends lessc
{
    function updateUrl ($css, $src) {
        global $src_url;
        $src_url = $src;
        return preg_replace_callback('/url\(([^\)]*)\)/', array('TZLess', 'replaceurl'), $css);
    }

    public static function replaceurl ($matches) {
        global $src_url;
        $url = str_replace(array('"', '\''), '', $matches[1]);
        $url = Plazart::cleanPath ($src_url.'/'.$url);
        return "url('$url')";
    }

    function compressCss ($src, $dest) {
        if (!is_file ($src)) return false;
        // get css text
        $css_text = JFile::read($src);
        // if this is template.css or template-responsive.css, prepend bootstrap
        //		if (preg_match('#template(-responsive)?\.css#', $src)) {
        //			$bs = preg_replace ('#template(-responsive)?\.css#', 'bootstrap\1.css', $src);
        //			if (is_file ($bs)) {
        //				$css_text = JFile::read($bs) . "\n" . $css_text;
        //			}
        //		}


        $result = Minify_CSS_Compressor::process ($css_text);
        // print to file
        JFile::write ($dest, $result);

        return true;

    }

    function clearfolder($src) {
        $files = JFolder::files ($src,'.',0,1);
        if (count($files)) {
            foreach ($files as $file) {
                JFile::delete($file);
            }
        }
        $folders = JFolder::folders ($src,'.',0,1);
        if (count($folders)) {
            foreach ($folders as $folder) {
                JFolder::delete($folder);
            }
        }
    }

    function clearfile($src) {
        if(JFile::exists($src)){
            JFile::delete($src);
        }
    }

    public static function compileAll ($params=null,$theme = null) {

        if ($params && !$params->get('devmode', 0)) return false;

        $less = new self;
        // compile all css files
        $files = array ();
        $lesspath = 'components'.DIRECTORY_SEPARATOR.COM_TZ_PORTFOLIO.DIRECTORY_SEPARATOR.'less'.DIRECTORY_SEPARATOR;
        $csspath = 'components'.DIRECTORY_SEPARATOR.COM_TZ_PORTFOLIO.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR;

        // delete old css
        $less->clearfolder(JPATH_ROOT.DIRECTORY_SEPARATOR. $csspath);
        $buffer =   '<html><body></body></html>';
        JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'index.html', $buffer);

        // get single files need to compile
        $lessFiles = JFolder::files (JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath, '.less');

        if (!count($lessFiles)) {
            return 1;
        }

        foreach ($lessFiles as $file) {
            // delete old css
//            $less -> clearfile(JPATH_ROOT.DIRECTORY_SEPARATOR. $csspath.$file.'.css');
//            $less -> clearfile(JPATH_ROOT.DIRECTORY_SEPARATOR. $csspath.$file.'.min.css');

            $filecss    =   substr($file, 0, -5);
            $less->ccompile(JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath.$file,JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.$filecss.'.css');
            $src = JPATH_ROOT.DIRECTORY_SEPARATOR. $csspath.$filecss.'.css';
            $desc = JPATH_ROOT.DIRECTORY_SEPARATOR. $csspath.$filecss.'.min.css';
            $result = $less->compressCss ($src, $desc);
        }
        return 2;

        // get themes
//        $themes = JFolder::folders (JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath.DIRECTORY_SEPARATOR.'themes');
//        if (!count($themes)) return false;
//        foreach ($themes as $t) {
//            $buffer =   '<html><body></body></html>';
//            JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'themes'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.'index.html', $buffer);
//            // compile
//            $files  =   JFolder::files (JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$t, '.less');
//            if (!count($files)) continue;
//            foreach ($files as $file) {
//                $filecss    =   substr($file, 0, -5);
//                $less->ccompile (JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath.'themes'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.$file, JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'themes'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.$filecss.'.css');
//                $src = JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'themes'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.$filecss.'.css';
//                $desc = JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'themes'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.$filecss.'.min.css';
//                $result = $less->compressCss ($src, $desc);
//            }
//        }

        //compile bootstrap
//        $lesspath = 'templates'.DIRECTORY_SEPARATOR.TZ_TEMPLATE.DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'less'.DIRECTORY_SEPARATOR;
//        $csspath = 'templates'.DIRECTORY_SEPARATOR.TZ_TEMPLATE.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR;
//        $result =   $less->ccompile(JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath.'bootstrap.less',JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'bootstrap.css');
//        $src = JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'bootstrap.css';
//        $desc = JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'bootstrap.min.css';
//        $result = $less->compressCss ($src, $desc);
//        $result =   $less->ccompile(JPATH_ROOT.DIRECTORY_SEPARATOR.$lesspath.'responsive.less',JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'bootstrap-responsive.css');
//        $src = JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'bootstrap-responsive.css';
//        $desc = JPATH_ROOT.DIRECTORY_SEPARATOR.$csspath.'bootstrap-responsive.min.css';
//        $result = $less->compressCss ($src, $desc);
    }
}
