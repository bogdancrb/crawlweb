<?php

namespace CrawlerBundle\Controller;

use AppBundle\Entity\Attributes;
use AppBundle\Entity\Content;
use AppBundle\Entity\Template;
use AppBundle\Entity\TemplateElement;
use CrawlerBundle\ServiceContainer;
use DateTime;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class WebCrawler
 * @package CrawlerBundle\Controller
 */
class WebCrawler
{
	private $client;
	private $currentTemplateElementIds;

	/** @var  EntityManager */
	private $entityManager;
	/** @var AbstractManagerRegistry  */
	private $doctrine;

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
		$this->client->setHeader('User-Agent','Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');

		$this->container = ServiceContainer::getContainer();
		$this->doctrine = ServiceContainer::get('doctrine');
		$this->entityManager = $this->doctrine->getEntityManager();
		$this->currentTemplateElementIds = [];
	}

	/**
	 *
	 */
	public function executeCrawler()
	{
		$sleepSecondsConfig = $this->container->getParameter('crawler.sleep_seconds');
		$timeIntervalConfig = $this->container->getParameter('crawler.time_interval');
		$maxProxyIpRequestsUsage = $this->container->getParameter('crawler.max_proxy_ip_requests_usage');
		$proxyIps = $this->container->getParameter('crawler.proxy_ips');

		$crawlContent = $this->doctrine
			->getRepository('AppBundle:Content')
			->retrieveContent($timeIntervalConfig);

		/** @var Content $content */
		foreach ($crawlContent as $content)
		{
			$proxyIp = $this->getProxyIpAndAgent($proxyIps);

			var_dump($proxyIp);

			$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_PROXY, 'http://' . $proxyIp['IP'] . ':80');
			//$this->client->setHeader('User-Agent', $proxyIp['Agent']);

			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			try
			{
				$foundTemplate = false;
				$outdatedTemplate = false;
				$possibleOutdatedTemplate = false;

				var_dump($content->getUrl());

				$crawler = $this->client->request('GET', $content->getUrl());

				if ($content->getAttributes()->count() > 0)
				{
					$attributes = $content->getAttributes();

					/** @var Attributes $attributes */
					foreach ($attributes as $attribute)
					{
						var_dump('[OLD DATA]: ' . $attribute->getTemplateElement()->getName()
							. ': ' . strip_tags(fread($attribute->getValue(), 1000000)));

						$this->entityManager->remove($attribute);
					}
				}

				$templates = $content->getSites()->getTemplate();

				/** @var Template $template */
				foreach ($templates as $template)
				{
					$outdatedTemplate = false;

					$templateElements = $template->getTemplateElement();

					var_dump($template->getName());

					$attribute = null;

					$countSuccessfullyFetchedElements = 0;

					/** @var TemplateElement $templateElement */
					foreach ($templateElements as $templateElement)
					{
						$extractedValues = $crawler->filter($templateElement->getCssPath());

						$countSuccessfullyFetchedElements += count($extractedValues);

						if (count($extractedValues) > 0)
						{
							/** @var \DOMElement $value */
							foreach ($extractedValues as $value)
							{
								$foundTemplate = true;

								$attributeValue = trim($value->nodeValue);
								$url = strstr($value->getAttribute('href'), 'javascript') == false
									? $value->getAttribute('href')
									: null;

								if (!empty($attributeValue) && !$templateElement->getIgnoreAttributeValue())
								{
									var_dump('[NEW DATA]: ' . $templateElement->getName() . ': ' . $attributeValue);

									$attribute = new Attributes();
									$attribute->setValue($attributeValue)
										->setContent($content)
										->setTemplateElement($templateElement);
									$this->entityManager->persist($attribute);
								}

								if (!empty($url))
								{
									if (strstr($url, 'http://') == false)
									{
										$url = $content->getSites()->getMainUrl() . $url;
									}

									var_dump('HREF FOUND: ' . $url);

									/** @var Content $contentUrlFound */
									$contentUrlFound = $this->doctrine
										->getRepository('AppBundle:Content')
										->findOneByUrl($url);

									if (count($contentUrlFound) <= 0)
									{
										$newContent = new Content();
										$newContent->setUrl($url)
											->setSites($content->getSites());
										$this->entityManager->persist($newContent);
									}
								}
							}
						}
						else
						{
							if ($countSuccessfullyFetchedElements > 2)
							{
								$possibleOutdatedTemplate = true;
								$outdatedTemplate = false;
							}
							else
							{
								$outdatedTemplate = true;
							}
						}
					}

					if ($foundTemplate)
					{
						break;
					}
					else if (isset($attribute))
					{
						$this->entityManager->remove($attribute);
						$attribute = null;
					}
				}

				if (!$foundTemplate)
				{
					// TODO log error and mail
					// TODO mark content as failed in DB

					throw new \Exception('no template found');
				}
				else if ($outdatedTemplate)
				{
					// TODO log error and mail
					// TODO mark content as outdated in DB
					throw new \Exception('template outdated, please update');
				}
				else if ($possibleOutdatedTemplate)
				{
					var_dump('template is possible to be outdated, please check the template settings');
				}

				$lastAccessedDate = new DateTime();
				$lastAccessedDate->format("Y-m-d H:i:s");

				$content->setLastAccessed($lastAccessedDate);

				$this->entityManager->persist($content);
				$this->entityManager->flush();

				$connection->commit();
			}
			catch (\Exception $e)
			{
				var_dump($e->getMessage());

				$connection->rollBack();
			}

			if (!empty($sleepSecondsConfig))
			{
				var_dump('Process halted for '. $sleepSecondsConfig/60 . ' minutes');

				// Halt the crawling process, so that we do not get banned from the website :)
				sleep($sleepSecondsConfig);
			}
		}
	}

	private function getProxyIpAndAgent($proxyIps = [])
	{
		srand();
		$randIndex = rand(1, sizeof($proxyIps) - 1);

		return $proxyIps[$randIndex];
	}
}