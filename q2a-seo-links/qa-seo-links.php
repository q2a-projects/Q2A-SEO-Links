<?php
/*              
    Plugin Name: SEO Links
    Plugin URI: 
    Plugin Update Check URI:  http://QA-Themes.com
    Plugin Description: SEO Links for Question2Answer
    Plugin Version: 1
    Plugin Date: 2012-7-3
    Plugin Author: QA-Themes
    Plugin Author URI:
    Plugin License: GPLv2                           
    Plugin Minimum Question2Answer Version: 1.5
*/

		
	function qa_sanitize_html($html, $linksnewwindow=false, $storage=false)
/*
	Return $html after ensuring it is safe, i.e. removing Javascripts and the like - uses htmLawed library
	Links open in a new window if $linksnewwindow is true. Set $storage to true if sanitization is for
	storing in the database, rather than immediate display to user - some think this should be less strict.
*/
	{
		$safe=qa_sanitize_html_base($html, $linksnewwindow, $storage);
		$rel_types = array(1 => 'Nofollow', 2 => 'External', 3 => 'Nofollow External', 4 => '');
		$links_list=json_decode(qa_opt('seo_links_list'));
		$dom = new DOMDocument;
		$encod  = mb_detect_encoding($safe);
		$dom->loadHTML(mb_convert_encoding($safe, 'HTML-ENTITIES', $encod));
		$links = $dom->getElementsByTagName('a');
		// apply rel change to list of links
		
		foreach ($links as $link) {
			foreach($links_list as $key=>$value)
			{	
				$site_url=parse_url($value->host);
				if(isset($site_url['host']))
					$host= $site_url['host'];
				else
					$host= $site_url['path'];
				
				// add rel attribute according to host address
				if( $host && $link->getAttribute('href') )
					if (strpos( strtolower($link->getAttribute('href')) , strtolower($host) ))
						$link->setAttribute('rel', $rel_types[$value->rel]);
			}
		}
		/* ~~ old method
			// remove all rel attributes
			$safe= preg_replace('/(<[^>]+) rel=".*?"/i', '$1',$safe); 
			// add nofollow to them all
			$safe = preg_replace('/<a /', '<a rel="nofollow" ', $safe);
			foreach($links_list as $key=>$value)
			{
				// add rel attribute according to host address
				$search = '#<a rel="nofollow" href="'. $value->host .'("|/[^"]*")#i';
				$replace = '<a rel="'. $rel_types[$value->rel] .'" href="'. $value->host .'\1'; //
				$safe= preg_replace( $search, $replace, $safe );
			}
		*/
		if( qa_opt('seo_links_internal_links') )
		{
			foreach ($links as $link) {
				$site_url=parse_url(qa_opt('site_url'));
				if (strpos( strtolower($link->getAttribute('href')) , strtolower($site_url['host']) ))
					$link->setAttribute('rel', 'dofollow');
			}
			$safe = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));
		
			/* ~~ old method
			$search = '#<a rel="nofollow" href="' . qa_opt('site_url') . '("|/[^"]*")#i';
			$replace = '<a href="' . qa_opt('site_url') . '\1';
			$safe = preg_replace( $search, $replace, $safe );
			*/
		}
		return $safe;
	}

	function qa_html_convert_urls($html, $newwindow=false)
/*
	Return $html with any URLs converted into links (with nofollow and in a new window if $newwindow)
	URL regular expressions can get crazy: http://internet.ls-la.net/folklore/url-regexpr.html
	So this is something quick and dirty that should do the trick in most cases
*/
	{
		$host=getHost($html);
        $rel_types = array(1 => 'Nofollow', 2 => 'External', 3 => 'Nofollow External', 4 => '');
		$links_list=json_decode(qa_opt('seo_links_list'));
		$rel='nofollow';
		foreach($links_list as $key=>$value)
			if (getHost($value->host) == $host)
				{$rel=$rel_types[$value->rel];}
		if( qa_opt('seo_links_internal_links') ){
			$host = getHost($html);
			$site_url=parse_url(qa_opt('site_url'));
			if(isset($site_url['host']))
				$site_host= $site_url['host'];
			else
				$site_host= $site_url['path'];
			if($host==$site_host)
				$rel = 'dofollow';
		}
		return substr(preg_replace('/([^A-Za-z0-9])((http|https|ftp):\/\/([^\s&<>"\'\.])+\.([^\s&<>"\']|&amp;)+)/i', '\1<a href="\2" rel="'. $rel .'"'.($newwindow ? ' target="_blank"' : '').'>\2</a>', ' '.$html.' '), 1, -1);
	}
	function getHost($Address) {
	   $parseUrl = parse_url(trim($Address));
	   return @trim($parseUrl[host] ? $parseUrl[host] : array_shift(explode('/', $parseUrl[path], 2)));
	} 