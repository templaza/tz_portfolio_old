<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id:connectionTools.class.php 431 2006-10-17 21:55:46 +0200 (Di, 17 Okt 2006) soeren_nb $
* @package VirtueMart
* @subpackage classes
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

/**
 * Provides general tools to handle connections (http, headers, ... )
 * 
 * @author soeren
 * @since VirtueMart 1.1.0
 */
class tzConnector {

	var $handle = null;

	/**
	 * Clears the output buffer, sends a http status code and a content if given
	 * @static 
	 * @param int $http_status
	 * @param string $mime_type
	 * @param string $content
	 */
	function sendHeaderAndContent( $http_status=200, $content='', $mime_type='text/html' ) {

		// Clear all Joomla header and buffer stuff
		while( @ob_end_clean() );

		$http_status = intval( $http_status );
		@header("HTTP/1.0 $http_status");
		if( $mime_type ) {
			@header( "Content-type: $mime_type; charset=".vmGetCharset() );
		} elseif( $mime_type != '' ) {
			@header( "Content-type: text/html; charset=".vmGetCharset() );
		}
		if( $content ) {
			echo $content;
		}
	}
	/**
	 * This is a general function to safely open a connection to a server,
	 * post data when needed and read the result.
	 * Tries using cURL and switches to fopen/fsockopen if cURL is not available
	 * @since VirtueMart 1.1.0
	 * @static 
	 * @param string $url
	 * @param string $postData
	 * @param array $headers
	 * @param resource $fileToSaveData
	 * @return mixed
	 */
	function handleCommunication( $url, $postData='', $headers=array(), $fileToSaveData=null ) {
		global $vmLogger;

		$urlParts = parse_url( $url );
		if( !isset( $urlParts['port'] )) $urlParts['port'] = 80;
		if( !isset( $urlParts['scheme'] )) $urlParts['scheme'] = 'http';

		if( isset( $urlParts['query'] )) $urlParts['query'] = '?'.$urlParts['query'];
		if( isset( $urlParts['path'] )) $urlParts['path'] = $urlParts['path'].vmGet($urlParts,'query');

		// Check proxy
		if( trim( @VM_PROXY_URL ) != '') {
			if( !stristr(VM_PROXY_URL, 'http')) {
				$proxyURL['host'] = VM_PROXY_URL;
				$proxyURL['scheme'] = 'http';
			} else {
				$proxyURL = parse_url(VM_PROXY_URL);
			}
		}
		else {
			$proxyURL = '';
		}

		if( function_exists( "curl_init" ) && function_exists( 'curl_exec' ) ) {

			$vmLogger->debug( 'Using the cURL library for communicating with '.$urlParts['host'] );

			$CR = curl_init();
			curl_setopt($CR, CURLOPT_URL, $url);

			// just to get sure the script doesn't die
			curl_setopt($CR, CURLOPT_TIMEOUT, 30 );
			if( !empty( $headers )) {
				// Add additional headers if provided
				curl_setopt($CR, CURLOPT_HTTPHEADER, $headers);
			}
			curl_setopt($CR, CURLOPT_FAILONERROR, true);
			if( $postData ) {
				curl_setopt($CR, CURLOPT_POSTFIELDS, $postData );
				curl_setopt($CR, CURLOPT_POST, 1);
			}
			if( is_resource($fileToSaveData)) {
				curl_setopt($CR, CURLOPT_FILE, $fileToSaveData );
			} else {
				curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
			}
			// Do we need to set up the proxy?
			if( !empty($proxyURL) ) {
				$vmLogger->debug( 'Setting up proxy: '.$proxyURL['host'].':'.VM_PROXY_PORT );
				//curl_setopt($CR, CURLOPT_HTTPPROXYTUNNEL, true);
				curl_setopt($CR, CURLOPT_PROXY, $proxyURL['host'] );
				curl_setopt($CR, CURLOPT_PROXYPORT, VM_PROXY_PORT );
				// Check if the proxy needs authentication
				if( trim( @VM_PROXY_USER ) != '') {
					$vmLogger->debug( 'Using proxy authentication!' );
					curl_setopt($CR, CURLOPT_PROXYUSERPWD, VM_PROXY_USER.':'.VM_PROXY_PASS );
				}
			}

			if( $urlParts['scheme'] == 'https') {
				// No PEER certificate validation...as we don't have
				// a certificate file for it to authenticate the host www.ups.com against!
				curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
				//curl_setopt($CR, CURLOPT_SSLCERT , "/usr/locale/xxxx/clientcertificate.pem");
			}
			$result = curl_exec( $CR );
			$error = curl_error( $CR );
			if( !empty( $error ) && stristr( $error, '502') && !empty( $proxyURL )) {
				$vmLogger->debug( 'Switching to NTLM authenticaton.');
				curl_setopt( $CR, CURLOPT_PROXYAUTH, CURLAUTH_NTLM );
				$result = curl_exec( $CR );
				$error = curl_error( $CR );
			}
			curl_close( $CR );

			if( !empty( $error )) {
				$vmLogger->err( $error );
				return false;
			}
			else {
				return $result;
			}
		}
		else {
			if( $postData ) {
				if( !empty( $proxyURL )) {
					// If we have something to post we need to write into a socket
					if( $proxyURL['scheme'] == 'https'){
						$protocol = 'ssl';
					}
					else {
						$protocol = 'http';
					}
					$fp = fsockopen("$protocol://".$proxyURL['host'], VM_PROXY_PORT, $errno, $errstr, $timeout = 30);
				}
				else {
					// If we have something to post we need to write into a socket
					if( $urlParts['scheme'] == 'https'){
						$protocol = 'ssl';
					}
					else {
						$protocol = $urlParts['scheme'];
					}
					$fp = fsockopen("$protocol://".$urlParts['host'], $urlParts['port'], $errno, $errstr, $timeout = 30);
				}
			}
			else {
				if( !empty( $proxyURL )) {
					// Do a read-only fopen transaction
					$fp = fopen( $proxyURL['scheme'].'://'.$proxyURL['host'].':'.VM_PROXY_PORT, 'rb' );
				}
				else {
					// Do a read-only fopen transaction
					$fp = @fopen( $urlParts['scheme'].'://'.$urlParts['host'].':'.$urlParts['port'].$urlParts['path'], 'rb' );
				}
			}
			if(!$fp){
				//error, plesae tell us which one
				$errmsg = "Possible server error!";
				if( !empty($errstr )) {
					$errmsg .= " - $errstr ($errno)\n";
				}
				$vmLogger->err( $errmsg );
				return false;
			}
			else {
				$vmLogger->debug( 'Connection opened to '.$urlParts['host']);
			}
			if( $postData ) {
				$vmLogger->debug('Now posting the variables.' );
				//send the server request
				if( !empty( $proxyURL )) {
					fputs($fp, "POST ".$urlParts['host'].':'.$urlParts['port'].$urlParts['path']." HTTP/1.0\r\n");
					fputs($fp, "Host: ".$proxyURL['host']."\r\n");

					if( trim( @VM_PROXY_USER )!= '') {
						fputs($fp, "Proxy-Authorization: Basic " . base64_encode (VM_PROXY_USER.':'.VM_PROXY_PASS ) . "\r\n\r\n");
					}
				}
				else {
					fputs($fp, 'POST '.$urlParts['path']." HTTP/1.0\r\n");
					fputs($fp, 'Host:'. $urlParts['host']."\r\n");
				}
				fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
				fputs($fp, "Content-length: ".strlen($postData)."\r\n");
				fputs($fp, "Connection: close\r\n\r\n");
				fputs($fp, $postData . "\r\n\r\n");
			}
			else {
				if( !empty( $proxyURL )) {
					fputs($fp, "GET ".$urlParts['host'].':'.$urlParts['port'].$urlParts['path']." HTTP/1.0\r\n");
					fputs($fp, "Host: ".$proxyURL['host']."\r\n");
					if( trim( @VM_PROXY_USER )!= '') {
						fputs($fp, "Proxy-Authorization: Basic " . base64_encode (VM_PROXY_USER.':'.VM_PROXY_PASS ) . "\r\n\r\n");
					}
				}
				else {
					fputs($fp, 'GET '.$urlParts['path']." HTTP/1.0\r\n");
					fputs($fp, 'Host:'. $urlParts['host']."\r\n");
				}
			}
			// Add additional headers if provided
			foreach( $headers as $header ) {
				fputs($fp, $header."\r\n");
			}
			$data = "";
			while (!feof($fp)) {
				$data .= @fgets ($fp, 4096);
			}
			fclose( $fp );

			// If didnt get content-length, something is wrong, return false.
			if ( trim($data) == '' ) {
				$vmLogger->err('An error occured while communicating with the server '.$urlParts['host'].'. It didn\'t reply (correctly). Please try again later, thank you.' );
				return false;
			}
			if(strpos($url, 'zip')) {
			   $result = $data;
			}     
			else { 
			   $result = trim( $data );
			} 
			if( is_resource($fileToSaveData )) {
				fwrite($fileToSaveData, $result );
				return true;
			} else {
				return $result;
			}
		}
	}
	/**
	* Set headers and send the file to the client
	*
	* @author Andreas Gohr <andi@splitbrain.org>
	* @param string The full path to the file
	* @param string The Mime Type of the file
	*/
	function sendFile($file,$mime, $overrideFileName=''){
		// send headers
        
		header("Content-Type: $mime");
		
		list($start,$len) = tzConnector::http_rangeRequest(filesize($file));
		
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Accept-Ranges: bytes');

		//application mime type is downloadable
		if(strtolower(substr($mime,0,11)) == 'application'){
			if( $overrideFileName == '') {
				$filename = basename($file);
			} else {
				$filename = $overrideFileName;
			}
			header('Content-Disposition: attachment; filename="'.$filename.'";');
		}
		
		$chunksize = 1*(1024*1024);
		// send file contents
		$fp = @fopen($file,"rb");
		if($fp){
			fseek($fp,$start); //seek to start of range

			$chunk = ($len > $chunksize) ? $chunksize : $len;
			while (!feof($fp) && $chunk > 0) {
				@set_time_limit(0); // large files can take a lot of time
				print fread($fp, $chunk);
				flush();
				$len -= $chunk;
				$chunk = ($len > $chunksize) ? $chunksize : $len;
			}
			fclose($fp);

		}else{
			header("HTTP/1.0 500 Internal Server Error");
			print "Could not read $file - bad permissions?";
			die();
		}
	}
	/**
	* Checks and sets headers to handle range requets
	*
	* @author  Andreas Gohr <andi@splitbrain.org>
	* @return array The start byte and the amount of bytes to send
	* @param int The file size
	*/
	function http_rangeRequest($size, $exitOnError=true ){
		global $vm_mainframe;
		if(!isset($_SERVER['HTTP_RANGE'])){
			// no range requested - send the whole file
			header("Content-Length: $size");
			return array(0,$size);
		}

		$t = explode('=', $_SERVER['HTTP_RANGE']);
		if (!$t[0]=='bytes') {
			// we only understand byte ranges - send the whole file
			header("Content-Length: $size");
			return array(0,$size);
		}

		$r = explode('-', $t[1]);
		$start = (int)$r[0];
		$end = (int)$r[1];
		if (!$end) $end = $size - 1;
		if ($start > $end || $start > $size || $end > $size){
			if( $exitOnError ) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				print 'Bad Range Request!';
				die();
			} else {
				return array(0,$size);
			}
		}

		$tot = $end - $start + 1;
		header('HTTP/1.1 206 Partial Content');
		header("Content-Range: bytes {$start}-{$end}/{$size}");
		header("Content-Length: $tot");

		return array($start,$tot);
	}
}
?>