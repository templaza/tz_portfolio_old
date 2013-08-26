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

class AutoCutText{
	/**
	 * String data input
	 *
	 * @var string HTML
	 */
	var $_data 			= null;
	
	/**
	 * Number introtext will be cut
	 *
	 * @var int
	 */
	var $_intronumber	=	0;
	
	/**
	 * String of introtext
	 *
	 * @var String HTML
	 */
	var $_subtext		=	null;
	
	/**
	 * String of fulltext
	 *
	 * @var String HTML
	 */
	var $_remaintext	=	null;
	
	/**
	 * Array Code
	 *
	 * @var Array
	 */
	var $_arr_code		=	array();
	
	/**
	 * Array Matches
	 *
	 * @var Array
	 */
	var $_arr_match		=	array();
	
	/**
	 * Data after encode
	 *
	 * @var String
	 */
	var $_data_code		=	null;
	
	/**
	 * Number encode
	 *
	 * @var Int
	 */
	var $_numbercode	=	0;
	
	
	/**
	 * Constructor
	 *
	 * @param String $data
	 * @param Int $number
	 */
	function AutoCutText($data, $number){
		$data	=	preg_replace('/&nbsp;/', ' ', $data );
		$data	=	preg_replace('/&gt;/', '>', $data );
		$data	=	preg_replace('/&lt;/', '<', $data );
		$data 	= 	preg_replace('/\r?\n/', 'YOSNEWLINE', $data);
//		$data	=	preg_replace('/\s+/', ' ', $data );

		
		if (preg_match('/(?!\s)<p(.*?)>/', $data, $match )) {
			$data	=	str_replace($match[0], ' <p'.$match[1].'>', $data );
		}		
		$data	=	preg_replace('/(?!\s)<br\s*\/>/', ' <br />', $data );
		if (preg_match('/(?!\s)<div(.*?)>/', $data, $match )) {
			$data	=	str_replace($match[0], ' <div'.$match[1].'>', $data );
		}
		$this->_data		=	$data;
		$this->_intronumber	=	$number;
		
		$this->encode();
		$this->wordSub();		
		$this->decode();		
		$this->makesafehtml();
	}
	
	/**
	 * Encode data
	 *
	 */
	function encode(){
		$this->_data_code	=	$this->_data;

		if (preg_match_all('/<table.*?>.*?<\/table>/si', $this->_data_code, $matches)) {
			for ($i = 0; $i<count($matches[0]); $i++){
				$this->_numbercode++;
				array_push($this->_arr_code, 'code_'.$this->_numbercode.'_code');
				array_push($this->_arr_match, $matches[0][$i]);
				$this->_data_code = str_replace($matches[0][$i], $this->_arr_code[$this->_numbercode-1], $this->_data_code);
			}
		}
		
		if (preg_match_all('/<select.*?>.*?<\/select>/si',$this->_data_code, $matches)){
			for ($i =0; $i<count($matches[0]); $i++){
				$this->_numbercode++;
				array_push($this->_arr_code, 'code_'.$this->_numbercode.'_code');
				array_push($this->_arr_match, $matches[0][$i]);
				$this->_data_code = str_replace($matches[0][$i], $this->_arr_code[$this->_numbercode-1], $this->_data_code);
			}
		}
		
		if (preg_match_all('/<script.*?>.*?<\/script>/si', $this->_data_code, $matches)) {
			for ($i = 0; $i<count($matches[0]); $i++){
				$this->_numbercode++;
				array_push($this->_arr_code, 'code_'.$this->_numbercode.'_code');
				array_push($this->_arr_match, $matches[0][$i]);
				$this->_data_code = str_replace($matches[0][$i], $this->_arr_code[$this->_numbercode-1], $this->_data_code);
			}
		}
		
		if (preg_match_all('/<\!--.*?-->/si', $this->_data_code, $matches)) {
			for ($i = 0; $i<count($matches[0]); $i++){
				$this->_numbercode++;
				array_push($this->_arr_code, 'code_'.$this->_numbercode.'_code');
				array_push($this->_arr_match, $matches[0][$i]);
				$this->_data_code = str_replace($matches[0][$i], $this->_arr_code[$this->_numbercode-1], $this->_data_code);
			}
		}
		
		if (preg_match_all('/(&lt;|<)a\s+.*?href="(.*?)"(&gt;|>)(.*?)(&lt;|<)\/a(&gt;|>)/is', $this->_data_code, $matches)) {
			for ($i = 0; $i<count($matches[0]); $i++){
				$this->_numbercode++;
				array_push($this->_arr_code, 'code_'.$this->_numbercode.'_code');
				array_push($this->_arr_match, $matches[0][$i]);
				$this->_data_code = str_replace($matches[0][$i], $this->_arr_code[$this->_numbercode-1], $this->_data_code);
			}
		}
		
		if (preg_match_all('/<.*?>/si', $this->_data_code, $matches)) {
			for ($i = 0; $i<count($matches[0]); $i++){
				$this->_numbercode++;
				array_push($this->_arr_code, 'code_'.$this->_numbercode.'_code');
				array_push($this->_arr_match, $matches[0][$i]);
				$this->_data_code = str_replace($matches[0][$i], $this->_arr_code[$this->_numbercode-1], $this->_data_code);
			}
		}
		
	}
	
	/**
	 * Cut word
	 *
	 */
	function wordSub() {
		$str_in	=	$this->_data_code;
		$numberWord	=	$this->_intronumber;
		$arr_text = preg_split('/ /',trim($str_in));
		$subText	=	array();
		$fullText	=	array();
		$count		=	0;

		$flag = 0;
		foreach ($arr_text as $text) {
			if ($flag == 0) {
				if ($count > $numberWord) {
					//find the next ending sentence
					// , | . | ;
					if (preg_match('/\.|;/', $text)) {
						$flag = 1;
					}					
					//if text contains code, analyze code, find <br />, <p>, <div>
					if (preg_match('/code_(\d+)_code/', $text, $match)) {
						$codeMatch = $match[1];
						if (preg_match('/(<\/p>)|(<br\s*\/>)|(<\/div>)/', $this->_arr_match[$codeMatch-1])) {
							$flag = 1;
						}
					}
				}
				
				if (!in_array($text, $this->_arr_code)) {
					$count++;
					array_push($subText, $text);
				} else {
					array_push($subText, $text);
				}
				
			} else {
				array_push($fullText, $text);
			}
		}
		$text = implode(' ', $subText);
		$text = str_replace('YOSNEWLINE', "\n", $text);

		$this->_subtext	=	$text;
		
		$text = implode(' ', $fullText);
		$text = str_replace('YOSNEWLINE', "\n", $text);
		$this->_remaintext = $text;
	}
	
	/**
	 * Decode Data
	 *
	 */
	function decode(){
		
		for ($i = 0; $i< $this->_numbercode; $i++){
			
			$this->_subtext	=	str_replace($this->_arr_code[$i],$this->_arr_match[$i],$this->_subtext);
			$this->_subtext = 	str_replace('YOSNEWLINE', "\n", $this->_subtext);
			
			$this->_remaintext=	str_replace($this->_arr_code[$i],$this->_arr_match[$i],$this->_remaintext);
			$this->_remaintext = 	str_replace('YOSNEWLINE', "\n", $this->_remaintext);
		}
		
	}
	
	function makesafehtml(){
		$subtext	=	$this->_subtext;
		$subtext	=	preg_replace('/<table.*?>.*?<\/table>/si','', $subtext );
		$subtext	=	preg_replace('/<select.*?>.*?<\/select>/si','',$subtext);
		$subtext	=	preg_replace('/<script.*?>.*?<\/script>/si','', $subtext);
		$subtext	=	preg_replace('/<\!--.*?-->/si','', $subtext);
//		$subtext	=	preg_replace('/<br>/si','<br />', $subtext);
		
		if(preg_match_all('/<(img|hr|br|input)(.*?)>/si',$subtext, $matches)){
			for ($i = 0; $i < count($matches[2]); $i++){
				$strIn = $matches[2][$i];
				if ((strlen($strIn) == 0) || ($strIn[strlen($strIn) - 1] != '/')) {
					$newStrIn = $strIn . '/';
					$subtext = str_replace('<'.$matches[1][$i].$strIn.'>', '<'.$matches[1][$i].$newStrIn.'>', $subtext);
				}
			}
		}
		
		preg_match_all('/<(?!\/).*?>/', $subtext, $matches);
		$arr_open = $matches[0];
		preg_match_all('/<\/.*?>/', $subtext, $matches);
		$arr_close = $matches[0];
		for ($i =0; $i<count($arr_open); $i++){
			if (preg_match('/<.*?\/>/i', $arr_open[$i])) {
				$arr_open = $this->delAnArrayElement($i, $arr_open);
				$i--;
			}
		}
		
		for ($i	=	0; $i< count($arr_open); $i++){			
			for ($j = 0; $j<count($arr_close); $j++){
				preg_match('/<(\S+)?\s*.*?>/i', $arr_open[$i], $match);
				preg_match('/<\/(\S+)?>/', $arr_close[$j], $match1);
				if ($match[1]==$match1[1]) {
					$arr_open	=	$this->delAnArrayElement($i, $arr_open);
					$arr_close	=	$this->delAnArrayElement($j, $arr_close);
					$i--;
					break;
				}
			}
			
		}
		
		$tagclose	=	'';
		$tagopen	=	'';
		for ($i = 0 ; $i<count($arr_open); $i++){
			preg_match('/<(\S+)?\s*.*?>/i', $arr_open[$i], $match);
			$tagclose	=	'</'.$match[1].'>'.$tagclose;
			$tagopen	.=	$match[0];			
		}
		
		$this->_subtext	.=	$tagclose;
		
		$regex = '#src\s*=\s*(["\'])(.*?)\1#im';
		
		preg_match_all($regex, $this->_subtext, $matches);		
		
		$images 	= (count($matches)) ? $matches : array();
		$arr_image 	= array();
		if (count($images) > 0 && isset($images[2]))
			$arr_image = $images[2];
		
		if (count($arr_image) > 0) {
			foreach ($arr_image as $image)
				if(file_exists(JPATH_SITE .'/'.$image)) {				
					$this->_subtext 	= str_replace($image, JURI::root().$image, $this->_subtext );
				}
		}
		
		$this->_remaintext = $tagopen. $this->_remaintext;
		
	}
	
	function delAnArrayElement($i,$array){
		$newarray	=	array();
		for ($j=0; $j<count($array); $j++){
			if ($j!=$i) {
				array_push($newarray, $array[$j]);
			}
		}
		return $newarray;
	}
	
	function getIntro(){
		return $this->_subtext;
	}
	
	function getFulltext(){
		return $this->_remaintext;
	}
}