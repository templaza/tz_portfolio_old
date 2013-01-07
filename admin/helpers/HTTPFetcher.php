<?php
/*------------------------------------------------------------------------

# HTTPFetcher.php - E-Content Component

# ------------------------------------------------------------------------

# author    E-Content Site

# copyright Copyright (C) 2010 econtentsite.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.econtentsite.com

# Technical Support:  Forum - http://forum.econtentsite.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Services_Yadis_HTTPResponse {
    function Services_Yadis_HTTPResponse($final_url = null, $status = null,
                                         $headers = null, $body = null)
    {
        $this->final_url = $final_url;
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }
}

/**
 * This class is the interface for HTTP fetchers the Yadis library
 * uses.  This interface is only important if you need to write a new
 * fetcher for some reason.
 *
 * @access private
 * @package Yadis
 */
class Services_Yadis_HTTPFetcher {

    var $timeout = 20; // timeout in seconds.

    /**
     * Return whether a URL should be allowed. Override this method to
     * conform to your local policy.
     *
     * By default, will attempt to fetch any http or https URL.
     */
    function allowedURL($url)
    {
        return $this->URLHasAllowedScheme($url);
    }

    /**
     * Is this an http or https URL?
     *
     * @access private
     */
    function URLHasAllowedScheme($url)
    {
        return (bool)preg_match('/^https?:\/\//i', $url);
    }

    /**
     * @access private
     */
    function _findRedirect($headers)
    {
        foreach ($headers as $line) {
            if (strpos($line, "Location: ") === 0) {
                $parts = explode(" ", $line, 2);
                return $parts[1];
            }
        }
        return null;
    }

    /**
     * Fetches the specified URL using optional extra headers and
     * returns the server's response.
     *
     * @param string $url The URL to be fetched.
     * @param array $extra_headers An array of header strings
     * (e.g. "Accept: text/html").
     * @return mixed $result An array of ($code, $url, $headers,
     * $body) if the URL could be fetched; null if the URL does not
     * pass the URLHasAllowedScheme check or if the server's response
     * is malformed.
     */
    function get($url, $headers)
    {
        trigger_error("not implemented", E_USER_ERROR);
    }
}

?>
