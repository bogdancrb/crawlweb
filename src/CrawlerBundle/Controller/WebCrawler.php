<?php

namespace CrawlerBundle\Controller;

use AppBundle\Entity\Attributes;
use AppBundle\Entity\Content;
use AppBundle\Entity\Sites;
use CrawlerBundle\Command\WebCrawlerCommand;
use CrawlerBundle\ServiceContainer;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Tests\Service;

/**
 * Class WebCrawler
 * @package CrawlerBundle\Controller
 */
class WebCrawler
{
	private $baseUrl;
	private $client;

	/** @var  EntityManager */
	private $entityManager;

	/** @var Container  */
	private $container;

	/**
	 * WebCrawler constructor.
	 */
	public function __construct()
	{
		$this->client = new Client();
		$this->client->getClient()->setDefaultOption('verify', 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_TIMEOUT, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_CONNECTTIMEOUT, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYHOST, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYPEER, 0);
		$this->client->setHeader('User-Agent','Google Bot: Googlebot/2.1');

		$this->container = ServiceContainer::getContainer();
		$this->entityManager = ServiceContainer::get('doctrine')->getEntityManager();
	}

	/**
	 *
	 */
	public function executeCrawler()
	{
		$crawlContent = $this->entityManager->getRepository('AppBundle:Content')->retrieveContent(
			$this->container->getParameter('crawler.time_interval')
		);

		/** @var Content $content */
		foreach ($crawlContent as $content)
		{
			var_dump($content->getUrl());
			$crawler = $this->client->request('GET', $content->getUrl());

			if ($content->getAttributes()->count() > 0)
			{
//				/** @var Attributes $attribute */
//				foreach ($content->getAttributes() as $attribute)
//				{
//					var_dump('[OLD DATA]: ' . $attribute->getTemplateElement()->getName()
//						. ': ' . strip_tags(fread($attribute->getValue(), 10000)));
//					var_dump($attribute->getTemplateElement()->getCssPath());
//
//					$extractedValues = $crawler->filter($attribute->getTemplateElement()->getCssPath());
//
//					/** @var \DOMElement $value */
//					foreach ($extractedValues as $value)
//					{
//						var_dump('[NEW DATA]: ' . $attribute->getTemplateElement()->getName() . ': ' . $value->nodeValue);
//
//						if (!empty($value->getAttribute('href')))
//						{
//							var_dump($value->getAttribute('href'));
//						}
//					}
//					var_dump("######################################");
//				}
			}
			else
			{
				var_dump($content->getSites()->getTemplate()[0]->getName());
				var_dump($content->getSites()->getTemplate()[0]->getTemplateElement()[0]->getName());

//				$crawlSites = $this->entityManager->getRepository('AppBundle:Sites')->findOneById($content->getSites()->getId());
//
//				/** @var Sites $site */
//				foreach ($crawlSites as $site)
//				{
//					var_dump($site->getTemplate()->getTemplateElement()->getName());
//				}
			}
		}

//		$this->baseUrl = "http://www.pcgarage.ro/mouse-gaming/marvo/m205-red/";
//		$crawler = $this->client->request('GET', $this->baseUrl);
//
//		$urls = $crawler->filter('html body.nisp.with-branding.active-branding div#container div.main-content.clearfix h1.p-name');
//
//		/** @var \DOMElement $url */
//		foreach ($urls as $url)
//		{
////			var_dump($url->getNodePath());
//			var_dump($url->nodeValue);
//
////			if (!empty($url->getAttribute('title')))
////			{
////				var_dump($url->getAttribute('title'));
////			}
////
//			if (!empty($url->getAttribute('href')))
//			{
//				var_dump($url->getAttribute('href'));
//			}
//		}
	}
}