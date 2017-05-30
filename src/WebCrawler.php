<?php

namespace Fetcher;

use Goutte\Client;

class WebCrawler
{
	private $baseUrl;
	private $client;

	public function __construct($baseUrl = "http://www.pcgarage.ro/sisteme-pc-garage/")
	{
		$this->baseUrl = $baseUrl;

		$this->client = new Client();
		$this->client->getClient()->setDefaultOption('verify', 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_TIMEOUT, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_CONNECTTIMEOUT, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYHOST, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYPEER, 0);
		//$this->client->getClient()->setDefaultOption('headers', 'Google Bot: Googlebot/2.1');
		$this->client->setHeader('User-Agent','Google Bot: Googlebot/2.1');
	}

	public function execute()
	{
		$crawler = $this->client->request('GET', $this->baseUrl);

		if (strstr($this->baseUrl, 'altex'))
			$urls = $crawler->filter('html body.catalog-category-view.categorypath-telefoane-tablete-si-gadgets-tablete.category-tablete div.u-container main.lg-u-space-pb-20 div.u-clearfix div.lg-u-float-right.lg-u-size-8of10 div#catalog-products-container ul.Products.Products--grid.Products--4to2 li.Products-item div.Product div.Product-list-right h2.Product-nameHeading a.Product-name.js-ProductClickListener');
		else
			$urls = $crawler->filter('html body.nisp.with-branding.active-branding div#container div.main-content.clearfix div#content-wrapper div#listing-right div.grid-products.clearfix.product-list-container div.product-box-container div.product-box div.pb-specs-container div.pb-name a');

//		$urls = $crawler->filterXPath('/html/body/div[3]/div[2]/div[2]/div[2]/div[3]/div[1]/div/div[2]/div[1]/a');

//		var_dump($urls);

		foreach ($urls as $url)
		{
			var_dump($url->getAttribute('title'));
			var_dump($url->getAttribute('href'));
			var_dump(" ");
		}
	}
}