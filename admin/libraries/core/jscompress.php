<?php

// No direct access
defined('_JEXEC') or die();
tzportfolioimport ('minify/JavaScriptPacker');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class TZJScompress {
    public static function compressAll ($params = null, $theme = null) {

        if ($params && !$params->get('devmode', 0)) return false;

        $jspath = 'components'.DIRECTORY_SEPARATOR.COM_TZ_PORTFOLIO.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR;
        // get single files need to compress
        $jsFiles = JFolder::files (JPATH_ROOT.DIRECTORY_SEPARATOR.$jspath, '.js');
        if (!count($jsFiles)) {
            return 1;
        }
        if (JFolder::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$jspath.'packed')) JFolder::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.$jspath.'packed');

        $buffer =   '<html><body></body></html>';
        JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.$jspath.'packed'.DIRECTORY_SEPARATOR.'index.html', $buffer);

        foreach ($jsFiles as $file) {
            $filejs    =   substr($file, 0, -3);
            $script     =   JFile::read(JPATH_ROOT.DIRECTORY_SEPARATOR.$jspath.$file);
            $packer = new JavaScriptPacker($script, 'Normal', true, false);
            $packed = $packer->pack();
            $result =   JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.$jspath.'packed'.DIRECTORY_SEPARATOR.$filejs.'.min.js',$packed);
        }
        return 2;
    }
}