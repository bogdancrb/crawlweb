<?php

/**
 * PHP-Proxy is a web-based proxy script designed to be fast, easy to customize,
 * and be able to provide support for complex sites such as YouTube and Facebook.
 * There have been many other proxy software scripts in the past, such as Glype, PHProxy,
 * CGIProxy, Surrogafier, ASProxy, Zelune... but all have either perished permanently or the creator
 * has stopped updating them. This proxy script is intended to replace all others.
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Source: https://github.com/Athlon1600/php-proxy-app
 *
 * Created by: https://www.php-proxy.com/
 * Modified by: Bogdan Corbeanu
 */

use Proxy\Plugin\AbstractPlugin;
use Proxy\Event\ProxyEvent;

class UrlFormPlugin extends AbstractPlugin
{
	private $templatesPath;

	public function __construct($templatesPath)
	{
		$this->templatesPath = $templatesPath;
	}

	public function onCompleted(ProxyEvent $event){

		$request = $event['request'];
		$response = $event['response'];
		
		$url = $request->getUri();
		
		// we attach url_form only if this is a html response
		if(!is_html($response->headers->get('content-type'))){
			return;
		}
		
		// this path would be relative to index.php that included it?
		$url_form = render_template($this->templatesPath . "url_form.php", array(
			'url' => $url
		));
		$url_form_header = render_template($this->templatesPath . "url_form_header.php");
		
		$output = $response->getContent();
		
		// does the html page contain <body> tag, if so insert our form right after <body> tag starts
		$output = preg_replace('@<head.*?>@is', '$0'.PHP_EOL.$url_form_header, $output, 1, $countHead);
		$output = preg_replace('@<body.*?>@is', '$0'.PHP_EOL.$url_form, $output, 1, $count);
		
		// <body> tag was not found, just put the form at the top of the page
		if($count == 0){
			$output = $url_form.$output;
		}
		
		$response->setContent($output);
	}
}

?>