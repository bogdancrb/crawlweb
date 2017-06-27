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
use Proxy\Html;

class YoutubePlugin extends AbstractPlugin {

	protected $url_pattern = 'youtube.com';
	private $templatesPath;

	public function __construct($templatesPath)
	{
		$this->templatesPath = $templatesPath;
	}

	public function onCompleted(ProxyEvent $event){

		$response = $event['response'];
		$url = $event['request']->getUrl();
		$output = $response->getContent();

		// remove top banner that's full of ads
		$output = Html::remove("#header", $output);

		// do this on all youtube pages
		$output = preg_replace('@masthead-positioner">@', 'masthead-positioner" style="position:static;">', $output, 1);

		// replace future thumbnails with src=
		$output = preg_replace('#<img[^>]*data-thumb=#s','<img alt="Thumbnail" src=', $output);

		$youtube = new \YouTubeDownloader();
		// cannot pass HTML directly because all the links in it are already "proxified"...
		$links = $youtube->getDownloadLinks($url, "mp4 360, mp4");

		if($links){

			$url = current($links)['url'];

			$player = vid_player($url, 640, 390, 'mp4');

			// this div blocks our player controls
			$output = Html::remove("#theater-background", $output);

			// replace youtube player div block with our own
			$output = Html::replace_inner("#player-api", $player, $output);
		}

		$url_form_header = render_template($this->templatesPath . "url_form_header.php");

		$output = $url_form_header.$output;

		$response->setContent($output);
	}
}

?>